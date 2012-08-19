<?
error_reporting( E_ALL );
date_default_timezone_set('Europe/London');

include_once("CONFIG_db.php");

include_once("LIB_db_functions.php");

$usage="Usage:\nset_status.php STOP\tStop crawler after current page if running\n".
			"set_status.php OK\tAllow crawler to restart when next executed.\n";

$strStopSQL="UPDATE tblConfig SET strValue='STOP' WHERE strName='CrawlerStatus'";
$strOKSQL="UPDATE tblConfig SET strValue='OK' WHERE strName='CrawlerStatus'";

if ($argc!=2) {
	print "Please enter an argument\n$usage";
	exit();
}

$command=strtoupper($argv[1]);
if ($command!="OK" && $command!="STOP") {
	print "Invalid argument\n$usage";
	exit();
}

print "Connecting to database...";
db_connect();
print "OK\n";

if ($command=="STOP") {
	print "Stopping crawler....";
	db_run_query($strStopSQL);
	print "OK\n";
	print "Crawler should stop gracefully after current page\n";
} else {
	print "Updating status to OK....";
	db_run_query($strOKSQL);
	print "OK\n";
}

print "Disconnecting....";
db_close();
print "OK\n";

?>
