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

$dir="networks/";
mkdir($dir);//Returns false if cannot make dir (but this is ok if e.g. dir already exists)

//Before starting, check the domains fields of the database and fill in any missing entries
//Also fill in missing 
$strSQL="SELECT strDomain FROM tblPages WHERE bolCentral=1 GROUP BY strDomain";
$result = mysql_query($strSQL,$GLOBALS["db"]) or die('Query failed: ' . mysql_error());
while (null!=($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
	$domain=$row['strDomain'];
	//system or exec
	system("php listNodes_graphml_forDomain.php $domain > $dir/$domain.graphml");
}


db_close();
echo "Done.\n";

mail($operator_email, "Networks for domain done", "Finished producing networks for each domain: " . date('Y-m-d H:i:s') ."\n","FROM: " . $operator_email);
?>
