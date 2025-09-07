<?php
// This script may be used to retrieve a SR Template
// from the back-end.
// Each user is identified by his Authentication Token.

function stringToFilename($string)
{
  $nonValidChar = array("\\","/",":","*","?","\"","<",">","|","'","&"," ");
  $subChar = array("%5C","%2F","%3A","%2A","%3F","%22","%3C","%3E","%7C","%27","%26","%20");
  return str_replace($nonValidChar,$subChar,$string);
}

function filenameToString($string)
{
  $nonValidChar = array("%5C","%2F","%3A","%2A","%3F","%22","%3C","%3E","%7C","%27","%26","%20");
  $subChar = array("\\","/",":","*","?","\"","<",">","|","'","&"," ");
  return str_replace($nonValidChar,$subChar,$string);
}

// Posted parameters:
$authToken = $_REQUEST['AuthenticationToken'];
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
	$authToken = stripslashes($authToken);
}

$srTemplateName = $_REQUEST['SRTemplateName'];

$userID = $authToken;
$tokens = explode("\\", $authToken);
if ((strlen($authToken) > 0) && (count($tokens) == 3))
{
	$userID = $tokens[1];
}

$srTemplDirPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/Templates_" . stringToFilename($userID);
$srTemplFilePathname = $srTemplDirPath . "/" . stringToFilename($srTemplateName) . ".xml";
if (file_exists($srTemplFilePathname))
{
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-Type: text/xml');
	header('Content-Length: ' . filesize($srTemplFilePathname));

	ignore_user_abort(true);
	if ($file = fopen($srTemplFilePathname, 'rb'))
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

	print "<SRTemplate>\n";
	print "</SRTemplate>\n";
}

?>