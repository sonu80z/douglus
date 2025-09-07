<?php

//session_start();

// Lower the error reporting level
$oldErrRepLevel = error_reporting(E_ERROR);

// This script may be used to update the status of a study
// on the back-end. The following study status flags are currently
// supported: READ, DICTATED, TRANSCRIBED, SIGNED.

// Posted parameters:
$authToken = $_POST['AuthenticationToken'];
$queryType = $_POST['QueryType'];
$studyInstUID = $_POST['StudyInstanceUID'];

$updateStatus = "SUCCESS";
$updateStatusDetails = "";

$bDeflatedInfo = false;
if (isset($_POST['Content-Encoding']))
{
	$bDeflatedInfo = $_POST['Content-Encoding'] == "deflate" ? true : false;
}
// Update or get the study status on the back-end...
// ...
// This normally implies an update operation on a 
// back-end database.
// For sake of simplicity, here the status of each study
// is maintained by using files with appropriate name and
// extensions.

$studyRead = "unknown";
if (isset($_POST['StudyStatusRead']))
{
	$studyRead = $_POST['StudyStatusRead'];
	
	if (strcasecmp($studyRead, "true") == 0)
	{
		// Create an empty ".read" file
		// WARNING: the subdirectory "Storage" located in this
		// script's directory must already exist.
		$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".read";
		$fp = fopen($filename, "w");
		fclose($fp);
	}
}

$studyDictated = "unknown";
if (isset($_POST['StudyStatusDictated']))
{
	$studyDictated = $_POST['StudyStatusDictated'];
	
	if (strcasecmp($studyDictated, "true") == 0)
	{
		if (isset($_FILES['UploadedVocReportFile']))
		{
			// Build the filename of the WAV file to be stored
			// WARNING: the subdirectory "Storage" located in this
			// script's directory must already exist.
			$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".wav";
			if ($bDeflatedInfo) $filename .= ".deflated";
			if (move_uploaded_file($_FILES['UploadedVocReportFile']['tmp_name'], $filename) == false)
			{
				// Update of status failed
				$updateStatus = "FAILURE";
			}
			else if ($bDeflatedInfo)
			{
				$deflatedInput = fopen($filename, 'rb')	;
				$deflateParams = array('level' => 5, 'window' => 15, 'memory' => 5);
		    $filterResource = stream_filter_prepend($deflatedInput, 'zlib.inflate', STREAM_FILTER_READ, $deflateParams);
		
		    if (is_resource($deflatedInput))
		    {
		      $outFile = fopen(str_replace(".deflated", "", $filename), "w+b");
		      while(!feof($deflatedInput))
		      {
		        $buffer = fread($deflatedInput, 32 * 1024);
		        fwrite($outFile, $buffer);
		        fflush($outFile);
		      }
		      stream_filter_remove($filterResource);
		      fclose($deflatedInput);
		      fclose($outFile);
		      unlink($filename);
		    }
		    else
		    {
		      $updateStatus = "FAILURE";
		    }					
			}
		}
	}
}

$studyTranscribed = "unknown";
$transcribedReport = "";
if (isset($_POST['StudyStatusTranscribed']))
{
	$studyTranscribed = $_POST['StudyStatusTranscribed'];
	if (strcasecmp($studyTranscribed, "true") == 0)
	{
		// Build the filename of the TXT file to be stored
		// WARNING: the subdirectory "Storage" located in this
		// script's directory must already exist.
		$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".txt";
		if (isset($_POST['TranscribedReport']))
		{
			$transcribedReport = $_POST['TranscribedReport'];
			
			$fp = fopen($filename, "w");
			fwrite($fp, $transcribedReport, strlen($transcribedReport));
			fclose($fp);
		}
	}
}

$studySigned = "unknown";
if (isset($_POST['StudyStatusSigned']))
{
	$studySigned = $_POST['StudyStatusSigned'];
	
	if (strcasecmp($studySigned, "true") == 0)
	{
		// Create an empty ".sign" file
		// WARNING: the subdirectory "Storage" located in this
		// script's directory must already exist.
		$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".sign";
		$fp = fopen($filename, "w");
		fclose($fp);
	}
}

$studyLockedForRead = "unknown";
if (isset($_POST['StudyStatusLockedForReading']))
{
	$studyLockedForRead = $_POST['StudyStatusLockedForReading'];

  $filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $studyInstUID . ".lockRead";

	if (strcasecmp($studyLockedForRead, "true") == 0)
	{
		// Create a ".lockRead" file, containing the AuthenticationToken
		// WARNING: the subdirectory "Storage" located in this
		// script's directory must already exist.

    // First of all, check if the ".lockRead" file already exists
    if (file_exists($filename))
    {
      // File already exists --> check if it was locked by the same user
      $fp = fopen($filename, "r");
      $lockedAuthToken = fgets($fp);
      fclose($fp);
      if (strcmp($lockedAuthToken, $authToken) == 0)
      {
        // The study was already locked by the same user --> OK
        // just touch the file
        touch($filename);
      }
      else
      {
        $updateStatus = "FAILURE";
        $updateDetails = "AlreadyLocked";
      }
    }
    else
    {
      // Lock file on study does not exist yet --> create it!
      $fp = fopen($filename, "w");
      fprintf($fp, "%s", $authToken);
      fclose($fp);
    }
	}
  else if (strcasecmp($studyLockedForRead, "false") == 0)
	{
		// Delete the ".lockRead" file
		unlink($filename);
	}
}

header('Content-Type: text/xml');    
print "<?xml version=\"1.0\"?>\n";

print "<UpdateStudyInfoResponse>\n";

print "  <UpdateStatus>" . $updateStatus . "</UpdateStatus>\n";
print "  <UpdateStatusDetails>" . $updateStatusDetails . "</UpdateStatusDetails>\n";

// The following are for verification only...
print "  <QueryType>" . $queryType . "</QueryType>\n";
print "  <StudyInstanceUID>" . $studyInstUID . "</StudyInstanceUID>\n";
print "  <StudyStatusRead>" . $studyRead . "</StudyStatusRead>\n";
print "  <StudyStatusDictated>" . $studyDictated . "</StudyStatusDictated>\n";
print "  <StudyStatusTranscribed>" . $studyTranscribed . "</StudyStatusTranscribed>\n";
print "  <StudyStatusSigned>" . $studySigned . "</StudyStatusSigned>\n";
print "  <TranscribedReport>" . $transcribedReport . "</TranscribedReport>\n";

print "</UpdateStudyInfoResponse>\n";

// Restore the old Error Reporting Level
error_reporting($oldErrRepLevel);

?>