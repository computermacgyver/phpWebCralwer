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
# LIB_rss                                                               
#                                                                       
# This library provides routines useful when working with RSS feeds     
#                                                                       
#-----------------------------------------------------------------------
# FUNCTIONS                                                             
#                                                                       
# download_parse_rss($target)                                           
#		    Downloads and parses rss data                               
#                                                                       
# display_rss_array($rss_array)                                         
#		    Displays a parsed news feed                                 
#                                                                       
# strip_cdata_tags()                                                    
#           Removes cdata[] tags from strings                           
#                                                                       
#-----------------------------------------------------------------------

/***********************************************************************
download_parse_rss($target)     						                
-------------------------------------------------------------			
DESCRIPTION:															
		Downloads and parses a RSS web site                             
INPUT:																    
		$target                                                         
            The web address of the RSS feed                             
RETURNS:																
		The parsed RSS feed                                             
***********************************************************************/
function download_parse_rss($target)
    {
    # download tge rss page
    $news = http_get($target, "");
    
    # Parse title & copyright notice
    $rss_array['TITLE'] = return_between($news['FILE'], "<title>", "</title>", EXCL);
    $rss_array['COPYRIGHT'] = return_between($news['FILE'], "<copyright>", "</copyright>", EXCL);

    # Parse the items
    $item_array = parse_array($news['FILE'], "<item>", "</item>");
    for($xx=0; $xx<count($item_array); $xx++)
        {
        $rss_array['ITITLE'][$xx] = return_between($item_array[$xx], "<title>", "</title>", EXCL);
        $rss_array['ILINK'][$xx] = return_between($item_array[$xx], "<link>", "</link>", EXCL);
        $rss_array['IDESCRIPTION'][$xx] = return_between($item_array[$xx], "<description>", "</description>", EXCL);
        $rss_array['IPUBDATE'][$xx] = return_between($item_array[$xx], "<pubDate>", "</pubDate>", EXCL);
        }

    return $rss_array;
    }

/***********************************************************************
display_rss_array($rss_array)     						                
-------------------------------------------------------------			
DESCRIPTION:															
		Displays parsed RSS data                                        
INPUT:																    
		$target                                                         
            The web address of the RSS feed                             
RETURNS:																
		Sends results to the display device                             
***********************************************************************/
function display_rss_array($rss_array)
    {?>
    <table border="0">
        <!-- Display the article title and copyright notice -->    
        <tr><td><font size="+1"><b><?echo strip_cdata_tags($rss_array['TITLE'])?></b></font></td></tr>
        <tr><td><?echo strip_cdata_tags($rss_array['COPYRIGHT'])?></td></tr>

        <!-- Display the article descriptions and links -->    
        <?for($xx=0; $xx<count($rss_array['ITITLE']); $xx++)
            {?>
            <tr>
                <td>
                    <a href="<?echo strip_cdata_tags($rss_array['ILINK'][$xx])?>">
                        <b><?echo strip_cdata_tags($rss_array['ITITLE'][$xx])?></b>
                    </a>
                </td>
            </tr>
            <tr>
                <td><?echo strip_cdata_tags($rss_array['IDESCRIPTION'][$xx])?></td>
            </tr>
            <tr>
                <td><font size="-1"><?echo strip_cdata_tags($rss_array['IPUBDATE'][$xx])?></font></td>
            </tr>
          <?}?>
    </table>
  <?}

/***********************************************************************
strip_cdata_tags($string)                                               
-------------------------------------------------------------			
DESCRIPTION:															
		Removes CDDATA tags from a string                               
                                                                        
INPUT:																    
		$string                                                         
            Text containing CDDATA tags                                 
RETURNS:																
		Returns a string free of CDDATA tags                            
***********************************************************************/
function strip_cdata_tags($string)
    {
    # Strip XML CDATA characters from all array elements
    $string = str_replace("<![CDATA[", "", $string);
    $string = str_replace("]]>", "", $string);
    return $string;
    }  
  ?>
  