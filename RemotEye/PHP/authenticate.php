<?php
//session_start();

// This script may be used to verify authentication of the RemotEye client:
// Username and Password are passed by the RemotEye client as POST parameters.

$username = $_POST['Username'];
$password = $_POST['Password'];

// ... do something to verify username and password ...
// (this step usually involves access to a database)

// This sample script always authenticates the client
$authenticationOK = true;

// Build the XML response
header('Content-Type: text/xml');    
print "<?xml version=\"1.0\"?>\n";
print "<AuthenticationResponse>\n";
if ($authenticationOK)
{
	print "<AuthenticationStatus>Success</AuthenticationStatus>\n";
	print "<AuthenticationToken>TKN_" . $username . "</AuthenticationToken>\n";
	print "<Username>User_" . $username . "</Username>\n";
}
else
{
	print "<AuthenticationStatus>Failure</AuthenticationStatus>\n";
}
print "</AuthenticationResponse>\n";

?>
