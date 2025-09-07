<?php

//session_start();

// This script may be used to implement the Study-level query
// on the back-end: the search parameters are passed by the 
// RemotEye client as POST parameters.
// Please notice that this script will be called once per each

// Search parameters:
$authToken = $_POST['AuthenticationToken'];
$queryType = $_POST['QueryType'];
$callingAE = $_POST['CallingAETitle'];
$calledAE = $_POST['CalledAETitle'];
$patientID = "";
$issuerOfPatientID = @$_POST['IssuerOfPatientID'];
$patientName = "";
$studyDescr = "";
$accessionNum = "";
$refPhysName = "";
$studyDate = "";
$studyRead = "";
$studyDictated = "";
$studyTranscribed = "";
$studyVerified = "";
$studyInstUIDs = @$_POST['StudyInstUIDs'];
$modality = $_POST['Modality'];

$patientID = $_POST['PatientID'];
$patientName = $_POST['PatientName'];
$studyDescr = $_POST['StudyDescription'];
$accessionNum = $_POST['AccessionNumber'];
$refPhysName = $_POST['RefPhysName'];
$studyDate = $_POST['StudyDate'];	

$studyRead = @$_POST['StudyStatusRead'];
$studyDictated = @$_POST['StudyStatusDictated'];
$studyTranscribed = @$_POST['StudyStatusTranscribed'];
$studyVerified = @$_POST['StudyStatusVerified'];



// ... perform a query on the back-end database to return
// all matching Studies ...

// WARNING: the POSTed variables may contain wildcard characters,
// and range matching, as specified by DICOM PS 3.4 for the 
// Query-Retrieve SOP Class, hence some translations may be 
// necessary before querying the database.

// Build the XML response, returning all Studies matching
// the search parameters:
// this sample script just returns three fixed Studies, with
// no access to database, but in the actual implementation
// the returned list will be dynamically generated on the 
// basis of the resultset of the DB query.

header('Content-Type: text/xml');    
print "<?xml version=\"1.0\"?>\n";

print "<StudyLevelResponse>\n";

