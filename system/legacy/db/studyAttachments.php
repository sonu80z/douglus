<?php
/**
 * Study Notes & Attachments
 * This is used to get data for notes and attachments.
 */

session_start();

include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/system/legacy/locale.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
import("system.database.MySQLDatabase");
import("system.core.database.MySQLDatabase");


$db = MySQLDatabase::GetInstance($DB_DATABASE);
$uid = $_POST['uid'];
//$uid = '1.2.840.113680.1.103.56263.1218827817.484238';
$notes = "[]";
$attachments = "[]";
if($uid != ""){
	$attachmentsQuery = "select id, path, mimetype as type, size from attachment where uuid = '".$uid."'";
	$notesQuery = "select created, headline, notes from studyNotes where uuid = '".$uid."';";
 }
 print "{success:true, notes:".$db->ExecuteReader($notesQuery)->toJSON(false).", attachments:".$db->ExecuteReader($attachmentsQuery)->toJSON(false)."}";
//     die('<pre>'.print_r($_REQUEST, 1).'</pre>');
?>
