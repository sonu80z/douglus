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
header('Original-Content-Length: ' . filesize($filename));
header('Content-Transfer-Encoding: binary');
header('Content-Encoding: deflate');
header('Transfer-Encoding: chunked');

// Set DEFLATE compression filter on the output stream
$deflateParams = array('level' => 9, 'window' => 15, 'memory' => 9);

$deflatedStdOut = fopen('php://output', 'w');
$deflateFilterStdOut = stream_filter_append($deflatedStdOut, 'zlib.deflate', STREAM_FILTER_WRITE, $deflateParams);

if ($file = fopen($filename, 'rb')) 
{
	while(!feof($file)) 
	{
		// Allow sufficient execution time to the script:
		// we must go at least at 1 KB / s...
		set_time_limit(32);
		
		$buffer = fread($file, 32 * 1024);
		//print $buffer;
		fwrite($deflatedStdOut, $buffer);
		// Flush output buffers
		ob_flush();
		//flush(); 
		fflush($deflatedStdOut);
	}
}

stream_filter_remove($deflateFilterStdOut); 
fclose($deflatedStdOut);

// Restore the old Error Reporting Level
error_reporting($oldErrRepLevel);

exit();

?> 

