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

//Before starting, check the domains fields of the database and fill in any missing entries
//Also fill in missing 
//$strSQL="SELECT strURL,strDomain,strHTML FROM tblPages WHERE bolProcessed=0 AND bolCentral=1";
//$result = mysql_query($strSQL,$GLOBALS["db"]) or die('Query failed: ' . mysql_error());
$seed=db_get_next_to_process();
while ($seed!=null) {
	$domain=$seed['strDomain'];
	$html=$seed['strHTML'];
	
	$atags = parse_array($html,"<a","</a>");
	foreach ($atags as $tag) {
		$destURL=get_attribute($tag,"href");
		//echo "destURL: $destURL\n";
		if (strpos($destURL,"http://")!==false || strpos($destURL,"https://")!==false) {
			$destDomain=get_domain($destURL);
			//echo "Saving To-From: $domain - $destDomain\n";
			db_update_domain_links($domain,$destDomain);
		}
	}
	
	db_marked_processed($seed);
	$seed=db_get_next_to_process();
}


db_close();
echo "Done.\n";

mail($operator_email, "Parse Success", "Finished parsing external linked domains: " . date('Y-m-d H:i:s') ."\n","FROM: " . $operator_email);
?>
