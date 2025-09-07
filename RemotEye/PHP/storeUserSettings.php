<?php

//session_start();

// This script may be used to store the User's Settings
// to the back-end.
// Each user is identified by his Authentication Token.

// Posted parameters:
$authToken = $_REQUEST['AuthenticationToken'];
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
	$authToken = stripslashes($authToken);
}

$userSettingsXml = $_REQUEST['UserSettings'];

$userID = $authToken;
$tokens = explode("\\", $authToken);
if ((strlen($authToken) > 0) && (count($tokens) == 3))
{
	$userID = $tokens[1];
}

$userSettingsFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/User_" . $userID . "_Settings.xml";
if ($file = fopen($userSettingsFilename, 'w')) 
{
	// Write the XML settings to the settings file
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
	{
		fprintf($file, stripslashes($userSettingsXml));
	}
	else
	{
		fprintf($file, $userSettingsXml);
	}
	
	fclose($file);
	
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<StoreUserSettingsResponse>\n";
	print "  <StoreStatus>Success</StoreStatus>\n";	
	print "</StoreUserSettingsResponse>\n";
}
else
{
	// Unable to create the settings file --> Report failure
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<StoreUserSettingsResponse>\n";
	print "  <StoreStatus>Failure</StoreStatus>\n";	
	print "</StoreUserSettingsResponse>\n";
}

?>