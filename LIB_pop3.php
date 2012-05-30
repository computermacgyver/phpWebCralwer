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

########################################################################
#                                                                       
# LIB_pop3.php     POP3 Mail Routines                                   
#                                                                       
# This library provides routines that read and delete mail form mail    
# servers that employ POP3 (Post Office Protocol)                       
#                                                                       
# Detailed information about POP3 is found in RFC 1939                  
# http://www.faqs.org/rfcs/rfc1939.html                                 
#                                                                       
#-----------------------------------------------------------------------
# FUNCTIONS                                                             
#                                                                       
#    POP3_connect     Initiates a POP3 connection to a mail server and  
#                     returns a connection hand and indication if the   
#                     username and password were authenticated.         
#                                                                       
#    POP3_list()      Executes a POP3 LIST command                      
#                                                                       
#    POP3_retr()      Executes a POP3 RETR command                      
#                                                                       
#    POP3_delete()    Executes a POP3 DELE command                      
#                                                                       
#    POP3_quit()      Executes a POP3 QUIT command                      
#                                                                       
#-----------------------------------------------------------------------

/***********************************************************************
POP3 Constants (scope = global)                                        
----------------------------------------------------------------------*/
define("END_CHAR", ".");        // Identifies the POP3 multi line
                                // termination character (if found
                                // at the beginning of a line.
define("POP3_PORT", "110");     // POP3 servers listen to port 110

/***********************************************************************
POP3_connect($server, $user, $pass)                                     
-------------------------------------------------------------            
DESCRIPTION:                                                             
        Attempts to open a socket to the POP3 server identified by
        $server. Uses $user and $pass to authenticate a user ith an
        email account on $server.
        
OUTPUT:
        $array['login']   True if authentication is successful
                          otherwise false
        $array['handle']  The socket id used in subsequent commands
        $array['message'] Signon message returned by mail server
                                                                            
INPUT:                                                                    
        $server           Address of POP3 mail server
        $user             Email address of email account
        $pass             Password for email account
***********************************************************************/
function POP3_connect($server, $user, $pass)
    {
    $error="";
    $handle = fsockopen($server, $port=POP3_PORT, $errno, $errstr, 30);
    $message = fgets($handle, 4096);
    if(stristr($message, "+OK") === FALSE)    
        $error = $error . $message;
    
    // Send the username
    fwrite($handle, "user ".$user."\n");
    $user_reply = fgets($handle, 4096);
    if(stristr($user_reply, "+OK") === FALSE)    
        $error = $error . $user_reply;
    
    // Send the password
    fwrite($handle, "pass ".$pass."\n");
    $pass_reply = fgets($handle, 4096);
    if(stristr($pass_reply, "+OK") === FALSE)    
        $error = $error . $pass_reply;
    
    // Evaluate the results of presenting user/pass credentials
    if(strlen($error)==0)
        {
        $ret_array['login']=true;
        $ret_array['message']=$message;
        }
    else
        {
        $ret_array['login']=false;
        $ret_array['message']=$error;
        }
    $ret_array['handle']=$handle;
    
    // Return result array
    return $ret_array;
    }

/***********************************************************************
POP3_list($handle)                                                      
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Creates an array, each element containing the pair "id size"    
OUTPUT:                                                                 
        $list_array[n][id size]                                         
INPUT:                                                                  
        $handle           From POP3_connect() function                  
***********************************************************************/
function POP3_list($handle)
    {
    // Initialization
    $list_array = array();
    $reply="";
    
    // Make "list" request
    $list = fputs($handle, "LIST\n");
    $list_reply = fgets($handle);
    
    // Fill an array with mail server reply
    if(stristr($list_reply, "+OK"))
        {
    	while(!stristr($reply, END_CHAR))
            {
            $reply = fgets($handle);
            if(!stristr($reply, END_CHAR))
                array_push($list_array, $reply);
            }
        return $list_array;
        }
    }

/***********************************************************************
POP3_retr($handle, $id)                                                 
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Executes a POP3 RETR command                                    
OUTPUT:                                                                 
        $message    The entire email message (including headers)        
                    that corresponds to $id                             
INPUT:                                                                  
        $handle     From POP3_connect() function                        
        $id         From POP3_list() function                           
***********************************************************************/
function POP3_retr($handle, $id)
    {
    // Initialize
    $flag="";
    $message="";
    
    // Make POP3 "RETR #id" request
    fputs($handle, "RETR ".$id."\n");

    // Gather the multiline response
   	while($flag=="")
        {
        $this_line = fgets($handle, 4095);
        if(substr($this_line, 0, 1)==".")
            $flag="FLAG";
        
        $message = $message . $this_line;
        }
    return $message;
    }

/***********************************************************************
POP3_delete($handle, $id)                                               
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Executes a POP3 DELE command, which marks email records,        
        indicated by $id, for deletion. The record is not actually      
        deleted until a QUIT command is issued.                         
INPUT:                                                                  
        $handle           From POP3_connect() function                  
***********************************************************************/
function POP3_delete($handle, $id)
    {
    fputs($handle, "DELE ".$id."\n");
    $reply = fgets($handle);
    return $reply;
    }

/***********************************************************************
POP3_quit($handle)                                                      
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Executes a POP3 QUIT command, which ends the POP3 session and   
        deletes any records previously marked with POP3_delete().       
INPUT:                                                                  
        $handle           From POP3_connect() function                  
***********************************************************************/
function POP3_quit($handle)
    {
    fputs($handle, "QUIT\n");
    $reply = fgets($handle);
    return $reply;
    }
?>
