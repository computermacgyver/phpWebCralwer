<?php

#Database Functions

include("CONFIG_db.php");
# Above file defines the following variables: 
#
# $db_host = "localhost";
# $db_username = "jrandom";
# $db_password = "ASDF!!1!one1";
# $db_name = "somedb";
## $operator_email is used to mail the user on script completion
# $operator_email = "j.random@example.com";
# 

function /*public*/ db_connect() {
	global $db_host, $db_username, $db_password, $db_name;
	$GLOBALS["db"] = mysql_connect($db_host, $db_username, $db_password) or
	    die("Could not connect: " . mysql_error());
	mysql_select_db($db_name,$GLOBALS["db"]);
}

#db_get_next_spider_target();
#db_store_html($seed,$downloaded_page['FILE'])
#db_store_link($seed,$resolved_address;

function /*private*/ db_run_select($strSQL,$returnVal=false) {
	global $db_name;
	mysql_select_db($db_name);
	#echo $strSQL . "\n";	
	try {
		$result = mysql_query($strSQL,$GLOBALS["db"]) or die('Query failed: ' . mysql_error());
	}
	catch(Exception $e)
	{
	  echo "$strSQL\n" . $e->getMessage() ."\n"  . mysql_error();
	}
	
	if (!$returnVal) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$output = $row;
	} else  {
		$row = mysql_fetch_array($result, MYSQL_NUM);
		if ($row==Null)
			$output = Null;
		else 
			$output = $row[0];
	}
	mysql_free_result($result);
	return $output;
}

function /*private*/ db_run_query($strSQL) {
	//mysql_select_db("DBNAME");
	#echo $strSQL . "\n";
	try {
		$result = mysql_query($strSQL,$GLOBALS["db"]); #or die('Query failed: ' . mysql_error());
	}
	catch(Exception $e)
	{
	  echo "$strSQL\n" . $e->getMessage() ."\n"  . mysql_error();
	}
	if (!$result) {
		print "ERROR: Query returned false:  $strSQL\n";
		fprintf(STDERR,"ERROR: Query returned false.\n" . mysql_error() . "\n");
		throw new Exception("ERROR: Query returned false:  \n$strSQL\n\n\n");
		#die("Query returned false:  $strSQL\n");
	}
}

function /*public*/ db_close() {
	#mysql_close();
}

function db_get_next_spider_target() {
	#return array("iPageID" => 1,
	#	     "strURL" => "http://www.schrenk.com/",
	#	     "iLevel" => 0,
	#	     "fkQueryID" => 1,
	#	     "strHTML" => NULL
	#	);
	global $MAX_PENETRATION;
	#echo "Getting next target from database...\n";
	$strSQL = "SELECT * from tblPages WHERE NOT bolHarvested AND iLevel < " . $MAX_PENETRATION . 
		" AND strURL LIKE '%direct.gov.uk%' LIMIT 1";
	return db_run_select($strSQL);
}

function db_get_next_to_download() {
	#return array("iPageID" => 1,
	#	     "strURL" => "http://www.schrenk.com/",
	#	     "iLevel" => 0,
	#	     "fkQueryID" => 1,
	#	     "strHTML" => NULL
	#	);
	#echo "Getting next target from database...\n";
	$strSQL = "SELECT * from tblPages WHERE strHTML IS NULL LIMIT 1";
	return db_run_select($strSQL);
}

function db_get_next_to_process() {
	#return array("iPageID" => 1,
	#	     "strURL" => "http://www.schrenk.com/",
	#	     "iLevel" => 0,
	#	     "fkQueryID" => 1,
	#	     "strHTML" => NULL
	#	);
	#echo "Getting next target from database...\n";
	$strSQL = "SELECT * from tblPages WHERE bolProcessed=0 AND bolExclude=0 LIMIT 1";
	return db_run_select($strSQL);
}

function db_marked_harvested($seed) {
	$strSQL = "UPDATE tblPages SET bolHarvested=1 WHERE iPageID=" . $seed["iPageID"];
	db_run_query($strSQL);
}

function db_marked_processed($seed) {
	$strSQL = "UPDATE tblPages SET bolProcessed=1 WHERE iPageID=" . $seed["iPageID"];
	db_run_query($strSQL);
}

