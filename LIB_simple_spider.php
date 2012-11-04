<?php
/*
########################################################################                                        
Copyright 2007, Michael Schrenk                                                                                 
   This software is designed for use with the book,                                                             
   "Webbots, Spiders, and Screen Scarpers", Michael Schrenk, 2007 No Starch Press, San Francisco CA             
                                                                                                                
W3C® SOFTWARE NOTICE AND LICENSE                                                                                
                                                                                                                
This work (and included software, documentation such as READMEs, or other                                       
related items) is being provided by the copyright holders under the following license.                          
 By obtaining, using and/or copying this work, you (the licensee) agree that you have read,                     
 understood, and will comply with the following terms and conditions.                                           
                                                                                                                
Permission to copy, modify, and distribute this software and its documentation, with or                         
without modification, for any purpose and without fee or royalty is hereby granted, provided                    
that you include the following on ALL copies of the software and documentation or portions thereof,             
including modifications:                                                                                        
   1. The full text of this NOTICE in a location viewable to users of the redistributed                         
      or derivative work.                                                                                       
   2. Any pre-existing intellectual property disclaimers, notices, or terms and conditions.                     
      If none exist, the W3C Software Short Notice should be included (hypertext is preferred,                  
      text is permitted) within the body of any redistributed or derivative code.                               
   3. Notice of any changes or modifications to the files, including the date changes were made.                
      (We recommend you provide URIs to the location from which the code is derived.)                           
                                                                                                                
THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT HOLDERS MAKE NO REPRESENTATIONS OR           
WARRANTIES, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR FITNESS          
FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD         
PARTY PATENTS, COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.                                                          
                                                                                                                
COPYRIGHT HOLDERS WILL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL DAMAGES ARISING OUT     
OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.                                                                    
                                                                                                                
The name and trademarks of copyright holders may NOT be used in advertising or publicity pertaining to the      
software without specific, written prior permission. Title to copyright in this software and any associated     
documentation will at all times remain with copyright holders.                                                  
########################################################################                                        
*/

//General initalization for whitelist
global $whitelistdomain,$whitelistdomainlist,$whitelistdomainlist_arr,$whitelistdomainlist_part;
if ($whitelistdomain) {
	$whitelistdomainlist_arr=explode(":",$whitelistdomainlist);
	$whitelistdomainlist_part=":$whitelistdomainlist:";
}

global $whitelisturl,$whitelisturllist,$whitelisturllist_arr;
if ($whitelisturl) {
	$whitelisturllist_arr=explode(":",$whitelisturllist);
}







/***********************************************************************
harvest_links($url)                                                     
-------------------------------------------------------------			
DESCRIPTION:															
		Collects all links from a web page                              
                                                                        
INPUT:																    
		$url                                                            
            Fully resolved web address of target web page               
RETURNS:																
		Returns an array of links                                       
***********************************************************************/
function harvest_links($url)
    {
    # Initialize
    global $DELAY;
    $link_array = array();
    
    # Get page base for $url
    $page_base = get_base_page_address($url);
    
    # Download webpage
    sleep($DELAY);          
    $downloaded_page = http_get($url, "");
    $anchor_tags = parse_array($downloaded_page['FILE'], "<a", "</a>", EXCL);
    # Put http attributes for each tag into an array
    for($xx=0; $xx<count($anchor_tags); $xx++)
        {
        $href = get_attribute($anchor_tags[$xx], "href");
        $resolved_addres = resolve_address($href, $page_base);
        $link_array[] = $resolved_addres;
        echo "Harvested: ".$resolved_addres." \n";
        }
    return $link_array;
    }

/***********************************************************************
archive_links($spider_array, $penetration_level, $temp_link_array)      
-------------------------------------------------------------			
DESCRIPTION:															
		Puts raw links into an archival array                           
                                                                        
INPUT:																    
        $spider_array                                                   
            The name of the archival array                              
                                                                        
        $penetration_level                                              
            Page depth at which the spidering was conducted             
                                                                        
        $temp_link_array                                                
            $temporary array of raw links                               
RETURNS:																
		Returns archival array                                          
***********************************************************************/
function archive_links($spider_array, $penetration_level, $temp_link_array)
    {
    for($xx=0; $xx<count($temp_link_array); $xx++)
        {
        # Don't add exlcuded links to $spider_array
        if(!excluded_link($spider_array, $temp_link_array[$xx]))
            {
            $spider_array[$penetration_level][] = $temp_link_array[$xx];
            }
        }
    return $spider_array;
    }

