<?php

session_start();
session_write_close();

// This script may be used to tunnel License Manager requests through
// HTTP: the 'licenseManagerURL' applet parameter shall be used to point
// to this script.

// Read the request posted by the RemotEye client
$licManReq = $_POST['LicManReq'];
$licManPort = $_POST['LicManPort'];
$authToken = $_POST['AuthenticationToken'];

header('Content-Type: text/plain');

$errno = 0;
$errdesc = "";

$totalBytesWritten = 0;

// Try to connect to the License Manager
if ($fp = fsockopen("localhost", $licManPort, $errno, $errdesc, 5))
{
	// Socket connection succeeded
	
	// Send the request to the License Manager application
	fwrite($fp, $licManReq, strlen($licManReq));
	fflush($fp);
		
	// Read and forward response to the caller
	while (true) 
	{
		$tmpLine = fread ($fp, 256);
		$tmpLineLen = strlen($tmpLine);
		if ($tmpLineLen > 0)
		{
			print $tmpLine;
			$totalBytesWritten += $tmpLineLen;
		}
		
		if (($tmpLineLen <= 0) && feof($fp))
		{
			break;
		}
	}
  
  // Flush the output buffers
  $oldErrorRepLevel = error_reporting(E_ERROR);
	ob_flush();
	flush(); 
	error_reporting($oldErrorRepLevel);
  
  // Close socket
  fclose($fp);
}
else
{
	// Failed to open socket
	error_log("Failed to connect to License Manager (port $licManPort)");
	
	// Send an unrecognized answer to the caller
	print("*");
	
	// Flush the output buffers
  $oldErrorRepLevel = error_reporting(E_ERROR);
	ob_flush();
	flush(); 
	error_reporting($oldErrorRepLevel);
}

exit();
?>
