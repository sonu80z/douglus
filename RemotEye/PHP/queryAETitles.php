<?php

//session_start();

// This script may be used to implement the AE titles query
// on the back-end: it shall return the list of AE titles
// which shall be considered by the viewer for the passed
// 'Context'.

// ... perform a query on the back-end database to return
// the list of available AE titles for the passed 'Context' ...

// Build the XML response, returning all AE Titles:
// this sample script just returns a fixed list of AE titles, with
// no access to database, but in the actual implementation
// the returned list will be dynamically generated on the 
// basis of the resultset of the DB query.

header('Content-Type: text/xml');    
print "<?xml version=\"1.0\"?>\n";

print "<AETitlesResponse>\n";

print "  <AETitle>*</AETitle>\n";
print "  <AETitle>Sample AE 1</AETitle>\n";
print "  <AETitle>Sample AE 2</AETitle>\n";
print "  <AETitle>Sample AE 3</AETitle>\n";
print "  <AETitle>Sample AE 4</AETitle>\n";
print "  <AETitle>Sample AE 5</AETitle>\n";

print "</AETitlesResponse>\n";

?>