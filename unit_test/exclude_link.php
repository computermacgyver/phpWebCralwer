<?php
//Restrict crawling to a whitelist? true | false
$whitelistdomain=true;	
//What level of domain to match (-1 any part, 1=tld, 2=sld, etc. e.g. 1=uk, 2=gov.uk, 3=direct.gov.uk)
$whitelistdomainlevel=-1; 
//list of domains separated with : (no starting / ending :)
$whitelistdomainlist="example.com:another.co.uk";

include_once("../LIB_simple_spider.php");

assert(exclude_link("http://example.com")===false);

assert(exclude_link("http://www.example.com")===false);

assert(exclude_link("http://abcde.example.com")===false);

assert(exclude_link("http://www.example.com/hij/klmno")===false);

assert(exclude_link("http://abc.def.example.com/hij/klmno")===false);

assert(exclude_link("http://abc.def.should-fail.com/hij/klmno")===true);

?>
