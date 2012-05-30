<?php
error_reporting( E_ALL );

include("LIB_db_functions.php");

db_connect();

$strWHERE=" WHERE NOT tblPages.bolExclude";

echo "<" . "?";
echo "xml version=\"1.0\" encoding=\"UTF-8\"" . "?" . ">\n";
?>
<graphml xmlns="http://graphml.graphdrawing.org/xmlns"  
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://graphml.graphdrawing.org/xmlns 
        http://graphml.graphdrawing.org/xmlns/1.0/graphml.xsd">
  <key id="label" for="node" attr.name="label" attr.type="string" />
  <key id="page_id" for="node" attr.name="page_id" attr.type="int"/>
  <key id="numPages" for="node" attr.name="numPages" attr.type="int" />
  <key id="weight" for="edge" attr.name="weight" attr.type="int" />
  <graph id="G" edgedefault="directed">
<?php
//  <key id="day" for="node" attr.name="day" attr.type="int" />
$strSQL="SELECT MIN(iPageID) as iPageID, strDomain, count(*) as size" .
	" FROM tblPages " . $strWHERE . " AND bolCentral=1 AND NOT strDomain IS NULL GROUP BY strDomain";
$result = mysql_query($strSQL);
$counter=1;
$arrNodes=array();
while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {
	#echo "$counter \"" . $record["strURL"] . "\"\r\n";
	$iPageID=$record["iPageID"];
	echo "\t <node id=\"" . $record["strDomain"] ."\">\n";
	echo "\t\t <data key=\"label\">". 
		str_replace(array("&","'","\"","<",">"),
			array("&amp;","&apos;","&quot;","&lt;","&gt;"),
			$record["strDomain"]) . "</data>\n";
	echo "\t\t <data key=\"page_id\">" . $record["iPageID"] . "</data>\n";
	echo "\t\t <data key=\"numPages\">" . $record["size"] . "</data>\n";
#	echo "\t\t <data key=\"lang\">" . (strpos($record["strURL"],"en")===false?"cy":"en"). "</data>\n";
	//echo "\t\t <data key=\"day\">" . $record["dtDays"] . "</data>\n";
	echo "    </node>\n";
}
mysql_free_result($result);

#echo "*Arcs\r\n";
#$strSQL="SELECT fkParentID, fkChildID, iNumberTimes FROM (tblLinks INNER JOIN tblPages as t ON tblLinks.fkChildID=t.iPageID) INNER JOIN tblPages ON tblLinks.fkParentID=tblPages.iPageID  WHERE NOT tblPages.bolExclude AND NOT t.bolExclude";

$strSQL="SELECT tblPages.strDomain as fkParentID, t.strDomain as fkChildID, SUM(iNumberTimes) as iNumberTimes FROM (tblLinks INNER JOIN tblPages as t ON tblLinks.fkChildID=t.iPageID) INNER JOIN tblPages ON tblLinks.fkParentID=tblPages.iPageID  WHERE tblPages.bolCentral=1 AND t.bolCentral=1 AND NOT tblPages.bolExclude AND NOT t.bolExclude  AND NOT tblPages.strDomain IS NULL AND NOT t.strDomain IS NULL GROUP BY t.strDomain, tblPages.strDomain";

$result = mysql_query($strSQL);
$counter=1;
while ($record = mysql_fetch_array($result, MYSQL_ASSOC)) {
	#echo ($arrNodes[$record["fkParentID"]][0]) . " " . 
#		($arrNodes[$record["fkChildID"]][0]) .
#		" " . ($record["iNumberTimes"]+1). "\r\n";
	echo "\t<edge id=\"e$counter\" source=\"" . $record["fkParentID"] . "\"" . 
		" target=\"" . $record["fkChildID"] . "\">\n";
	echo "\t\t <data key=\"weight\">" . $record["iNumberTimes"] . "</data>\n";
	echo "\t</edge>\n";
	$counter=$counter+1;
}
mysql_free_result($result);

#echo "*Edges\r\n";
echo "  </graph>\n";
echo "</graphml>\n";

db_close();
?>
