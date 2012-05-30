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
# LIB_mysql.php     MySQL database Routines                             
#                                                                       
#-----------------------------------------------------------------------
# FUNCTIONS                                                             
#                                                                       
#    insert()                                                           
#               Inserts a row into database,                            
#               as defined by a keyed array                             
#                                                                       
#    update()                                                           
#               Updates an existing row in a database,                  
#               as defined by a keyed array and a row index             
#                                                                       
#    exe_sql()                                                          
#               Executes a SQL command and return a result set          
#                                                                       
########################################################################

/***********************************************************************
MySQL Constants (scope = global)                                        
----------------------------------------------------------------------*/
define("MYSQL_ADDRESS", "");          // Define the IP address of your MySQL Server
define("MYSQL_USERNAME", "");         // Define your MySQL user name
define("MYSQL_PASSWORD", "");         // Define your MySQL password
define("DATABASE", "");               // Define your default database
define("SUCCESS", true);              // Successful operation flag
define("FAILURE", false);             // Failed operation flag

if(strlen(MYSQL_ADDRESS) + strlen(MYSQL_USERNAME) + strlen(MYSQL_PASSWORD) + strlen(MYSQL_ADDRESS) + strlen(DATABASE) == 0)
    echo "WARNING: MySQL not configured.<br>\n";
    
/***********************************************************************
Database connection routine (only used by routines in this library
----------------------------------------------------------------------*/
function connect_to_database()
	{
	return(mysql_connect(MYSQL_ADDRESS, MYSQL_USERNAME, MYSQL_PASSWORD));
	}

/***********************************************************************
insert($database, $table, $data_array)                                  
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Inserts a row into database as defined by a keyed array         
INPUT:                                                                  
        $database     Name of database (where $table is located)        
        $table        Table where row insertion occurs                  
        $data_array   A keyed array with defines the data to insert     
                      (i.e. $data_array['column_name'] = data)          
RETURNS                                                                 
        SUCCESS or FAILURE                                              
***********************************************************************/
function insert($database, $table, $data_array)
	{
    # Connect to MySQL server and select database
	$mysql_connect = connect_to_database();
	mysql_select_db ($database, $mysql_connect);
    
    # Create column and data values for SQL command
    foreach ($data_array as $key => $value) 
        {
        $tmp_col[] = $key;
        $tmp_dat[] = "'$value'";
        }
     $columns = join(",", $tmp_col);
     $data = join(",", $tmp_dat);
    
    # Create and execute SQL command
	$sql = "INSERT INTO ".$table."(".$columns.")VALUES(". $data.")";
    $result = mysql_query($sql, $mysql_connect);
    
    # Report SQL error, if one occured, otherwise return result
    if(mysql_error($mysql_connect))
        {
        echo "MySQL Update Error: ".mysql_error($mysql_connect);
        $result = "";
        }
    else
        {
        return $result;
        }
	}

/***********************************************************************
update($database, $table, $data_array, $key_column, $id)                
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Inserts a row into database as defined by a keyed array         
INPUT:                                                                  
        $database     Name of database (where $table is located)        
        $table        Table where row insertion occurs                  
        $data_array   A keyed array with defines the data to insert     
                      (i.e. $data_array['column_name'] = data)          
RETURNS                                                                 
        SUCCESS or FAILURE                                              
***********************************************************************/
function update($database, $table, $data_array, $key_column, $id)
	{
    # Connect to MySQL server and select database
	$mysql_connect = connect_to_database();
	$bool= mysql_select_db ($database, $mysql_connect);
	 	
    # Create column and data values for SQL command
	$setting_list="";
	for ($xx=0; $xx<count($data_array); $xx++)
		{
		list($key,$value)=each($data_array);
		$setting_list.= $key."="."\"".$value."\"";
		if ($xx!=count($data_array)-1)
			$setting_list .= ",";
		}

    # Create SQL command
	$sql="UPDATE ".$table." SET ".$setting_list." WHERE ". $key_column." = " . "\"" . $id . "\"";
    $result = mysql_query($sql, $mysql_connect);
    
    # Report SQL error, if one occured, otherwise return result
    if(mysql_error($mysql_connect))
        {
        echo "MySQL Update Error: ".mysql_error($mysql_connect);
        $result = "";
        }
    else
        {
        return $result;
        }
	}

/***********************************************************************
exe_sql($database, $sql)                                                
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Executes a SQL command and returns the result                   
INPUT:                                                                  
        $database     Name of database to operate on                    
        $sql          sql command applied to $database                  
RETURNS                                                                 
        An array containing the results of sql operation                
***********************************************************************/
function exe_sql($database, $sql)
	{
    # Connect to MySQL server and select database
	$mysql_connect = connect_to_database();
	mysql_select_db($database, $mysql_connect);
    
    # Execute SQL command
	$result = mysql_query($sql, $mysql_connect);
    
    # Report SQL error, if one occured
    if(mysql_error ($mysql_connect))
        {
        echo "MySQL ERROR: ".mysql_error($mysql_connect);
        $result_set = "";
        }
    else
        {
        # Fetch every row in the result set
        for ($xx=0; $xx<mysql_numrows($result); $xx++)
    	    {
		    $result_set[$xx] = mysql_fetch_array($result);
    	    }
        
        # If the result set has only one row, return a single dimension array
        if(sizeof($result_set)==1) 
            $result_set=$result_set[0];
        
        return $result_set;
        }
	}
?>