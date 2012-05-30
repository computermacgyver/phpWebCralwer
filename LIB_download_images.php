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
# LIB_download_images.php     POP3 Mail Routines                        
#                                                                       
# This library provides routines #                                                                       
#                                                                       
#-----------------------------------------------------------------------
# FUNCTIONS                                                             
#                                                                       
#   download_binary_file()                                              
#                     A binary-safe download routine for images         
#                                                                       
#   mkpath($path)                                                       
#                     Createa a directory path for downloaded images    
#                                                                       
#   download_images_for_page($target)                                   
#                     Downloads all images for a given target webpage   
#                                                                       
#-----------------------------------------------------------------------

include_once("LIB_parse.php");               # include parse library
include_once("LIB_http.php");                # include curl library
include_once("LIB_resolve_addresses.php");   # include address resolution library

/***********************************************************************
download_binary_file($file, $ref)                                       
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Downloads an image file (binary safe)                           
INPUT:                                                                  
        $file         Image file to download                            
        $ref          Referer value sent to target server               
OUTPUT:                                                                 
        The image file is returned as a string                          
***********************************************************************/
function download_binary_file($file, $ref)
    {
    # Open a PHP/CURL session
    $s = curl_init();
    
    # Configure the CURL command
    curl_setopt($s, CURLOPT_URL, $file); // Define target site
    curl_setopt($s, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
    curl_setopt($s, CURLOPT_BINARYTRANSFER, 1);        // Indicate binary transfer
	curl_setopt($s, CURLOPT_REFERER, $ref);            // Referer value
	curl_setopt($s, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
	curl_setopt($s, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
	curl_setopt($s, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
    
    # Execute the CURL command (Send contents of target web page to string)
    $downloaded_page = curl_exec($s);
    
    # Close PHP/CURL session
    curl_close($s);
    
    # return file
    return $downloaded_page;
    }

/***********************************************************************
mkpath($path)                                                           
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Creates a complete directory structure                          
INPUT:                                                                  
        $file         Image file to download                            
        $ref          Referer value sent to target server               
OUTPUT:                                                                 
        A directory structure, as defined by $path is created           
                                                                        
 This script is a modified version of a script originally posted on www.php.net
***********************************************************************/
function mkpath($path)
    {
    # Make the slashes are all single and lean the right way
    $path=preg_replace('/(\/){2,}|(\\\){1,}/','/',$path); 

    # Make an array of all the directories in path
    $dirs=explode("/",$path);

    # Verify that each directory in path exist. Create it if it doesn't
    $path="";
    foreach ($dirs as $element)
        {
        $path.=$element."/";
        if(!is_dir($path))      // Directory verified here
            mkdir($path);       // Created if it doesn't exist
         }
    }
   
/***********************************************************************
download_images_for_page($target)                                       
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Downloads all images in the target web page and recreates the   
        original directory structure                                    
INPUT:                                                                  
        $target       Web page that references images                   
OUTPUT:                                                                 
        Downloads all images in the target web page and recreates the   
        original directory structure                                    
***********************************************************************/
function download_images_for_page($target)
    {
    echo "target = $target\n";
    
    # Download the web page
    $web_page = http_get($target, $referer="");
    
    # Update the target in case there was a redirection
    $target = $web_page['STATUS']['url'];
    
    # Strip file name off target for use as page base
    $page_base=get_base_page_address($target);
    
    # Identify the directory where iamges are to be saved
    $save_image_directory = "saved_images_".str_replace("http://", "", $page_base);
    
    # Parse the image tags
    $img_tag_array = parse_array($web_page['FILE'], "<img", ">");
    
    if(count($img_tag_array)==0)
        {
        echo "No images found at $target\n";
        exit;
        }

    # Echo the image source attribute from each image tag
    for($xx=0; $xx<count($img_tag_array); $xx++)
        {
        $image_path = get_attribute($img_tag_array[$xx],  $attribute="src");
        echo " image: ".$image_path;
        $image_url = resolve_address($image_path, $page_base);
        if(get_base_domain_address($page_base) == get_base_domain_address($image_url))
            {
            # Make image storage directory for image, if one doesn't exist
            $directory = substr($image_path, 0, strrpos($image_path, "/"));
            $directory = str_replace(":", "-", $directory );
            $image_path = str_replace(":", "-", $image_path );
            
            clearstatcache(); // clear cache to get accurate directory status
            if(!is_dir($save_image_directory."/".$directory))
                mkpath($save_image_directory."/".$directory);
            
            # Download the image, report image size
            $this_image_file =  download_binary_file($image_url, $ref="");
            echo " size: ".strlen($this_image_file);
            
            # Save the image
            if(stristr($image_url, ".jpg") || stristr($image_url, ".gif") || stristr($image_url, ".png"))
                {
                $fp = fopen($save_image_directory."/".$image_path, "w");
                fputs($fp, $this_image_file);
                fclose($fp);
                echo "\n"; 
                }
            }
        else
            {
            echo "\nSkipping off-domain image.\n";
            }
        }
    }
?>
