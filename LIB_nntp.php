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
# F U N C T I O N S                                                     
#                                                                       
# read_nntp_buffer($socket)                                             
#    Used by the other functions to read the buffer that receives data  
#    from news servers                                                  
#                                                                       
# get_nntp_groups($server)                                              
#    Returns a list of newsgroups on a valid news server                
#                                                                       
# get_nntp_article_ids($server, $newsgroup)                             
#    Returns a list of article ids for a newsgroup on a news server     
#                                                                       
# read_nntp_article($server, $newsgroup, $article)                      
#    Downloads a single article from a news group on a news server      
#                                                                       
#-----------------------------------------------------------------------

/***********************************************************************
read_nntp_buffer($socket)                                               
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Reads data from a news server                                   
                                                                        
        THIS FUNCTION IS USED INTERNALLY AND NOT USEFUL ALONE           
                                                                        
INPUT:                                                                  
        $socket       Reference to the socket of the connection to      
                      the news server                                   
                                                                        
OUTPUT:                                                                 
        The data sent from the news server                              
***********************************************************************/
function read_nntp_buffer($socket)
    {
    $this_line ="";
    $buffer ="";

    while($this_line!=".\r\n")          // Read until lone . found on line
        {
        $this_line = fgets($socket);    // Read line from socket
        $buffer = $buffer . $this_line;
        #
        # UNCOMMENT THE FOLLOWING LINE IF YOU NEED TO SEE PROGRESS (This script may take a long time to run).
         echo "this_line=$this_line;<br>";
        #
        }
    return $buffer;
    }

/***********************************************************************
get_nntp_groups($server)                                                
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Reads available newsgroups from a news server                   
                                                                        
INPUT:                                                                  
        $socket       Reference to the socket of the connection to      
                      the news server                                   
                                                                        
OUTPUT:                                                                 
        A list of newsgroups on the news server                         
***********************************************************************/
function get_nntp_groups($server)
    {
    # Open socket connection to the mail server
    $fp = fsockopen($server, $port="119", $errno, $errstr, 30);
    if (!$fp)
        {
        # If socket error, issue error
        $return_array['ERROR'] = "ERROR: $errstr ($errno)";
        }
    else
        {
        # Else tell server to return a list of hosted newsgroups
        $out = "LIST\r\n";
        fputs($fp, $out);
        $groups = read_nntp_buffer($fp);

        $groups_array = explode("\r\n", $groups); // Convert to an array
        }
    fputs($fp, "QUIT \r\n"); // Log out
    fclose($fp); // Close socket

    return $groups_array;
    }

/***********************************************************************
get_nntp_article_ids($server, $newsgroup)                               
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Reads available article ids from a news server                  
                                                                        
INPUT:                                                                  
        $server      Address of news server                             
        $newsgroup   Name of newsgroup                                  
                                                                        
OUTPUT:                                                                 
        Returns available article ids for the newsgroup on the server   
***********************************************************************/
function get_nntp_article_ids($server, $newsgroup)
    {
    # Open socket connection to the mail server
    $socket = fsockopen($server, $port="119", $errno, $errstr, 30);
    if (!$socket)
        {
        # If socket error, issue error
        $return_array['ERROR'] = "ERROR: $errstr ($errno)";
        }
    else
        {
        # Else tell server which group to connect to
        fputs($socket, "GROUP ".$newsgroup." \r\n");
        $return_array['GROUP_MESSAGE'] = trim(fread($socket, 2000));
        # Get the range of available articles for this group
        fputs($socket, "NEXT \r\n");
        $res = fread($socket, 2000);
        $array = explode(" ", $res);
        $return_array['RESPONSE_CODE'] = $array[0];
        $return_array['EST_QTY_ARTICLES'] = $array[1];
        $return_array['FIRST_ARTICLE'] = $array[2];
        $return_array['LAST_ARTICLE'] = $array[3];
        }
    fputs($socket, "QUIT \r\n");
    fclose($socket);
    return $return_array;
    }

/***********************************************************************
read_nntp_article($server, $newsgroup, $article)                        
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Reads article from a news server                                
                                                                        
INPUT:                                                                  
        $server      Address of news server                             
        $newsgroup   Name of newsgroup                                  
        $article_id  ID of article to read                              
                                                                        
OUTPUT:                                                                 
        Returns the article specified by the article id                 
***********************************************************************/
function read_nntp_article($server, $newsgroup, $article)
    {
    # Open socket connection to the mail server
    $socket = fsockopen($server, $port="119", $errno, $errstr, 30);
    if (!$socket)
        {
        # If socket error, issue error
        $return_array['ERROR'] = "ERROR: $errstr ($errno)";
        }
    else
        {
        # Else tell server which group to connect to
        fputs($socket, "GROUP ".$newsgroup." \r\n");
        # Request this article's HEAD
        fputs($socket, "HEAD $article \r\n");
        $return_array['HEAD'] = read_nntp_buffer($socket);
        # Request the article
        fputs($socket, "BODY $article \r\n");
        $return_array['ARTICLE'] = read_nntp_buffer($socket);
        }
    fputs($socket, "QUIT \r\n");    // Sign out (newsgroup server)
    fclose($socket);                // Close socket
    return $return_array;           // Return data array
    }
?>
