<?php 
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
import("system.core.database.MySQLDatabase");
$output =file_get_contents('system/viewer/newStudyView.html');
	
print $output;

?>