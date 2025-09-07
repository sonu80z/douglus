<?php

//session_start();

// This script may be used to retrieve the User's Settings
// from the back-end.
// Each user is identified by his Authentication Token.

// Posted parameters:
$authToken = $_REQUEST['AuthenticationToken'];
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
	$authToken = stripslashes($authToken);
}

$userID = $authToken;
$tokens = explode("\\", $authToken);
if ((strlen($authToken) > 0) && (count($tokens) == 3))
{
	$userID = $tokens[1];
}

$userSettingsFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/User_" . $userID . "_Settings.xml";

if (file_exists($userSettingsFilename))
{
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-Type: text/xml');    
	header('Content-Length: ' . filesize($userSettingsFilename));
	
	ignore_user_abort(true);
	if ($file = fopen($userSettingsFilename, 'rb')) 
	{
		while ((!feof($file)) && (connection_status() == 0))
		{
			// Allow sufficient execution time to the script:
			// we must go at least at 1 KB / s...
			set_time_limit(32);
					
			$buffer = fread($file, 32 * 1024);
	    print $buffer;
		}
		
		fclose($file);
	}
}
else
{
	// Return an empty User Settings document
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<UserSettings>\n";	
	print "</UserSettings>\n";
}

?>