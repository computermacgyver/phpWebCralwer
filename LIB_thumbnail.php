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

/*
#-----------------------------------------------------------------------
#                                                                       
# LIB_thumbnail     JPG Thumbnailing routine                            
#                                                                       
#-----------------------------------------------------------------------
                                                                        
create_thumbnail($org_file, $new_file_name, $max_width, $max_height)    
-------------------------------------------------------------			
DESCRIPTION:															
		Creates a thumbnail image of a larger image                     
                                                                        
INPUT:																    
        $org_file                                                       
            The name of the original image file                         
                                                                        
        $new_file_name                                                  
            The name of the thumbnail image file                        
                                                                        
        $max_width                                                      
            The maximum width of the thumbnail file                     
        $max_height                                                     
            The maximum height of the thumbnail file                    
RETURNS:																
		Creates a thumbnail file with the file name $new_file_name      
#########################################################################
*/
function create_thumbnail($org_file, $new_file_name, $max_width, $max_height)
    {
	// Initialization
	$src_image_array = getimagesize ($org_file);
	$srcX = 1;
	$srcY = 1;
	$srcW = $src_image_array[0];
	$srcH = $src_image_array[1];
    
    # If images is wider than it is tall
    if($srcW>$srcH)
        {
        $dstX = 1;
		$dstY = 1;
        if($srcW>$max_width)
    	    $dstW = $max_width;
        else
    	    $dstW = $srcW;
    	$ratio = $srcW/$srcH; 
		$dstH  = $dstW/$ratio;
        }
    # Else if the images is taller than it is wide
    else
        {
        $dstX = 1;
		$dstY = 1;
        if($srcH>$max_width)
    	    $dstH = $max_width;
	    else
    	    $dstH = $srcH;
    	$ratio = $srcH/$srcW; 
        $dstW  = $dstH/$ratio;
        }
    $src_image = ImageCreateFromJPEG ($org_file); 
    $dst_image = imagecreatetruecolor($dstW, $dstH) or die ("Cannot Initialize new GD image stream");
    $result = imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH );
	$bool = imagejpeg ($dst_image, $new_file_name);		// create thumbnail image

    imagedestroy($src_image);
    imagedestroy($dst_image);
    return $bool;
    }
?>
