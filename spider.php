<?php
error_reporting( E_ALL );
date_default_timezone_set('Europe/London');

ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);

# Initialization

include_once("CONFIG_db.php");			//Include configuration (do this first)

include_once("LIB_http.php");			// http library
include_once("LIB_parse.php");			// parse library
include_once("LIB_resolve_addresses.php");	// address resolution library
include_once("LIB_exclusion_list.php");		// list of excluded keywords
include_once("LIB_simple_spider.php");		// spider routines used by this app.
include_once("LIB_db_functions.php");
include_once("LIB_encoding.php");


set_time_limit(0);				// Don't let PHP timeout

db_connect();

if ($first_run==true) {
	//Before starting, check the domains fields of the database and fill in any missing entries
	//Also fill in missing 
	$strSQL="SELECT * FROM tblPages WHERE strDomain IS NULL OR strDomain='' OR strCleanURL IS NULL  OR strCleanURL=''";
	$statement = $GLOBALS["db"]->prepare($strSQL);
	$result = $statement->execute();
	while (null!=($row = $statement->fetch(PDO::FETCH_ASSOC))) {
		$url=$row['strURL'];
		if ($row['strDomain']!=null && $row['strDomain']!='') {
			$domain = false;//$row['strDomain'];
		} else {
			$domain = get_domain_part($url,$SAME_DOMAIN_FETCH_LEVEL);
		}
	
		if ($row['strCleanURL']!=null && $row['strCleanURL']!='') {
			$cleanURL = false;//$row['strCleanURL'];
		} else {
			$cleanURL = clean_url($url);
		}
	
		$pageID=$row["iPageID"];
		$strSQL="UPDATE tblPages SET ";
	
		if ($domain===false && $cleanURL===false) {
			//Should never get here. If we do something strange with db config
			die("Assert failes: neither domain nor clean url in need of updating");
		}
		if ($domain!==false) $strSQL.="strDomain='$domain' ";
		if ($domain!==false && $cleanURL!==false) $strSQL.=", ";
		if ($cleanURL!==false) $strSQL.="strCleanURL='$cleanURL' ";
		$strSQL.=" WHERE iPageID=$pageID";
		db_run_query($strSQL);
	}

	//die("Preparation done.\n");
}

$seed = db_get_next_to_harvest();
while ($seed!=NULL) {

	$SEED_URL        = $seed["strURL"];	// First URL spider downloads
	#$START_PENETRATION = seed['iLevel'];

	# Get links from $SEED_URL
	echo "Harvesting Seed " . $seed["iPageID"] . "; URL   " . $seed["strURL"] . "\n"; 

	#$link_array = array();

	# Get page base for $url
	$page_base = get_base_page_address($seed["strURL"]);

	# Download webpage
	//global $DELAY;
	//sleep($DELAY);          

	$strHTML="";
	if ($seed["strHTML"]==NULL) {
		//die("No page: "  . $seed["iPageID"]);	
		echo "Downloading....\n";
		try {
			$strURL = $seed["strURL"];
			if (exclude_link($seed["strURL"])) throw new Exception("Page in excluded list: $strURL\n");
			$downloaded_page = http_get_withheader($seed["strURL"], "");
			# Catch fetch errors, oversize files, non-text extensions etc.
			if ($downloaded_page['ERROR'] !== '') throw new Exception("Error fetching page: {$downloaded_page['ERROR']}");
			$content_type=$downloaded_page['STATUS']['content_type'];
			$strStatus=$downloaded_page['STATUS'];
			$code=$strStatus["http_code"];
			if (($code!=200 && $code!=206) || strpos(strtolower($content_type),"text")===false) {
				//200 is OK, 206 is partial content
				print "Skipping....http_code is $code content_type is $content_type\n";
				db_marked_harvested($seed);
				$seed = db_get_next_to_harvest();
				continue;
			}
			$strHTML = $downloaded_page['FILE'];
			$strHTML=db_store_html($seed,$strHTML,$strStatus["url"]);
			if ($strHTML==null) {
				throw new Exception("Page could not be stored.");
			}
		} catch (Exception $e) {
				echo "Exeception caught: $e . Skipping page\n";
				db_marked_harvested($seed);
				$seed = db_get_next_to_harvest();
				continue;
	 	}
	} else {
		$strHTML = $seed['strHTML'];
	}

	/*Get headers just this time for fun, please*/
	/*	if ($seed["strHeader"]==NULL) {
			$downloaded_page = http_get_withheader($seed["strURL"], "");
			$strHeader = substr($downloaded_page['FILE'],0,strpos($downloaded_page['FILE'],"<"));
			$strSQL = "UPDATE tblPages SET " .
			"strHeader='" .mysql_real_escape_string($strHeader) . "' WHERE iPageID=" . $seed["iPageID"];
			db_run_query($strSQL);
		}*/
	/*End insert*/

	echo "Parsing....\n";
	$anchor_tags = parse_array($strHTML, "<a ", "</a>", EXCL);
	# Put http attributes for each tag into an array
	$sqlQuery="INSERT INTO tblLinks(fkParentID,fkChildID,fkQueryID,iNumberTimes) VALUES ";
	$outputExists=false;
	for($xx=0; $xx<count($anchor_tags); $xx++) {
		//print "tags : ". $anchor_tags[$xx]. "\n";
		$href = get_attribute($anchor_tags[$xx], "href");
		//print "href = $href , page_base = $page_base \n";
		if ($href===false) continue;
		$resolved_address = resolve_address($href, $page_base);
		//echo "have address: $resolved_address\n";
		if (!exclude_link($resolved_address)) {
			try {
				$out="";
				if ($MAX_PENETRATION==0)//crawl only links in db
					$out=db_store_link_internal_only($seed,$resolved_address);
				else //grow crawl list (possibly in conjuction with white list)
					$out=db_store_link($seed,$resolved_address);

				if ($out!="") {
					$outputExists=true;
					$sqlQuery+=$out.",";
				}
			} catch(Exception $e) {
				echo "***ERROR***\n";
				echo "Couldn't store: $resolved_address\n";
				echo "While harvesting: $SEED_URL\n";
				break;//ignore any further links (to prevent multiple error messages for one page)
			}
		}
		#echo "Harvested: ".$resolved_address." \n";
	}
	
	if ($outputExists) {
		$sqlQuery=substr($sqlQuery,0,strlen($sqlQuery)-1);//trim last char
		db_run_query($sqlQuery);//already in try-catch in function
	}

	db_marked_harvested($seed);
	
	/*Safe zone:
	This is where halting of the crawler should occur if at all
	Check DB to see if flag has been left to stop*/
	$stop=false;
	$strSQL="SELECT strValue FROM tblConfig WHERE strName='CrawlerStatus'";
	$result = db_run_select($strSQL,null,true);
	if ($result=="STOP") {
		echo "***Receivend command to STOP\n. Stopping now; crawl is incomplete.\n";
		mail($operator_email, "Crawl Stopped", "Bot stopped via DB Stop signal: " . date('Y-m-d H:i:s') ."\n","FROM: " . $operator_email);
		$stop=true;
		break;
	}
	$seed = db_get_next_to_harvest();
}
db_close();
echo "Done.\n";

if (!$stop) {
	mail($operator_email, "Crawl Success", "Bot has  finished: " . date('Y-m-d H:i:s') ."\n","FROM: " . $operator_email);
}
?>
