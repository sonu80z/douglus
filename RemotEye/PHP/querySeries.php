<?php

//session_start();

// This script may be used to implement the Series-level query
// on the back-end: the search parameters are passed by the 
// RemotEye client as POST parameters.
// Please notice that this script will be called once per each
// Study returned by the Study-level query.

// Search parameters:
$authToken = $_POST['AuthenticationToken'];
$queryType = $_POST['QueryType'];
$patientID = $_POST['PatientID'];	// Single-value matching
$studyInstUID = $_POST['StudyInstanceUID']; // Single-value matching
$callingAE = $_POST['CallingAETitle'];
$calledAE = $_POST['CalledAETitle'];
$modality = $_POST['Modality'];
$issuerOfPatientID = @$_POST['IssuerOfPatientID'];

// ... perform a query on the back-end database to return
// all matching Series ...

// WARNING: the POSTed variables may contain wildcard characters,
// as specified by DICOM PS 3.4 for the Query-Retrieve SOP Class,
// hence some translations may be necessary before querying the
// database.

// Build the XML response, returning all Series matching
// the search parameters:
// this sample script just returns four fixed Series, with
// no access to database, but in the actual implementation
// the returned list will be dynamically generated on the 
// basis of the resultset of the DB query.

header('Content-Type: text/xml');    
print "<?xml version=\"1.0\"?>\n";

print "<SeriesLevelResponse>\n";

if ($studyInstUID == "1.3.6.1.4.1.18047.1.6.1999991394698164579")
{
  print "  <Series>\n";
  print "    <SeriesInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164580</SeriesInstanceUID>\n";
  print "    <Modality>MR</Modality>\n";
  print "    <SeriesNumber>101</SeriesNumber>\n";
  print "    <SeriesDescription>ABDOSCAN-STSURVEY/MST</SeriesDescription>\n";
  print "  </Series>\n";
}
else if ($studyInstUID == "1.3.6.1.4.1.18047.1.6.1999991394698376400")
{
  print "  <Series>\n";
  print "    <SeriesInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376401</SeriesInstanceUID>\n";
  print "    <Modality>MR</Modality>\n";
  print "    <SeriesNumber>101</SeriesNumber>\n";
  print "    <SeriesDescription>ABDOSCAN-STSURVEY/MST</SeriesDescription>\n";
  print "  </Series>\n";
}
else 
{
  print "  <Series>\n";
  print "    <SeriesInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333324</SeriesInstanceUID>\n";
  print "    <Modality>MR</Modality>\n";
  print "    <SeriesNumber>101</SeriesNumber>\n";
  print "    <SeriesDescription>ABDOSCAN-STSURVEY/MST</SeriesDescription>\n";
  print "  </Series>\n";
} 


print "</SeriesLevelResponse>\n";

?>