<?php 

//session_start();

// This script may be used to retrieve a given SOP Instance
// (i.e., DICOM Image or Presentation State) and send it
// to the client.
// The SOP Instance may be stored on the server's hard disk
// (as in this example) or in a database.
// The 'SOP Instance UID' of the instance to be returned is
// passed to this script as a GET parameter.

$sopInstUID = $_GET['SOPInstanceUID'];
$startOffset = 0;
if (isset($_GET['startOffset']))
{
	$startOffset = $_GET['startOffset'];	
}
//$callingAE = $_GET['CallingAETitle'];
//$calledAE = $_GET['CalledAETitle'];
//$authToken = $_GET['AuthenticationToken'];

$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $sopInstUID . ".dcm";

// Lower the error reporting level
$oldErrRepLevel = error_reporting(E_ERROR);

// Stream the DICOM file to the HTTP response
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/dicom');    
header('Content-Length: ' . filesize($filename));
header('Content-Transfer-Encoding: binary');

if ($file = fopen($filename, 'rb')) 
{
	if ($startOffset > 0)
	{
		fseek($file, $startOffset, SEEK_SET);
	}
	
	while(!feof($file)) 
	{
		// Allow sufficient execution time to the script:
		// we must go at least at 1 KB / s...
		set_time_limit(32);
		
		$buffer = fread($file, 32 * 1024);
    print $buffer;
    
    // Flush output buffers
    ob_flush();
		flush(); 
	}
  
  fclose($file);
}

// Restore the old Error Reporting Level
error_reporting($oldErrRepLevel);

exit();

?> 

