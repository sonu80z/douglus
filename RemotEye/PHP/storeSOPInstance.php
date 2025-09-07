<?php

//session_start();

// This sample PHP script stores the posted SOP Instance in a file on server's disk.
// The binary content of the SOP Instance file is posted as 'multipart/form-data'.

// Lower the error reporting level
$oldErrRepLevel = error_reporting(E_ERROR);

// Read posted parameters
$authenticationToken = $_POST['AuthenticationToken'];
$exportOpID = $_POST['ExportOpID'];
$isLast = $_POST['IsLastOfOp'];
$sopInstanceUID = $_POST['SOPInstanceUID'];
$sopClassUID = $_POST['SOPClassUID'];
$patientName = $_POST['PatientName'];
$patientBirthdate = $_POST['PatientBirthdate'];

$bDeflatedInstance = false;
if (isset($_POST['Content-Encoding']))
{
	$bDeflatedInstance = $_POST['Content-Encoding'] == "deflate" ? true : false;
}

if (isset($_POST['ReferencedSOPInstances']))
{
	// This posted variable is only available when storing
	// DICOM PS objects
	$referencedSOPInstances = $_POST['ReferencedSOPInstances']; // FFU
}

// Several other parameters are POSTed by the client, which
// are not used by this sample. Please refer to the Integration
// Manual for further details.

// Build the filename of the SOP Instance to be stored
// WARNING: the subdirectory "Storage" located in this
// script's directory must already exist.
$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $exportOpID . "/";
if (! file_exists($filename))
{
	mkdir($filename, 0777);
}

$filename .= $sopInstanceUID . "_" . $isLast;
if (strcmp($sopClassUID, "1.2.840.10008.5.1.4.1.1.11.1") == 0)
{
	// The SOP Instance we are storing is a Presentation State object
	$filename .= ".pre";
}
else if (strcmp($sopClassUID, "1.2.840.10008.5.1.4.1.1.88.59") == 0)
{
	// The SOP Instance we are storing is a Key Object Selection object (Key Image)
	$filename .= ".ki";
}
else
{
	// The SOP Instance we are storing is of another DICOM SOP Class
	$filename .= ".dcm";
}
if ($bDeflatedInstance)$filename .= ".deflated";

if (move_uploaded_file($_FILES['UploadedDICOMFile']['tmp_name'], $filename) == false)
{
	// Report failed storage to the caller
	print "FAILURE";
	
	// Restore the old Error Reporting Level
	error_reporting($oldErrRepLevel);

	exit();
}
else
{
	// If it is a deflated file we need to inflate it.
	if ($bDeflatedInstance)
	{
		$deflatedInput = fopen($filename, 'rb')	;
		$deflateParams = array('level' => 9, 'window' => 15, 'memory' => 9);
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
			echo "SUCCESS";
			// Restore the old Error Reporting Level
			error_reporting($oldErrRepLevel);
			exit();

		}
		else
		{
		  echo "FAILURE";
		  // Restore the old Error Reporting Level
			error_reporting($oldErrRepLevel);
			exit();
		}
	}
	else
	{
		// Output the "SUCCESS" string upon successful completion
		// of the storage operation
		print "SUCCESS";
		// Restore the old Error Reporting Level
		error_reporting($oldErrRepLevel);

		exit();
	}
}
?>