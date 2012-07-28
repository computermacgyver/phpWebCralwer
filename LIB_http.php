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

#-----------------------------------------------------------------------
# LIB_http                                                              
#                                                                       
# F U N C T I O N S                                                     
#                                                                       
# http_get()                                                            
#    Fetches a file from the Internet with the HTTP protocol            
# http_header()                                                         
#    Same as http_get(), but only returns HTTP header instead of        
#    the normal file contents                                           
# http_get_form()                                                       
#    Submits a form with the GET method                                 
# http_get_form_withheader()                                            
#    Same as http_get_form(), but only returns HTTP header instead of   
#    the normal file contents                                           
# http_post_form()                                                      
#    Submits a form with the POST method                                
# http_post_withheader()                                                
#    Same as http_post_form(), but only returns HTTP header instead of  
#    the normal file contents                                           
# http_header()                                                         
#                                                                       
# http()                                                                
#   A common routine called by all of the previously described          
#   functions. You should always use one of the other wrappers for this 
#   routine and not call it directly.                                   
#                                                                       
#-----------------------------------------------------------------------
# R E T U R N E D   D A T A                                             
# All of these routines return a three dimensional array defined as     
# follows:	    												        
#    $return_array['FILE']   = Contents of fetched file, will also      
#                              include the HTTP header if requested    
#    $return_array['STATUS'] = CURL generated status of transfer        
#    $return_array['ERROR']  = CURL generated error status              
########################################################################

/***********************************************************************
Webbot defaults (scope = global)                                       
----------------------------------------------------------------------*/
# Define how your webbot will appear in server logs
#define("WEBBOT_NAME", "Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.43 Safari/532.5");

#define("WEBBOT_NAME", "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.18) Gecko/2010021501 Ubuntu/8.04 (hardy) Firefox/3.0.18");

define("WEBBOT_NAME", $user_agent);

# Length of time cURL will wait for a response (seconds)
define("CURL_TIMEOUT", 250);

# Location of your cookie file. (Must be fully resolved local address)
define("COOKIE_FILE", $cookie_file_location);

# DEFINE METHOD CONSTANTS
define("HEAD", "HEAD");
define("GET",  "GET");
define("POST", "POST");

# DEFINE HEADER INCLUSION
define("EXCL_HEAD", FALSE);
define("INCL_HEAD", TRUE);

/***********************************************************************
User interfaces         	                                            
-------------------------------------------------------------			
*/

/***********************************************************************
function http_get($target, $ref)                                        
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Downloads an ASCII file without the http header                 
INPUT:                                                                  
        $target       The target file (to download)                     
        $ref          The server referer variable                       
OUTPUT:                                                                 
        $return_array['FILE']   = Contents of fetched file, will also   
                                 include the HTTP header if requested   
        $return_array['STATUS'] = CURL generated status of transfer     
        $return_array['ERROR']  = CURL generated error status           
***********************************************************************/
function http_get($target, $ref)
    {
    return http($target, $ref, $method="GET", $data_array="", EXCL_HEAD);
    }

/***********************************************************************
http_get_withheader($target, $ref)                                      
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Downloads an ASCII file with the http header                    
INPUT:                                                                  
        $target       The target file (to download)                     
        $ref          The server referer variable                       
OUTPUT:                                                                 
        $return_array['FILE']   = Contents of fetched file, will also   
                                 include the HTTP header if requested   
        $return_array['STATUS'] = CURL generated status of transfer     
        $return_array['ERROR']  = CURL generated error status           
***********************************************************************/
function http_get_withheader($target, $ref)
    {
    return http($target, $ref, $method="GET", $data_array="", INCL_HEAD);
    }

