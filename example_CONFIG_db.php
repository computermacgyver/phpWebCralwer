<?php

global $db_host,$db_username,$db_name,$operator_email,$whitelistdomain,$whitelistdomainlevel,$whitelistdomainlist;

//Is this the first time running the script or have database changes been made outside of the script?
//If so, set this to true; leaving it at true will not hurt but will cause extra start-up time if the script is stopped and restarted.
$first_run=true;

$db_host = "localhost";
$db_username = "jrandom";
$db_password = "ASDF!!1!one1";
$db_name = "somedb";
$db_port = 3306;
$db_dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";
## $operator_email is used to mail the user on script completion
$operator_email = "j.random@example.com";

## User agent string to send in HTTP requests
$user_agent = "Mozilla/5.0 phpWebCrawler Contact Info Here ";
$cookie_file_location = "/home/jrandom/cookies.txt";

# $whitelistdomain, $whitelistdomainlevel, $whitelistdomainlist; //NEED to document SAH
//Crawl all .gov.uk and .org.uk sites

//Restrict crawling to a whitelist? true | false
$whitelistdomain=true;	
//What level of domain to match (-1 any part, 1=tld, 2=sld, etc. e.g. 1=uk, 2=gov.uk, 3=direct.gov.uk)
$whitelistdomainlevel=2; 
//list of domains separated with : (no starting / ending :)
$whitelistdomainlist="gov.uk:org.uk";
//Fetch only first part of each page, to avoid huge files? (Experimental!)
$fetchrangeonly=false;
// If $fetchrangeonly=true, what range to fetch? Here, the first 100KB is specified.
$maxfetchsize=100000;

// Set spider penetration depth. 
//If 0 crawl only pages in database. NOT IMPLEMETNED
//If -1 ignore this parameter entirely: WARNING: Poorly configured sites may have an infinite number of URLs to crawl.
$MAX_PENETRATION = 5;                           

// Wait one second between page fetches NOT IMPLEMENTED
//$FETCH_DELAY     = 1;

// Wait five seconds between page fetches on same domain
$SAME_DOMAIN_FETCH_DELAY= 5;
// What part of the domain should be matched to qualify for the SAME_DOMAIN_FETCH_DELAY?
$SAME_DOMAIN_FETCH_LEVEL= 3;

// Allow spider to roam from the SEED_URL's domain
$ALLOW_OFFSITE   = true;                       
// Only include URL's to remote domains
$ONLY_OFFSITE   = false;                       

?>
