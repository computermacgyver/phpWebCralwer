<?php
error_reporting( E_ALL );
date_default_timezone_set('Europe/London');

include_once("CONFIG_db.php");			//Include configuration (do this first)

include_once("LIB_parse.php");			// parse library
include_once("LIB_exclusion_list.php");		// list of excluded keywords
include_once("LIB_simple_spider.php");		// spider routines used by this app.
include_once("LIB_db_functions.php");
include_once("LIB_encoding.php");

#output dir
exec("mkdir -p files/html");
db_connect();

$strSQL="SELECT iPageID,strHTML FROM tblPages WHERE bolHarvested";
$statement = $GLOBALS["db"]->prepare($strSQL);
$result = $statement->execute();

#$strSQL="SELECT strHTML FROM tblPages WHERE iPageID=?"
#$htmlStmt=$GLOBALS["db"]->prepare($strSQL);

while (null!=($row = $statement->fetch(PDO::FETCH_ASSOC))) {
	$id=$row['iPageID'];
	$html=$row['strHTML'];
	
	#$htmlResult=$htmlStmt->execute($id);
	#$html=...
	
	$fh=fopen("files/html/$id.html","w");
	fwrite($fh,$html);
	fclose($fh);
}
$statement->closeCursor();

?>