/***********************************************************************
http_get_form($target, $ref, $data_array)                               
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Submits a form with the GET method and downloads the page       
        (without a http header) referenced by the form's ACTION variable
INPUT:                                                                  
        $target       The target file (to download)                     
        $ref          The server referer variable                       
        $data_array   An array that defines the form variables          
                      (See "Webbots, Spiders, and Screen Scrapers" for  
                      more information about $data_array)               
OUTPUT:                                                                 
        $return_array['FILE']   = Contents of fetched file, will also   
                                 include the HTTP header if requested   
        $return_array['STATUS'] = CURL generated status of transfer     
        $return_array['ERROR']  = CURL generated error status           
***********************************************************************/
function http_get_form($target, $ref, $data_array)
    {
    return http($target, $ref, $method="GET", $data_array, EXCL_HEAD);
    }

/***********************************************************************
http_get_form_withheader($target, $ref, $data_array)                    
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Submits a form with the GET method and downloads the page       
        (with http header) referenced by the form's ACTION variable     
INPUT:                                                                  
        $target       The target file (to download)                     
        $ref          The server referer variable                       
        $data_array   An array that defines the form variables          
                      (See "Webbots, Spiders, and Screen Scrapers" for  
                      more information about $data_array)               
OUTPUT:                                                                 
        $return_array['FILE']   = Contents of fetched file, will also   
                                 include the HTTP header if requested   
        $return_array['STATUS'] = CURL generated status of transfer     
        $return_array['ERROR']  = CURL generated error status           
***********************************************************************/
function http_get_form_withheader($target, $ref, $data_array)
    {
    return http($target, $ref, $method="GET", $data_array, INCL_HEAD);
    }

/***********************************************************************
http_post_form($target, $ref, $data_array)                              
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Submits a form with the POST method and downloads the page      
        (without http header) referenced by the form's ACTION variable  
INPUT:                                                                  
        $target       The target file (to download)                     
        $ref          The server referer variable                       
        $data_array   An array that defines the form variables          
                      (See "Webbots, Spiders, and Screen Scrapers" for  
                      more information about $data_array)               
OUTPUT:                                                                 
        $return_array['FILE']   = Contents of fetched file, will also   
                                 include the HTTP header if requested   
        $return_array['STATUS'] = CURL generated status of transfer     
        $return_array['ERROR']  = CURL generated error status           
***********************************************************************/
function http_post_form($target, $ref, $data_array)
    {
    return http($target, $ref, $method="POST", $data_array, EXCL_HEAD);
    }

function http_post_withheader($target, $ref, $data_array)
    {
    return http($target, $ref, $method="POST", $data_array, INCL_HEAD);
    }

function http_header($target, $ref)
    {
    return http($target, $ref, $method="HEAD", $data_array="", INCL_HEAD);
    }