function db_store_html($seed,$strHTML,$strURL) {/*!! TODO: Encoding Issues, pull date from header*/
	#store strHTML and current datetime
	#echo "start db_store_html(....)\n";
	#echo "$strHTML";
	//ENCODING!!!!!!!!!!
	try {
		$enc = get_encoding($strHTML,true);
		if (strlen($enc)>0) 
			$enc = $enc . ",";
		$enc = $enc . "x-euc-jp,EUC-JP,JIS,SJIS,iso-8859-1,ASCII";
		#mb_detect_encoding($strHTML,$enc) 
		$strHTML = mb_convert_encoding($strHTML, "UTF-8", $enc);
		//$strHTML = html_entity_decode( $strHTML, ENT_QUOTES, "UTF-8" );
		//$strHTML = remove($strHTML,"<script","</script>");//strip JavaScript
		//$strHTML=strip_tags($strHTML);//Remove html tags
		$strHTML = strtolower_utf8($strHTML);
		$return = $strHTML;
		try {
			$strHTML = mysql_real_escape_string($strHTML);
			if ($strURL && clean_url($strURL)!=clean_url($seed["strURL"])) {
				$domain=mysql_real_escape_string(get_domain($strURL));
				$cleanURL=mysql_real_escape_string(clean_url($strURL));
				$url=mysql_real_escape_string($strURL);
				$strSQL = "UPDATE tblPages SET strURL='$url', "
					. " strCleanURL='$cleanURL', strHTML='" . $strHTML . "' WHERE iPageID=" . $seed["iPageID"];	
			} else {
				$strSQL = "UPDATE tblPages SET " .
					"strHTML='" . $strHTML . "' WHERE iPageID=" . $seed["iPageID"];
			}
			db_run_query($strSQL);
			#echo "end db_store_html(...)\n";
			#'" . date("YmdHi___NEED SECONDS__") . "'"
			#e.g. '20100131000000' 2010-01-31 00:00:00
			#CurDate(), CurTime, Now()
		} catch (Exception $e) {
			//ignore
		}
		return $return;
	} catch (Exception $e) {
		print "Exeception $e\n";
		return null;
	}
}


function db_store_link($seed,$link) {
	echo "db_store_link($seed,$link)\n";
	#check if in tblPages
	#if not, store
	#get unique id, tblPages.iPageID
	#get if there is link from seed[iPageID] to $resolved address
	#if so increment link count
	#if not, insert new record with link count = 0

	#echo "start......db_store_link(...)\n";
	#echo "link is: $link\n";
	$link=html_entity_decode($link);
	$cleanUrl=clean_url($link);
	$cleanUrl=mysql_real_escape_string($cleanUrl);
	$link=mysql_real_escape_string($link);
	$strSQL="SELECT iPageID FROM tblPages WHERE strCleanURL='" . $cleanUrl . "'";
	$page_id = db_run_select($strSQL,true);
	if ($page_id==NULL) {
		$strSQL="INSERT INTO tblPages(fkQueryID,strURL,strCleanURL,iLevel) VALUES (" .
			$seed["fkQueryID"] . ",'" . $link . "','" .$cleanUrl . "'," .
			($seed["iLevel"]+1) .")";
		db_run_query($strSQL);
		$strSQL="SELECT iPageID FROM tblPages WHERE strCleanURL='" . $cleanUrl . "'";
		$page_id = db_run_select($strSQL,true);
	} else {
		//check current level and give shorter level if possible?
	}

	$strSQL="SELECT iLinkID FROM tblLinks " .
			"WHERE fkParentID=" . $seed["iPageID"] . " AND fkChildID=" . $page_id;
	$link_id = db_run_select($strSQL,true);
	if ($link_id==NULL) {
		$strSQL="INSERT INTO tblLinks(fkParentID,fkChildID,fkQueryID,iNumberTimes) VALUES (" .
			$seed["iPageID"] . "," . $page_id . "," . $seed["fkQueryID"] . ",1)";
		db_run_query($strSQL);
	} else {
		//update
		$strSQL="UPDATE tblLinks SET iNumberTimes=iNumberTimes+1 WHERE iLinkID=" . $link_id;
		db_run_query($strSQL);	
	}

	return;
}


function db_store_link_internal_only($seed,$link) {
	//echo "db_store_link_internal_only($seed,$link)\n";
	#check if in tblPages
	#if not, store
	#get unique id, tblPages.iPageID
	#get if there is link from seed[iPageID] to $resolved address
	#if so increment link count
	#if not, insert new record with link count = 0

	#echo "start......db_store_link(...)\n";
	#echo "link is: $link\n";
	$link=html_entity_decode($link);
	$link=clean_url($link);
	$link=mysql_real_escape_string($link);
	//$strSQL="SELECT iPageID FROM tblPages WHERE bolExclude=0 AND strCleanURL='" . $link . "'";
	$strSQL="SELECT iPageID FROM tblPages WHERE strCleanURL='$link' ORDER BY bolExclude,iPageID LIMIT 1";
	$page_id = db_run_select($strSQL,true);
	if ($page_id==NULL) {
		/*$strSQL="SELECT iPageID FROM tblPages WHERE strCleanURL='" . $link . "'";
		$page_id = db_run_select($strSQL,true);
		if ($page_id==NULL) {
			return; //skip  if not in set
		} else {
			print "[W] Warning: Link to excluded page found: " .
				$seed["iPageID"] ."->$page_id\n";
		}*/
		return;
	} else {
		//check current level and give shorter level if possible?
	}

	$strSQL="SELECT iLinkID FROM tblLinks " .
			"WHERE fkParentID=" . $seed["iPageID"] . " AND fkChildID=" . $page_id;
	$link_id = db_run_select($strSQL,true);
	if ($link_id==NULL) {
		$strSQL="INSERT INTO tblLinks(fkParentID,fkChildID,fkQueryID,iNumberTimes,iLevel) VALUES (" .
			$seed["iPageID"] . "," . $page_id . "," . $seed["fkQueryID"] . ",1,1)";
		db_run_query($strSQL);
	} else {
		//update
		#$strSQL="UPDATE tblLinks SET iNumberTimes=iNumberTimes+1 WHERE iLinkID=" . $link_id;
		#db_run_query($strSQL);	
	}

	return;
}
