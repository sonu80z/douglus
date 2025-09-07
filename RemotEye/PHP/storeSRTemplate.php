<?php
// This script may be used to store SR Templates
// to the back-end.
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
$srTemplateOp = $_REQUEST['Operation'];
$srTemplateXml = "";
if (isset($_REQUEST['SRTemplate']))
{
	$srTemplateXml = $_REQUEST['SRTemplate'];
}
$srTemplateOpSuccess = false;

$userID = $authToken;
$tokens = explode("\\", $authToken);
if ((strlen($authToken) > 0) && (count($tokens) == 3))
{
	$userID = $tokens[1];
}

$srTemplDirPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/Templates_" . stringToFilename($userID);
if (! file_exists($srTemplDirPath))
{
  mkdir($srTemplDirPath);
}

$srTemplFilePathname = $srTemplDirPath . "/" . stringToFilename($srTemplateName) . ".xml";

if (strcasecmp($srTemplateOp, "Store") == 0)
{
	// We must STORE a new SR Template
	if ($file = fopen($srTemplFilePathname, 'w'))
	{
		// Write the XML settings to the settings file
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			fprintf($file, stripslashes($srTemplateXml));
		}
		else
		{
			fprintf($file, $srTemplateXml);
		}
		
		fclose($file);
		
		$srTemplateOpSuccess = true;
	}
}
else if (strcasecmp($srTemplateOp, "Delete") == 0)
{
	// We must DELETE an existing SR Template
	$srTemplateOpSuccess = unlink($srTemplFilePathname);
}

if ($srTemplateOpSuccess)
{
	header('Content-Type: text/xml');
	print "<?xml version=\"1.0\"?>\n";
	
	print "<StoreSRTemplateResponse>\n";
	print "  <StoreStatus>Success</StoreStatus>\n";
	print "</StoreSRTemplateResponse>\n";
}
else
{
	// Unable to create the settings file --> Report failure
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<StoreSRTemplateResponse>\n";
	print "  <StoreStatus>Failure</StoreStatus>\n";	
	print "</StoreSRTemplateResponse>\n";
}

?>