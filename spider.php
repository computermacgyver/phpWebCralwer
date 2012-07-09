<?php
error_reporting( E_ALL );
date_default_timezone_set('Europe/London');

# Initialization
include("LIB_http.php");                        // http library
include("LIB_parse.php");                       // parse library
include("LIB_resolve_addresses.php");           // address resolution library
include("LIB_exclusion_list.php");              // list of excluded keywords
include("LIB_simple_spider.php");               // spider routines used by this app.
include("LIB_db_functions.php");
include("LIB_encoding.php");
include("CONFIG_db.php");

set_time_limit(0);                           // Don't let PHP timeout

$MAX_PENETRATION = 5;                           // Set spider penetration depth
$FETCH_DELAY     = 1;                           // Wait one second between page fetches
$ALLOW_OFFSITE   = true;                        // Don't allow spider to roam from the SEED_URL's domain
$ONLY_OFFSITE   = false;                        // Only include URL's to remote domains
#$spider_array = array();

db_connect();

$seed = db_get_next_to_process();
while ($seed!=Null) {

	$SEED_URL        = $seed["strURL"];    // First URL spider downloads
	#$START_PENETRATION = seed['iLevel'];

	# Get links from $SEED_URL
	echo "(forward) Harvesting Seed " . $seed["iPageID"] . "; URL   " . $seed["strURL"] . "\n"; 

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
			$content_type=$downloaded_page['STATUS']['content_type'];
			$strStatus=$downloaded_page['STATUS'];
			$code=$strStatus["http_code"];
			if ($code!=200 || strpos(strtolower($content_type),"text")===false) {
				print "Skipping....http_code is $code content_type is $content_type\n";
				db_marked_processed($seed);
				$seed = db_get_next_to_process();
				continue;
			}
			$strHTML = $downloaded_page['FILE'];
			$strHTML=db_store_html($seed,$strHTML,$strStatus["url"]);
			if ($strHTML==null) {
				throw new Exception("Page could not be stored.");
			}
		} catch (Exception $e) {
				echo "Exeception caught: $e . Skipping page\n";
				db_marked_processed($seed);
				$seed = db_get_next_to_process();
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


	/*INSERT Encoding*/
	//Moved to store_html function
	/*END Encoding*/
	echo "Parsing....\n";
	$anchor_tags = parse_array($strHTML, "<a", "</a>", EXCL);
	# Put http attributes for each tag into an array
	for($xx=0; $xx<count($anchor_tags); $xx++) {
		$href = get_attribute($anchor_tags[$xx], "href");
		$resolved_address = resolve_address($href, $page_base);
		#echo "have address: $resolved_address\n";
		if (!exclude_link($resolved_address)) {
			#$link_array[] = $resolved_addres;
			try {
				db_store_link($seed,$resolved_address);//_internal_only for only links in DB
			} catch(Exception $e) {
				echo "***ERROR***\n";
				echo "Couldn't store: $resolved_address\n";
				echo "While processing: $SEED_URL\n";
				//ignore
			}
		}
		#echo "Harvested: ".$resolved_address." \n";
	}

	db_marked_processed($seed);

	echo "Pause...\n";

	$wait = mt_rand(9000000,11000000);#9 to 11 seconds
	usleep($wait); #(arg in microseconds)

	$seed = db_get_next_to_process();
}
db_close();
echo "Done.\n";

#link_array now has all off-site links from SEED pagehttps://www.facebook.com/pages/Clapham-Adventure/407825022594984
#insert into database
#for ($xx=0; $xx<length($link_array); $xx++) {
	#store new page returns unique id for url
	#also checks if url already exists, and if so just inserts  
#	page_id = store_new_page($seed, $link_array[$xx]); 
#	store_link($seed,page_id)
#$spider_array = archive_links($spider_array, 0, $link_array);

#$link_array;
#$temp_link_array = harvest_links($SEED_URL);



# Spider links in remaining penetration levels
#for($penetration_level=1; $penetration_level<=$MAX_PENETRATION; $penetration_level++)
#    {
#    $previous_level = $penetration_level - 1;
#    for($xx=0; $xx<count($spider_array[$previous_level]); $xx++)
#        {
#        unset($temp_link_array);
#        $temp_link_array = harvest_links($spider_array[$previous_level][$xx]);
#        echo "Level=$penetration_level, xx=$xx of ".count($spider_array[$previous_level])." <br>\n"; 
#        $spider_array = archive_links($spider_array, $penetration_level, $temp_link_array);
#        }
#    }

# Store seed page HTML in Database

# Store Links in Database
#store_links($seed,$spider_array);
#
#for($penetration_level=1; $penetration_level<=$MAX_PENETRATION; $penetration_level++)
#    {
#    for($xx=0; $xx<count($spider_array[$previous_level]); $xx++)
#        {
#       download_images_for_page($spider_array[$previous_level][$xx]);
#        }
#    }
#date_default_timezone_set('Europe/London');
mail($operator_email, "Crawl Success", "Bot has  finished: " . date('Y-m-d H:i:s') ."\n","FROM: " . $operator_email);
?>