if (strcasecmp($queryType, "find") == 0)
{
	print "  <Study>\n";
	print "    <PatientID>00001</PatientID>\n";
	print "    <IssuerOfPatientID>Issuer1</IssuerOfPatientID>\n";
	print "    <PatientName>Anon^Patient1</PatientName>\n";
	print "    <PatientBirthdate>19470213</PatientBirthdate>\n";
	print "    <PatientSex>M</PatientSex>\n";
	print "    <StudyInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164579</StudyInstanceUID>\n";
	print "    <StudyDescription>MRT Abdomen Abdomen</StudyDescription>\n";
	print "    <AccessionNumber>AccNum1</AccessionNumber>\n";
	print "    <RefPhysName>Ref^Phys</RefPhysName>\n";
	print "    <StudyDate>19100101</StudyDate>\n";
	print "    <StudyTime>111111</StudyTime>\n";
	print "    <StudyID></StudyID>\n";
	print "    <ModsInStudy>MR</ModsInStudy>\n";
	print "    <StudyStatusRead>true</StudyStatusRead>\n";
	print "    <StudyStatusDictated>true</StudyStatusDictated>\n";
	print "    <StudyStatusTranscribed>true</StudyStatusTranscribed>\n";
	print "    <StudyStatusVerified>false</StudyStatusVerified>\n";
	print "  </Study>\n";
	
	print "  <Study>\n";
	print "    <PatientID>00001</PatientID>\n";
	print "    <IssuerOfPatientID>Issuer1</IssuerOfPatientID>\n";
	print "    <PatientName>Anon^Patient1</PatientName>\n";
	print "    <PatientBirthdate>19470213</PatientBirthdate>\n";
	print "    <PatientSex>M</PatientSex>\n";
	print "    <StudyInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376400</StudyInstanceUID>\n";
	print "    <StudyDescription>MRT Abdomen Abdomen</StudyDescription>\n";
	print "    <AccessionNumber>AccNum3</AccessionNumber>\n";
	print "    <RefPhysName>Ref^Phys</RefPhysName>\n";
	print "    <StudyDate>19000101</StudyDate>\n";
	print "    <StudyTime>111111</StudyTime>\n";
	print "    <StudyID></StudyID>\n";
	print "    <ModsInStudy>MR</ModsInStudy>\n";
	print "    <StudyStatusRead>true</StudyStatusRead>\n";
	print "    <StudyStatusDictated>true</StudyStatusDictated>\n";
	print "    <StudyStatusTranscribed>true</StudyStatusTranscribed>\n";
	print "    <StudyStatusVerified>false</StudyStatusVerified>\n";
	print "  </Study>\n";
	
	print "  <Study>\n";
	print "    <PatientID>00002</PatientID>\n";
	print "    <IssuerOfPatientID>Issuer2</IssuerOfPatientID>\n";
	print "    <PatientName>Anon^Patient2</PatientName>\n";
	print "    <PatientBirthdate>19470213</PatientBirthdate>\n";
	print "    <PatientSex>M</PatientSex>\n";
	print "    <StudyInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333323</StudyInstanceUID>\n";
	print "    <StudyDescription>MRT Abdomen Abdomen</StudyDescription>\n";
	print "    <AccessionNumber>AccNum2</AccessionNumber>\n";
	print "    <RefPhysName>Ref^Phys</RefPhysName>\n";
	print "    <StudyDate>19000101</StudyDate>\n";
	print "    <StudyTime>111111</StudyTime>\n";
	print "    <StudyID></StudyID>\n";
	print "    <ModsInStudy>MR</ModsInStudy>\n";
	print "    <StudyStatusRead>true</StudyStatusRead>\n";
	print "    <StudyStatusDictated>true</StudyStatusDictated>\n";
	print "    <StudyStatusTranscribed>true</StudyStatusTranscribed>\n";
	print "    <StudyStatusVerified>false</StudyStatusVerified>\n";
	print "  </Study>\n";
}
else if (strcasecmp($queryType, "worklist") == 0)
{
	print "  <Study>\n";
	print "    <PatientID>00001</PatientID>\n";
	print "    <IssuerOfPatientID>Issuer1</IssuerOfPatientID>\n";
	print "    <PatientName>Anon^Patient1</PatientName>\n";
	print "    <PatientBirthdate>19470213</PatientBirthdate>\n";
	print "    <PatientSex>M</PatientSex>\n";
	print "    <StudyInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164579</StudyInstanceUID>\n";
	print "    <StudyDescription>MRT Abdomen Abdomen</StudyDescription>\n";
	print "    <AccessionNumber>AccNum1</AccessionNumber>\n";
	print "    <RefPhysName>Ref^Phys</RefPhysName>\n";
	print "    <StudyDate>19100101</StudyDate>\n";
	print "    <StudyTime>111111</StudyTime>\n";
	print "    <StudyID></StudyID>\n";
	print "    <ModsInStudy>MR</ModsInStudy>\n";
	print "    <StudyStatusRead>false</StudyStatusRead>\n";
	print "    <StudyStatusDictated>false</StudyStatusDictated>\n";
	print "    <StudyStatusTranscribed>false</StudyStatusTranscribed>\n";
	print "    <StudyStatusVerified>false</StudyStatusVerified>\n";
	print "  </Study>\n";
	
	print "  <Study>\n";
	print "    <PatientID>00001</PatientID>\n";
	print "    <IssuerOfPatientID>Issuer1</IssuerOfPatientID>\n";
	print "    <PatientName>Anon^Patient1</PatientName>\n";
	print "    <PatientBirthdate>19470213</PatientBirthdate>\n";
	print "    <PatientSex>M</PatientSex>\n";
	print "    <StudyInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376400</StudyInstanceUID>\n";
	print "    <StudyDescription>MRT Abdomen Abdomen</StudyDescription>\n";
	print "    <AccessionNumber>AccNum3</AccessionNumber>\n";
	print "    <RefPhysName>Ref^Phys</RefPhysName>\n";
	print "    <StudyDate>19000101</StudyDate>\n";
	print "    <StudyTime>111111</StudyTime>\n";
	print "    <StudyID></StudyID>\n";
	print "    <ModsInStudy>MR</ModsInStudy>\n";
	print "    <StudyStatusRead>false</StudyStatusRead>\n";
	print "    <StudyStatusDictated>false</StudyStatusDictated>\n";
	print "    <StudyStatusTranscribed>false</StudyStatusTranscribed>\n";
	print "    <StudyStatusVerified>false</StudyStatusVerified>\n";
	print "  </Study>\n";
	
	print "  <Study>\n";
	print "    <PatientID>00002</PatientID>\n";
	print "    <IssuerOfPatientID>Issuer2</IssuerOfPatientID>\n";
	print "    <PatientName>Anon^Patient2</PatientName>\n";
	print "    <PatientBirthdate>19470213</PatientBirthdate>\n";
	print "    <PatientSex>M</PatientSex>\n";
	print "    <StudyInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333323</StudyInstanceUID>\n";
	print "    <StudyDescription>MRT Abdomen Abdomen</StudyDescription>\n";
	print "    <AccessionNumber>AccNum2</AccessionNumber>\n";
	print "    <RefPhysName>Ref^Phys</RefPhysName>\n";
	print "    <StudyDate>19000101</StudyDate>\n";
	print "    <StudyTime>111111</StudyTime>\n";
	print "    <StudyID></StudyID>\n";
	print "    <ModsInStudy>MR</ModsInStudy>\n";
	print "    <StudyStatusRead>false</StudyStatusRead>\n";
	print "    <StudyStatusDictated>false</StudyStatusDictated>\n";
	print "    <StudyStatusTranscribed>false</StudyStatusTranscribed>\n";
	print "    <StudyStatusVerified>false</StudyStatusVerified>\n";
	print "  </Study>\n";
	
}

print "</StudyLevelResponse>\n";

?>