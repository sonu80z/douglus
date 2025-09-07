<?php
// This script may be used to query the back-end for existing SR Templates,
// for a specific user.
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

// Get the server URL
$url = "";
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) 
{
  $url = "https://" . $_SERVER['HTTP_HOST'];
}
else 
{
  $url = "http://" . $_SERVER['HTTP_HOST'];
}
$url .= $_SERVER['REQUEST_URI'];
$url = substr($url, 0, strlen($url) - strlen(basename(__FILE__)));

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

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: text/xml');

print "<?xml version=\"1.0\"?>\n\n";
print "<QuerySRTemplatesResponse>\n";
print "  <SRTemplates>\n";

$srTemplDirPath = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/Templates_" . stringToFilename($userID);
$srTemplDirRes = opendir($srTemplDirPath);
if ($srTemplDirRes !== false)
{
  while (($curFilename = readdir($srTemplDirRes)) !== false)
  {
    if(is_file($srTemplDirPath . "/" . $curFilename))
    {
      $curFilenameNoExt = basename($curFilename, ".xml");
      $curTemplName = filenameToString($curFilenameNoExt);
      $curTemplRetrURL = $url . "RetrieveSRTemplate.php?AuthenticationToken=" . urlencode($authToken) . "&SRTemplateName=" . urlencode($curTemplName);
      
      print "    <SRTemplate>\n";
      print "      <Name>" . htmlspecialchars($curTemplName, ENT_QUOTES) . "</Name>\n";
      print "      <URL>" . htmlspecialchars($curTemplRetrURL, ENT_QUOTES) . "</URL>\n";
      print "    </SRTemplate>\n";
    }
  }

  closedir($srTemplDirRes);
}
else
{
  print "Unable to read directory $srTemplDirPath\n";
}

print "  </SRTemplates>\n";
print "</QuerySRTemplatesResponse>\n";

?>