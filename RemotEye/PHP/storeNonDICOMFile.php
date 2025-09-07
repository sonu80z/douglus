<?php

//session_start();

// This sample PHP script stores the posted non-DICOM file to a file on server's disk.
// The binary content of the non-DICOM file is posted as 'multipart/form-data'.

// Lower the error reporting level
$oldErrRepLevel = error_reporting(E_ERROR);

// Read posted parameters
$authenticationToken = $_POST['AuthenticationToken'];
$exportOpID = $_POST['ExportOpID'];
$isLast = $_POST['IsLastOfOp'];
$mimeType = $_POST['MimeType'];
$operationName = "";
if (isset($_POST['OperationName']))
{
	$operationName = $_POST['OperationName'];

}

if (strcmp($operationName, "ExportImage") == 0)
{
	// The client is exporting a non-DICOM image to the server...
	
	// Get some useful POST parameters
	$sopInstanceUID = $_POST['SOPInstanceUID'];
	$sopClassUID = $_POST['SOPClassUID'];
	$patientName = $_POST['PatientName'];
	$patientBirthdate = $_POST['PatientBirthdate'];
	$frameNumber = $_POST['FrameNumber'];
	
	$bDeflatedInstance = false;
	if (isset($_POST['Content-Encoding']))
	{
		$bDeflatedInstance = $_POST['Content-Encoding'] == "deflate" ? true : false;
	}
	
	// Several other parameters are POSTed by the client, which
	// are not used by this sample. Please refer to the Integration
	// Manual for further details.
	
	// Build the filename of the File to be stored
	// WARNING: the subdirectory "Storage" located in this
	// script's directory must already exist.
	$filename = dirname($_SERVER['SCRIPT_FILENAME']) . "/Storage/" . $exportOpID . "/";
	if (! file_exists($filename))
	{
		mkdir($filename, 0777);
	}
	
	// Build the filename and extension depending on the format
	// of the exported image (MIME type)
	if (strcmp($mimeType, "image/jpeg") == 0)
	{
		// We are storing a JPEG image
		$filename .= $sopInstanceUID . "_" . $frameNumber . ".jpg";
	}
	else if (strcmp($mimeType, "image/png") == 0)
	{
		// We are storing a PNG image
		$filename .= $sopInstanceUID . "_" . $frameNumber . ".png";
	}
	else if (strcmp($mimeType, "image/jpeg2000") == 0)
	{
		// We are storing a JPEG-2000 image
		$filename .= $sopInstanceUID . "_" . $frameNumber . ".j2k";
	}
	else if (strcmp($mimeType, "video/avi") == 0)
	{
		// We are storing an AVI movie
		$filename .= $sopInstanceUID . "_" . $frameNumber . ".avi";
	}
	else
	{
		// We are storing an unknown file
		$filename .= $sopInstanceUID . "_" . $frameNumber . ".unknown";
	}
	
	if ($bDeflatedInstance)
	{
		$filename .= ".deflated";
	}
	
	if (move_uploaded_file($_FILES['UploadedNonDICOMFile']['tmp_name'], $filename) == false)
	{
		// Report failed storage to the caller (XML response)
		header('Content-Type: text/xml');    
		print "<?xml version=\"1.0\"?>\n";
		print "<StoreNonDICOMFileResponse>\n";
		print "  <ExportResult>Failure</ExportResult>\n";
		print "  <ErrorCode>1</ErrorCode>\n";
		print "  <ErrorDetails>Unable to save uploaded file</ErrorDetails>\n";
		print "</StoreNonDICOMFileResponse>\n";
	}
	else
	{
		// Report success
		header('Content-Type: text/xml');    
		print "<?xml version=\"1.0\"?>\n";
		print "<StoreNonDICOMFileResponse>\n";
		print "  <ExportResult>Success</ExportResult>\n";
		print "</StoreNonDICOMFileResponse>\n";
	}
}
else
{
	// Unknown Store Non-DICOM operation
	// Report failure to the caller (XML response)
	header('Content-Type: text/xml');    
	print "<?xml version=\"1.0\"?>\n";
	print "<StoreNonDICOMFileResponse>\n";
	print "  <ExportResult>Failure</ExportResult>\n";
	print "  <ErrorCode>0</ErrorCode>\n";
	print "  <ErrorDetails>Unknown 'OperationName' [" . $operationName . "]</ErrorDetails>\n";
	print "</StoreNonDICOMFileResponse>\n";
}

// Restore the old Error Reporting Level
error_reporting($oldErrRepLevel);

?>