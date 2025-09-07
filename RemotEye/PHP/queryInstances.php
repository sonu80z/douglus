<?php

//session_start();

// This script may be used to implement the Instance-level query
// on the back-end: the search parameters are passed by the 
// RemotEye client as POST parameters.
// Please notice that this script will be called once per each
// Series returned by the Series-level query.

// Search parameters:
$authToken = $_POST['AuthenticationToken'];
$queryType = $_POST['QueryType'];
$callingAE = $_POST['CallingAETitle'];
$calledAE = $_POST['CalledAETitle'];
$patientID = $_POST['PatientID'];	// Single-value matching
$studyInstUID = $_POST['StudyInstanceUID']; // Single-value matching
$seriesInstUID = $_POST['SeriesInstanceUID']; // Single-value matching
$issuerOfPatientID = @$_POST['IssuerOfPatientID'];
// ... perform a query on the back-end database to return
// all matching Instances ...

// WARNING: the POSTed variables may contain wildcard characters,
// as specified by DICOM PS 3.4 for the Query-Retrieve SOP Class,
// hence some translations may be necessary before querying the
// database.

// Build the XML response, returning all Instances matching
// the search parameters:
// this sample script just returns five fixed Series, with
// no access to database, but in the actual implementation
// the returned list will be dynamically generated on the 
// basis of the resultset of the DB query.

header('Content-Type: text/xml');    
print "<?xml version=\"1.0\"?>\n";

print "<InstanceLevelResponse>\n";

if ($seriesInstUID == "1.3.6.1.4.1.18047.1.6.1999991394698164580")
{
  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164581</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>1</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164583</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>2</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164587</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>3</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164590</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>4</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698164592</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>5</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";
}
else if ($seriesInstUID == "1.3.6.1.4.1.18047.1.6.1999991394698376401")
{
  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376402</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>1</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376403</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>2</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376404</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>3</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376407</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>4</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698376409</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>5</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";  
}
else
{
  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333325</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>1</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333326</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>2</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333328</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>3</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333330</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>4</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";

  print "  <Instance>\n";
  print "    <SOPInstanceUID>1.3.6.1.4.1.18047.1.6.1999991394698333332</SOPInstanceUID>\n";
  print "    <SOPClassUID>1.2.840.10008.5.1.4.1.1.1</SOPClassUID>\n";
  print "    <InstanceNumber>5</InstanceNumber>\n";
  print "    <NumOfFrames></NumOfFrames>\n";
  print "  </Instance>\n";  
}

print "</InstanceLevelResponse>\n";

?>