/***********************************************************************
get_domain($url)                                                        
-------------------------------------------------------------			
DESCRIPTION:															
        Gets the domain for a web address                               
INPUT:																    
        $url                                                            
            The web address                                             
                                                                        
RETURNS:																
		Returns the domain for the inputed url                          
***********************************************************************/
function get_domain($url) {
    // Remove protocol from $url
    //$url = str_replace("http://", "", $url);
    //$url = str_replace("https://", "", $url);
    $url = str_replace(get_protocol($url), "", $url);

    //remove www. as it is a default 3rd level domain that will usually be added if not present.
    $url = str_replace("www.", "", $url);
    
    $url = str_replace(":".get_port($url),"",$url);
    
    // Remove page and directory references
    if(stristr($url, "/"))
        $url = substr($url, 0, strpos($url, "/"));
    
    return $url;
}

function get_port($url) {
	$url = str_replace(get_protocol($url), "", $url);
    if (stristr($url,":")) {
		$start=strpos($url,":");
		$end=strpos($url,"/",$start);
		//echo "S=$start  END=$end\n";
		if ($end===false) return substr($url,$start+1);
		return substr($url,$start+1,$end-1-$start);
	} else {
		return 80;
	}
}

//return url from xth level domain 1=top level (uk), 2=gov.uk, 3= direct.gov.uk
function get_domain_part($url,$level) {
	$d = get_domain($url);
	
	if (substr_count($d,".")<=$level-1) return $d;
	
	$len = strlen($d);
	//find $level to last period
	$pos = -1;
	$count=0;
	while ($count<$level) {
		$count++;
		$pos = strrpos($d,".",-1*$pos-1);
		if ($pos===false) break;
		//echo "POS=$pos";
		$pos=$len - $pos;
		//echo  " POS'=$pos str:  " .substr($d,-1*$pos) . "\n";
	}
	$pos = $pos-1;
	return substr($d,-1*$pos);
}

function get_protocol($url) {
    $proto="udef://";
    if(strpos($url, "://")>0)
        $proto = substr($url, 0, strpos($url, "://")) . "://";
    return $proto;
}

function get_page($url) {
	$page="/";
	$url = str_replace(get_protocol($url), "", $url);
	if(stristr($url, "/"))
		$page = substr($url, strpos($url, "/"));
	return $page;
}

function same_page($u1,$u2) {
	if (!get_domain($u2)==get_domain($u1)) return false;
	
	$p1=get_page($u1);
	$p2=get_page($u2);
	if ($p1==$p2 || ($p1."/")==$p2 || $p1==($p2."/")) return true;
	return false;
}

function clean_url($url) {

//Insert for directgov only
//http://www.direct.gov.uk/cy/Message/index.htm?redirectURL=http%3a%2f%2fwww.direct.gov.uk%2fen%2fDl1%2fDirectories%2findex.htm&redirectLabel=Cysylltiadau&redirectWindow=no

	if (strpos($url,"redirectURL=")!==false) {
		$url=substr($url,strpos($url,"redirectURL=")+12);
		$url=substr($url,0,strpos($url,"&"));
		$url=urldecode($url);
	}
	$url = strtolower($url);
	$url = str_replace("www.", "", $url);
	$url = str_replace(get_protocol($url), "", $url);
	//strip everything after #
	if(strpos($url, "#")>0)
		$url = substr($url, 0, strpos($url, "#"));
	//strip query string
	if(strpos($url, "?")>0) {
		$qstring = substr($url,strpos($url, "?")+1);
		$url = substr($url, 0, strpos($url, "?"));

		//strip trailing slash if present --- Inserted 2010-06-27
		//$lastChar=substr($url,strlen($url)-1);
		//if ($lastChar=="/")
		//$url=substr($url,0,strlen($url)-1);

		$arrQstring=explode("&",$qstring);
		$qstringKeep="";
		foreach ($arrQstring as $aE) {
			//echo "$aE\n";
			if (strpos($aE,"=")===FALSE) continue;
			$aKey=substr($aE,0,strpos($aE,"="));
			$aVal=substr($aE,strpos($aE,"=")+1);
			//echo "$aKey == $aVal\n";
			//if (strpos($aKey,"id")>0 || $aKey=="p" || $aKey=="post" || $aKey=="page" || $aKey=="v" ) {
//Updated 2010-06-27
if (strpos($aKey,"id")!==FALSE || $aKey=="p" || $aKey=="post" || $aKey=="page" || $aKey=="v"  || $aKey=="reference" || $aKey=="story" || $aKey=="s" || $aKey=="number" || $aKey=="doc" || $aKey=="lang" || $aKey=="t" || $aKey=="f") {
				$url=$url . "?" . $aKey . "=" . $aVal;
				//break;
			}
		}
	}
	//strip trailing slash if present
	$lastChar=substr($url,strlen($url)-1);
	if ($lastChar=="/")
		$url=substr($url,0,strlen($url)-1);

	return $url;
}

