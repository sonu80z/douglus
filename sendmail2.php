<?php 
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
import("system.core.database.MySQLDatabase");


$db = MySQLDatabase::GetInstance();
$res = $db->ExecuteReader("select institution from patient where origid = (select patientid from study where uuid = '" . $_REQUEST['study_id'] . "' limit 1) limit 1")->GetNextAssoc();
$res = $db->ExecuteReader("select mail from institution_mail_addresses where institution = '$res[institution]' order by mail limit 1")->GetNextAssoc();

$output =file_get_contents('system/viewer/sendMailView2.html');

$study_id = $_GET['study_id'];
$pdf_file_name = 'transcriptions/'.$study_id.'.pdf';
$output = str_replace('{study_id}', $study_id, $output);
$output = str_replace('{pdf_file_name}', $pdf_file_name, $output);
$output = str_replace('{studyID}', $study_id, $output);
if (!isset($res['mail']) || !$res['mail'])
	$res['mail'] = 'address@sample.com';
$output = str_replace('{default_email}', $res['mail'], $output);
//print $output;
print json_encode(array('success'=>true,'data'=>array(
'study_id'=>$study_id,
'pdf_file_name'=>$pdf_file_name,
'studyID'=>$pdf_file_name,
'default_email'=>$res['mail']
))
);
?>