/***********************************************************************
http($url, $ref, $method, $data_array, $incl_head)                      
-------------------------------------------------------------			
DESCRIPTION:															
	This function returns a web page (HTML only) for a web page through	
	the execution of a simple HTTP GET request.							
	All HTTP redirects are automatically followed.						
                                                                        
    IT IS BEST TO USE ONE THE EARLIER DEFINED USER INTERFACES           
    FOR THIS FUNCTION                                                   
                                                                        
INPUTS:																	
	$target      Address of the target web site		 					
	$ref		 Address of the target web site's referrer				
	$method		 Defines request HTTP method; HEAD, GET or POST         
	$data_array	 A keyed array, containing query string                 
	$incl_head	 TRUE  = include http header                            
                 FALSE = don't include http header                      
                                                                        
RETURNS:																
    $return_array['FILE']   = Contents of fetched file, will also       
                              include the HTTP header if requested      
    $return_array['STATUS'] = CURL generated status of transfer         
    $return_array['ERROR']  = CURL generated error status               
***********************************************************************/
function http($target, $ref, $method, $data_array, $incl_head)
	{
    # XXX TODO: Run and test for 406 responses on otherwise acceptable
    #           downloads, as a result of the Accepts: header. This may
    #           well have a neglible effect: I understand many servers
    #           ignore it.
    # XXX TODO: Run and test for 206 and 416 responses from Range: header.
    # XXX TODO: Setup callback on HEAD to cancel download if given a
    #           content-type we can't handle 

    # Initialize PHP/CURL handle
	$ch = curl_init();

    # Process data, if presented
    if(is_array($data_array))
        {
	    # Convert data array into a query string (ie animal=dog&sport=baseball)
        foreach ($data_array as $key => $value) 
            {
            if(strlen(trim($value))>0)
                $temp_string[] = $key . "=" . urlencode($value);
            else
                $temp_string[] = $key;
            }
        $query_string = join('&', $temp_string);
        }
        
    # HEAD method configuration
    if($method == HEAD)
        {
	    curl_setopt($ch, CURLOPT_HEADER, TRUE);                // No http head
	    curl_setopt($ch, CURLOPT_NOBODY, TRUE);                // Return body
        }
    else
        {
        # GET method configuration
        if($method == GET)
            {
            if(isset($query_string))
                $target = $target . "?" . $query_string;
            curl_setopt ($ch, CURLOPT_HTTPGET, TRUE); 
            curl_setopt ($ch, CURLOPT_POST, FALSE); 
            }
        # POST method configuration
        if($method == POST)
            {
            if(isset($query_string))
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $query_string);
            curl_setopt ($ch, CURLOPT_POST, TRUE); 
            curl_setopt ($ch, CURLOPT_HTTPGET, FALSE); 
            }
            curl_setopt($ch, CURLOPT_HEADER, $incl_head);   // Include head as needed
	    curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
        }
        
	curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_FILE);   // Cookie management.
	curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_FILE);
	curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);    // Timeout
	curl_setopt($ch, CURLOPT_USERAGENT, WEBBOT_NAME);   // Webbot name
	curl_setopt($ch, CURLOPT_URL, $target);             // Target site
	curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
	curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
	curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'read_header'); // Callback function
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('accept: text/*')); // Ask for text only
	
	global $fetchrangeonly,$maxfetchsize;
	if ($fetchrangeonly === true)
	    curl_setopt($ch, CURLOPT_RANGE, "0-".strval($maxfetchsize-1)); // Size limit

    # Create return array
    $return_array['FILE']   = curl_exec($ch); 
    $return_array['STATUS'] = curl_getinfo($ch);
    $return_array['ERROR']  = curl_error($ch);
    
    # Close PHP/CURL handle
  	curl_close($ch);
    
    # Return results
  	return $return_array;
    }


function http_parse_headers( $header )
{
    $retVal = array();
    $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
    foreach( $fields as $field ) {
        if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
            $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
            if( isset($retVal[$match[1]]) ) {
                $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
            } else {
                $retVal[$match[1]] = trim($match[2]);
            }
        }
    }
    return $retVal;
}

# Check if we're being given a file which is too large, or which is
# in a non-text format we can't read.
# This callback function is given the header one line at a time.
# Hilariously, the way to return an error (and abort the transfer)
# is to return anything other than the length of $string.
# XXX TODO: Check that this hasn't eaten the headers, stopping them
#           being returned as part of the content array.
function read_header($ch, $string)
    {
    global $maxfetchsize;
    
    $length = strlen($string);
    # echo "Header: $string<br />\n";
    # XXX check http_parse_headers library is valid here. Otherwise, unpack from source
    $headerarray = http_parse_headers($string);
    if (array_key_exists('Content-Type', $headerarray))
        {
            if (preg_match( '/text\//', $headerarray['Content-Type']) == 0)
                {
                    print "Content-Type not text/*. Aborting fetch.";
                    # Abort fetch
                    return FALSE;
                }
        }
    if (array_key_exists('Content-Length', $headerarray))
        {
            if ($headerarray['Content-Length'] > $maxfetchsize)
                {
                    print "Content too large. Server ignoring Range:? Aborting fetch.";
                    # Abort fetch
                    return FALSE;
                }
        }
    # Continue with fetch
    return $length;
    }

?>