/***********************************************************************
exclude_link($spider_array, $link)                                     
-------------------------------------------------------------			
DESCRIPTION:															
        Tests a link to see if it should be in the archival array       
INPUT:																    
        $spider_array                                                   
            The spider's archival array                                 
                                                                        
        $link                                                           
            The link under test                                         
RETURNS:																
		Returns TRUE or FALSE depending on if the link should be        
        excluded                                                        
***********************************************************************/
function exclude_link($link)
    {
    # Initialization
    global $SEED_URL, $exclusion_array, $ALLOW_OFFSITE, $ONLY_OFFSITE;
    $exclude = false;
    
    // Exclude links that are JavaScript commands
    if(stristr($link, "javascript"))
        {
       // echo "Ignored JavaScript fuction: $link\n";
        $exclude=true;
        }

    if(strlen($link) < 9)
        {
       // echo "Link too short: $link\n";
        $exclude=true;
        }
    
    // Exclude links found in $exclusion_array
    for($xx=0; $xx<count($exclusion_array); $xx++)
        {
        //if(stristr($link, $exclusion_array[$xx]))
        if(preg_match($exclusion_array[$xx],$link)>0)
            {
            //echo "Ignored excluded link: $link\n";
            $exclude=true;
			break;
            }
        }
        
    // Exclude offsite links if requested
   /*if($ALLOW_OFFSITE==false)
        {
        if(get_domain($link)!=get_domain($SEED_URL))
            {
            //echo "Ignored offsite link: $link\n";
            $exclude=true;
            }
        }

    if($ONLY_OFFSITE==true)
        {
        if(get_domain($link)==get_domain($SEED_URL))
            {
           // echo "Ignored on-site link: $link\n";
            $exclude=true;
            }
        }*/
        global $whitelistdomain, $whitelistdomainlevel, $whitelistdomainlist,$whitelistdomainlist_arr,$whitelistdomainlist_part;
      if ($whitelistdomain) {
      	 if ($whitelistdomainlevel==-1) {//match any part
			$domain = get_domain($link);
      	 	$found=false;
      	 	#print $whitelistdomainlist_arr[0] . "\n$domain\n";
      	 	for ($x=0;$x<count($whitelistdomainlist_arr);$x++) {
      	 		if (strpos($domain,$whitelistdomainlist_arr[$x])!==false) {
      	 			$found=true;
      	 			break;
      	 		}
      	 	}
      	 	if ($found===false) $exclude=true;
		 } else {
			$domain = get_domain_part($link,$whitelistdomainlevel);
			if (strpos($whitelistdomainlist_part,":$domain:")===false) {
				$exclude=true;
			}
	     }
	   }
	   
	   global $whitelisturl,$whitelisturllist_arr;
	   if ($whitelisturl) {
      	 	$found=false;
      	 	for ($x=0;$x<count($whitelisturllist_arr);$x++) {
      	 		if (strpos($link,$whitelisturllist_arr[$x])!==false) {
      	 			$found=true;
      	 			break;
      	 		}
      	 	}
      	 	if ($found===false) $exclude=true;
	   }      

    return $exclude;
    }
?>
