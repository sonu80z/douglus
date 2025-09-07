<?php

// This sample PHP script logs the notified client action to a simple text 'log'
// file on the server's hard drive

// Read posted parameters
$authenticationToken = $_REQUEST['AuthenticationToken'];
$actionName = $_REQUEST['ActionName'];
$actionLevel = $_REQUEST['ActionLevel'];
$actionRelatedIDs = $_REQUEST['ActionRelatedIDs'];
$actionDetails = $_REQUEST['ActionDetails'];

// Build the filename of the text file where this Client Action
// will be logged.
// WARNING: the subdirectory "Storage" located in this
// script's directory must already exist.
$logFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/ClientActions.log";

if ($file = fopen($logFilename, 'a')) 
{
	$dateString = date("Y-m-d G:i:s");
	fprintf($file, "%s | %s - %s - %s - %s\r\n", $dateString, $actionName, $actionLevel, $actionRelatedIDs, $actionDetails);

	fclose($file);
	
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<NotifyClientActionResponse>\n";
	print "  <NotifyStatus>Success</NotifyStatus>\n";	
	print "</NotifyClientActionResponse>\n";
}
else
{
	// Unable to create the log file --> Report failure
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<NotifyClientActionResponse>\n";
	print "  <NotifyStatus>Failure</NotifyStatus>\n";	
	print "</NotifyClientActionResponse>\n";
}

exit();

?>