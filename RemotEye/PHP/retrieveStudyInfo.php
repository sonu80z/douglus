<?php

//session_start();

// This script may be used to retrieve the status of a study
// from the back-end. The following study status flags are currently
// supported: READ, DICTATED, TRANSCRIBED, SIGNED.
// Depending on the the posted parameters, additional information
// (i.e., the dictated report, or the transcribed report text)
// must also be returned by this script.

// Posted parameters:
$authToken = $_POST['AuthenticationToken'];
$queryType = $_POST['QueryType'];
$studyInstUID = $_POST['StudyInstanceUID'];
$retrieveDictReport = $_POST['RetrieveDictatedReport'];
$retrieveTranscrReport = $_POST['RetrieveTranscribedReport'];

// Get the base (dir) URL of this script
$url = "";
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on"))
{
	$url = "https://" . $_SERVER['HTTP_HOST'];
}
else
{
	$url = "http://" . $_SERVER['HTTP_HOST'];
}

// Extract the directory name from the script path (relative to doc root)
// note: we do not use "dirname()" since it seems to be unreliable.
$tokens = explode("/", $_SERVER['PHP_SELF']);
for ($i = 0; $i < count($tokens) - 1; $i++)
{
    if (strlen($tokens[$i]))
    {
        $url .= "/" . $tokens[$i];
    }
}

// Retrieve the study status on the back-end...
// ...
// Normally, this operation requires an access to
// the back-end database.

// For sake of simplicity, here the status of each study
// is maintained by using files with appropriate name and
// extensions.
$studyRead = "false";
$studyDictated = "false";
$studyTranscribed = "false";
$studySigned = "false";

$studyReadFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".read";
$txtReportFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".txt";
$vocReportFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".wav";
$studySignedFilename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".sign";

// Verify existence of various files,
// and set the status flags accordingly
if (file_exists($studyReadFilename))
{
	$studyRead = "true";
}
if (file_exists($vocReportFilename))
{
	$studyDictated = "true";
}
if (file_exists($txtReportFilename))
{
	$studyTranscribed = "true";
}
if (file_exists($studySignedFilename))
{
	$studySigned = "true";
}

if (strcasecmp($retrieveDictReport, "true") == 0)
{
	// If the client asked to return the dictated report,
	// then return the binary content of the audio file
	// of the voice recording (OLD MECHANISM)
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-Type: audio/wav');    
	header('Content-Length: ' . filesize($vocReportFilename));
	header('Content-Transfer-Encoding: binary');
	
	ignore_user_abort(true);
	if ($file = fopen($vocReportFilename, 'rb')) 
	{
		while ((!feof($file)) && (connection_status() == 0))
		{
			// Allow sufficient execution time to the script:
			// we must go at least at 1 KB / s...
			set_time_limit(32);
					
			$buffer = fread($file, 32 * 1024);
			
	    print $buffer;
		}
		
		$oldErrorRepLevel = error_reporting(E_ERROR);
		if (connection_status() == 0)
		{
			ob_flush();
			flush(); 
		}
		error_reporting($oldErrorRepLevel);
		
	  fclose($file);
	}
}
else
{
	// In all other cases, return an XML document containing
	// the study status flags
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	
	print "<RetrieveStudyInfoResponse>\n";
	
	print "  <StudyStatusRead>" . $studyRead . "</StudyStatusRead>\n";
	print "  <StudyStatusDictated>" . $studyDictated . "</StudyStatusDictated>\n";
	print "  <StudyStatusTranscribed>" . $studyTranscribed . "</StudyStatusTranscribed>\n";
	print "  <StudyStatusSigned>" . $studySigned . "</StudyStatusSigned>\n";
	
	if (strcasecmp($studyTranscribed, "true") == 0)
	{
		// Study is transcribed, i.e., a text report is available
		print "  <TextReportMIMEType>text/plain</TextReportMIMEType>\n";	
		print "  <TextReportURL>$url/Storage/$studyInstUID.txt</TextReportURL>\n";	
	}
	if (strcasecmp($studyDictated, "true") == 0)
	{
		// Study is dictated, i.e., a voice report is available
		print "  <VoiceReportMIMEType>audio/wav</VoiceReportMIMEType>\n";	
		print "  <VoiceReportURL>$url/Storage/$studyInstUID.wav</VoiceReportURL>\n";	
	}
	
	// The following are for verification only...
	print "  <QueryType>" . $queryType . "</QueryType>\n";
	print "  <StudyInstanceUID>" . $studyInstUID . "</StudyInstanceUID>\n";
	
	print "</RetrieveStudyInfoResponse>\n";
}

?>