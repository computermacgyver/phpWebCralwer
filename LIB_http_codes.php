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
#-------------------------------------
# Define 100 series http codes (informational)
#-------------------------------------
$status_code_array[100]  = "100 Continue";
$status_code_array[101]  = "101 Switching Protocols";

#-------------------------------------
# Define 200 series http codes (successful)
#-------------------------------------
$status_code_array[200]  = "200 OK";
$status_code_array[201]  = "201 Created";
$status_code_array[202]  = "202 Accepted";
$status_code_array[203]  = "203 Non-Authoritative Information";
$status_code_array[204]  = "204 No Content";
$status_code_array[205]  = "205 Reset Content";
$status_code_array[206]  = "206 Partial Content";

#-------------------------------------
# Define 300 series http codes (redirection)
#-------------------------------------
$status_code_array[300]  = "300 Multiple Choices";
$status_code_array[301]  = "301 Moved Permanently";
$status_code_array[302]  = "302 Found";
$status_code_array[303]  = "303 See Other";
$status_code_array[304]  = "304 Not Modified";
$status_code_array[305]  = "305 Use Proxy";
$status_code_array[306]  = "306 (Unused)";
$status_code_array[307]  = "307 Temporary Redirect";

#-------------------------------------
# Define 400 series http codes (client error)
#-------------------------------------
$status_code_array[400]  = "400 Bad Request";
$status_code_array[401]  = "401 Unauthorized";
$status_code_array[402]  = "402 Payment Required";
$status_code_array[403]  = "403 Forbidden";
$status_code_array[404]  = "404 Not Found";
$status_code_array[405]  = "405 Method Not Allowed";
$status_code_array[406]  = "406 Not Acceptable";
$status_code_array[407]  = "407 Proxy Authentication Required";
$status_code_array[408]  = "408 Request Timeout";
$status_code_array[409]  = "409 Conflict";
$status_code_array[410]  = "410 Gone";
$status_code_array[411]  = "411 Length Required";
$status_code_array[412]  = "412 Precondition Failed";
$status_code_array[413]  = "413 Request Entity Too Large";
$status_code_array[414]  = "414 Request-URI Too Long";
$status_code_array[415]  = "415 Unsupported Media Type";
$status_code_array[416]  = "416 Requested Range Not Satisfiable";
$status_code_array[417]  = "417 Expectation Failed";

#-------------------------------------
# Define 500 series http codes (server error)
#-------------------------------------
$status_code_array[500]  = "500 Internal Server Error";
$status_code_array[501]  = "501 Not Implemented";
$status_code_array[502]  = "502 Bad Gateway";
$status_code_array[503]  = "503 Service Unavailable";
$status_code_array[504]  = "504 Gateway Timeout";
$status_code_array[505]  = "505 HTTP Version Not Supported";
?>