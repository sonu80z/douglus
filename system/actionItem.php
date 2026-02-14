<?php
//
// actionItem.php
//
// Module for processing entries from MySQL database tables
//
// CopyRight (c) 2003-2008 RainbowFish Software
//
//define('EXPORT_DIR','C:/Program Files/PacsOne/export/');
define('EXPORT_DIR','C:/Program Files/PacsOne/export/');

define('ZIP_ISO_DIR','C:\\Program Files\\PacsOne\\php\\RPRS\\system\\zip_iso\\');
//define('ZIP_ISO_DIR','C:\\Program Files\\PacsOne\\php\\zip_iso\\');

//define('EFILM_DIR','');
session_start();
error_reporting(E_ALL);

//ob_start();
// disable PHP timeout
set_time_limit(0);
include_once($_SERVER["DOCUMENT_ROOT"]."/system/legacy/locale.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");

import('system.utilities.*');
import('system.core.orm.DataController');
import('system.models.*');

global $STUDY_STATUS_READ;
global $STUDY_STATUS_DEFAULT;

$echo = false;
if($echo) echo '<br />'.__LINE__;

if(!$_SESSION["AUTH_USER"])
{
//    var_dump ($_SESSION);
    global $internal_service;
    if (!$internal_service)
        exit();
    
}

$user = unserialize($_SESSION["AUTH_USER"]);
$controller = new DataController($DB_DATABASE);

$username = $user->username;
$action = $_REQUEST['actions'];
if (isset($_REQUEST['actionvalue']))
    $action = $_REQUEST['actionvalue'];
$option = $_REQUEST['option'];
$entry = $_REQUEST['entry'];
import('system.logger');
	
if($echo) echo '<br />'.__LINE__;
	
$return["success"] = "false";
$return["action"] = $action;
//added split because passing as a string list
if(gettype($entry) == "string") $entry = explode(',', $entry);

if($echo) echo '<br />'.__LINE__;
	
/******************************************* 
 *  Report Integration Updated (2/10/2009)  *
 *******************************************/
if (strcasecmp($action, "ViewReports") == 0 && isset($option)) 
{
if($echo) 
{
//$file_path = 'C:/Program Files/PacsOne/php/RPRS/transcriptions/1.3.6.1.4.1.11157.1269224815911140.1567986313.73304.pdf';
//echo '<br />'.__LINE__.' : '.$file_path;
//	echo '<br />'.__LINE__.' : '.$TRANSCRIPTION_DIRECTORY . $entry[0].".pdf";
$file_path = $TRANSCRIPTION_DIRECTORY . $entry[0].".pdf";
echo '<br />'.__LINE__.' : '.$file_path;
	if(!file_exists($file_path))   echo '   ==> doesnot exists';
	else echo '    ==> exists';
	   
	//echo '<br />$TRANSCRIPTION_DIRECTORY = '.$TRANSCRIPTION_DIRECTORY;
	
	//die;
}
	if(file_exists($TRANSCRIPTION_DIRECTORY . $entry[0].".pdf"))
	{
		$return["success"]  = "true";
	}
}
/************************************************* 
 * End Of Report Integration Updated (2/10/2009)  *
 *************************************************/
if (strcasecmp($action, "DownloadAttachment") == 0 && isset($option)) 
{
	if(file_exists($ATTACHMENT_DIRECTORY.$_REQUEST['attachedFile'])){
		$return["success"]  = "true";
	}
}
if (strcasecmp($action, "AttachPDF") == 0 && isset($option)) {
	$target = $TRANSCRIPTION_DIRECTORY . $entry[0] . ".pdf"; 
	if(move_uploaded_file($_FILES['file-attachment']['tmp_name'], $target)) {
		$return["success"]  = "true";
		$action = "Mark Study Read";
	}else{
		$return["success"]  = false;
		$return["msg"]  = $target;
		print JSON::Encode($return);exit;
	}
}

if (strcasecmp($action, 'addNOTE') == 0)
{
    $notes = ($controller->Find(new Notes(array("studyid"=>$entry[0])))->toObject("Notes"));
    if (!$notes)
    {
        $notes = new Notes();
        $notes->studyid = $entry[0];
        $notes->text = $_REQUEST['text'];
        $notes->username = $username;
        $controller->Add($notes);
    }
    else
    {
        $notes = $notes[0];
        if (strlen($_REQUEST['text']) > 0)
        {
            $notes->text = $_REQUEST['text'];
            $controller->Update($notes);
            $notes->username = $username;
        }
        else 
            $controller->Delete($notes);
    }
    $return["success"]  = "true";
    print JSON::Encode($return);
    return 1;
}

if (strcasecmp($action, "AttachORDER") == 0) 
{
    $target = $ORDER_DIRECTORY . $entry[0] . ".pdf"; 
    $study = ($controller->Find(new Study(array("uuid"=>$entry[0])))->toObject("Study"));
    $study = $study[0];

    if (file_exists($target))
        unlink($target);
    if(move_uploaded_file($_FILES['file-attachment']['tmp_name'], $target)) 
    {
        $study->has_attached_orders = 1;
        $controller->Update($study);
        $return["success"]  = "true";
		$return['msg']=$target;
    }
    else 
    {
        $return["failure"]  = "Could not move uploaded file. $target";
        print JSON::Encode($return);
        return 1;
    }
    print JSON::Encode($return);
    return 1;
}

if (strcasecmp($action, "AttachTECH_NOTES") == 0) 
{
    $filename = $_FILES['file-attachment']['name'];
    $l = strlen($filename);
    $fileExtention = substr($filename, $l-4, 4);
    $NOTES_DIR = $INSTALL_DIRECTORY.'tech_notes/';
    $target = $NOTES_DIR.$entry[0].$fileExtention; 
    
    if (file_exists($filename))
    {
        $return["failure"]  = "Error: the file is already exists";
        print JSON::Encode($return);
        return 1;
    }
    $study = ($controller->Find(new Study(array("uuid"=>$entry[0])))->toObject("Study"));
    $study = $study[0];

    if(move_uploaded_file($_FILES['file-attachment']['tmp_name'], $target)) 
    {
        $study->has_tech_notes = 1;
        $controller->Update($study);
        $return["success"]  = "true";
    }
    else 
    {
        $return["failure"]  = "Could not move uploaded file. $target";
        print JSON::Encode($return);
        return 1;
    }
    print JSON::Encode($return);
    return 1;
}


if (strcasecmp($action, "Remove the TECH_NOTES") == 0) 
{
    include_once $_SERVER["DOCUMENT_ROOT"]."/system/utilities/utilFunctions.php";
    
    $NOTES_DIR = $INSTALL_DIRECTORY.'tech_notes/';
    $filenamePart = $entry[0];
    
    $filename = findFile($NOTES_DIR, $filenamePart);
    if (!file_exists($filename))
    {
        $return["failure"]  = "File not found.";
        print JSON::Encode($return);
        return 1;
    }
    
    @unlink($filename);

    $study = ($controller->Find(new Study(array("uuid"=>$entry[0])))->toObject("Study"));
    $study = $study[0];

    $study->has_tech_notes = 0;
    $controller->Update($study);

    $return["success"]  = "true";
    print JSON::Encode($return);
    return 1;
}

if(strcasecmp($action,"scheduleZip")==0){
	$db = MySQLDatabase::GetInstance();
	$query='SELECT * FROM schedule WHERE `status`="pending" LIMIT 20';
	$result=$db->ExecuteReader($query);
	while($record=$result->GetNextAssoc()){
		
	/*	$i=0;
		while(1){*/
			//'100% Complete'
			$q = "select * from dbjob WHERE id={$record['dbjob_id']}";
			$result2=$db->ExecuteReader($q)->GetNextAssoc();
			//print_r($record);
			//print_r($result2);
			if($result2['status']=='100% Complete' OR $result2['status']=='success'){
				$q = "UPDATE schedule SET `status` = 'complete' WHERE id={$record['id']}";
				$result3=$db->ExecuteReader($q);
			//	break;
			//	zipBuilder($filename);
				//echo json_encode(array('success'=>true));
			}elseif($result2['status']=='failed'){
				//echo json_encode(array('success'=>false,'msg'=>'Error in pacsone '.$id,'raw'=>$result));
			//	break;
			}
		//}
	}
	exit;
}

if (strcasecmp($action, "Download") == 0) 
{
	//error_reporting(-1);
	$db = MySQLDatabase::GetInstance();
	//print_r(unserialize($_SESSION['AUTH_USER']));exit;
	
	//$q = "select * from dbjob ";
	
	//print_r($db->ExecuteReader($q)->GetNextAssoc());exit;
	
	//print_r(get_class($db));
       // $study_record = $db->ExecuteReader("select * from study where uuid = '".$study->uuid."' limit 1")->GetNextAssoc();
//print_r(get_class_methods(get_class($db)));
//exit;
	//var_dump($db);
	$level = "study";
	$label =  trim($option);				
	$directory='C:/Program Files/PacsOne/export/';
	$directory=EXPORT_DIR;
	$directory=str_replace('\\','/',$directory);
	$query = "insert into dbjob (username,aetitle,type,class,uuid,priority,status,details) values";
    $query .= "('$username','_$size','$type','$level','$label',$viewer,'created','$directory')";
	$filename =  trim($option);
	$filename=$directory.$filename;
/*	if(file_exists($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Chose another name'));
		exit;
	}*/
	if(!mkdir($filename)){
	/*	echo json_encode(array('success'=>false,'msg'=>'Fail to create directory'));
		exit;*/
	}
	//echo $filename;exit;
	$query="insert into dbjob (username,aetitle,type,class,uuid,priority,status,details) 
						values('root','_650','export','study','testdir',0,'created','".addslashes($filename)."')";
	
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	
	$filename =  trim($option);
	
	
	
	$result=$db->ExecuteReader('SELECT LAST_INSERT_ID() AS id')->GetNextAssoc();
	
	$id=$result['id'];
	
	/*foreach ($entry as $uid) {
        if (strcasecmp($level, "Patient") == 0) {
            $uid = urldecode($uid);
            $uid = get_magic_quotes_gpc()? $uid : addslashes($uid);
        }
		$db->ExecuteReader("replace export set jobid=$id,class='study',uuid='$uid'");
    }*/
	
	$q = "update dbjob set status='submitted',submittime=NOW() where id=$id";
	$db->ExecuteReader($q);
	$user=unserialize($_SESSION['AUTH_USER']);
	//print_r(unserialize($_SESSION['AUTH_USER']));
	$query = "insert into schedule (`user_id`,`dbjob_id`,`type`,`status`,`created`,`filename`) values";
    $query .= "({$user->id},$id,'zip','pending',NOW(),'$filename')";
	//print_r($query);exit;
	$study_record = $db->ExecuteReader($query);//->GetNextAssoc();
	$result=$db->ExecuteReader('SELECT LAST_INSERT_ID() AS id')->GetNextAssoc();
	//echo $schedule_id=$result['id'];exit;
	echo json_encode(array('success'=>true,'data'=>$schedule_id));
	return;
	$i=0;
	while(1){
		//'100% Complete'
		$q = "select status from dbjob WHERE id=$id";
		$result=$db->ExecuteReader($q)->GetNextAssoc();
		
		if($result['status']=='100% Complete'){
			zipBuilder($filename);
			//echo json_encode(array('success'=>true));
		}elseif($result['status']=='failed'){
			echo json_encode(array('success'=>false,'msg'=>'Error in pacsone '.$id,'raw'=>$result));
			exit;
		}
		sleep(1);
		$i++;
	}
	
	echo json_encode(array('success'=>true));
	//$result = $db->query("select id from dbjob where details='$directory' and status!='success' and status!='failed'");
	exit;
	
	//print_r($files);exit;
//	echo json_encode(array('success'=>false,'msg'=>$filename));
   // zipFiles2($files, $filename,true);
}

function zipBuilder($filename){
	//$filename =  trim($option);
	if(empty($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Invalid file name'));
		exit;
	}
	include_once 'legacy/download.php';
	error_reporting(E_ALL);
    $zip = new ZipHelper;
	$zip->init();
	
	//echo $tempname=$zip->getFilename();exit;
	$zip->addDirRecursively(EXPORT_DIR.$filename);
	
	if(defined('EFILM_DIR')){
		//$zip->addDirRecursively('C:/efilm');
		$zip->addDirRecursively(EFILM_DIR);
	}
	$result=$zip->close();
	
	
	
	$tempname=$zip->getFilename();
	
		return $tempname;
			if(empty($_SESSION["zipfiles"])){
			//	print_r($_SESSION);
				$_SESSION['zipfiles']=array();
				//print_r($_SESSION["AUTH_USER"]['zipfiles']);
				$_SESSION['zipfiles'][]=array('file'=>$tempname,'time'=>time(),'filename'=>$filename);
				$id=0;
			}else{
				$_SESSION['zipfiles'][]=array('file'=>$tempname,'time'=>time(),'filename'=>$filename);
				$id=count($_SESSION['zipfiles'])-1;
			}
			echo json_encode(array('success'=>true,'id'=>$id));
		
		exit;
		//echo "command returned $return_value\n";
	
}

if (strcasecmp($_GET['action'], "DownloadZip") == 0) 
{
	//set_error_handler('err_h');
    require_once ('legacy/download.php');	
	$id=$_GET['id'];
	
	downloadZip($id);
	
}

if (strcasecmp($action, "DownloadZipIso") == 0) 
{
	$user=unserialize($_SESSION['AUTH_USER']);
	$id=$_REQUEST['id'];
	$db = MySQLDatabase::GetInstance();
	$query='SELECT * FROM schedule WHERE `id`='.$id.' AND `user_id`='.$user->id;
	//echo $query;exit;
	$result=$db->ExecuteReader($query);
	$record=$result->GetNextAssoc();
	if($record['user_id']!=$user->id){
		echo 'Not authorised';
		exit;
	}
	switch($record['type']){
		case 'iso':
			$filename=$record['filename'].'.iso';
			$contenttype='application/octet-stream';
			break;
		case 'zip':
			$filename=$record['filename'].'.zip';
			$contenttype='application/octet-stream';
			break;
		default:
	}
	
	//echo ZIP_ISO_DIR.$record['tempname'];
	//echo (int)file_exists(ZIP_ISO_DIR.$record['tempname']);
	//exit;
	if(file_exists(ZIP_ISO_DIR.$record['filename'].'-'.$record['tempname'])){
	//if(file_exists(ZIP_ISO_DIR.$record['tempname'])){
		//print_r($record);exit;
		//header('Content-Type: application/octetstream');
		/*header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Content-Length: ' . filesize(ZIP_ISO_DIR.$record['tempname']));
		@readfile(ZIP_ISO_DIR.$record['tempname']);*/
		header('Location: /system/zip_iso/'.$record['filename'].'-'.$record['tempname']);
		exit;
	}else{
		echo "File not found";
	}
	//$q = "select status from dbjob WHERE id=$record[dbjob_id]";
	//$dbjob_record=$db->ExecuteReader($q)->GetNextAssoc();
	//	if($result['status']=='100% Complete'){//echo json_encode(array('success'=>false,'msg'=>$filename));exit;
	//		isofile($record['filename']);
			//echo json_encode(array('success'=>true));
	//	}
	
	//print_r($record);
	exit;
}

if (strcasecmp($action, "getJobQue") == 0) {
	$db = MySQLDatabase::GetInstance();
	$page=isset($_REQUEST['page'])?$_REQUEST['page']:1;
	$limit=25;
	$user=unserialize($_SESSION['AUTH_USER']);
	$query="SELECT COUNT(*) AS `total` FROM schedule WHERE  (type='iso' OR type='zip') AND user_id={$user->id} ";
	$result=$db->ExecuteReader($query)->GetNextAssoc();
	$total=$result['total'];
	$query="SELECT * FROM schedule WHERE  (type='iso' OR type='zip') AND user_id={$user->id} ORDER BY `created` DESC LIMIT ".(($page-1)*$limit).",$limit";
	$result=$db->ExecuteReader($query);
	$data=array();
	while($record=$result->GetNextAssoc()){
	
		$data[]=$record;
	}
	echo json_encode(array('success'=>true,'data'=>$data,'total'=>$total,'limit'=>$limit));
	exit;
}

if (strcasecmp($action, "deleteJobQue") == 0) {
	$db = MySQLDatabase::GetInstance();
	$id=$_REQUEST['id'];
	$user=unserialize($_SESSION['AUTH_USER']);
	$query="DELETE FROM schedule WHERE  id=$id AND user_id={$user->id} ";
	$result=$db->ExecuteReader($query);
	echo json_encode(array('success'=>(bool)$result));
	exit;
}

if (strcasecmp($action, "createZIP") == 0) 
{
	$db = MySQLDatabase::GetInstance();
	
	//$q = "select * from dbjob ";
	
	//print_r($db->ExecuteReader($q)->GetNextAssoc());exit;
	
	//print_r(get_class($db));
       // $study_record = $db->ExecuteReader("select * from study where uuid = '".$study->uuid."' limit 1")->GetNextAssoc();
//print_r(get_class_methods(get_class($db)));
//exit;
	//var_dump($db);
	$level = "study";
	$label =  trim($option);
	$directory=EXPORT_DIR;
	$directory=str_replace('\\','/',$directory);
	$query = "insert into dbjob (username,aetitle,type,class,uuid,priority,status,details) values";
    $query .= "('$username','_$size','$type','$level','$label',$viewer,'created','$directory')";
	$filename =  trim($option);
	$filename=$directory.$filename; 
	
	if(file_exists($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Chose another name'));
		exit;
	}
	if(!mkdir($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Fail to create directory'));
		exit;
	}
	
	$query="insert into dbjob (username,aetitle,type,class,uuid,priority,status,details) 
						values('root','_650','export','study','testdir',0,'created','$filename')";
	
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	
	$filename =  trim($option);
	
	
	
	$result=$db->ExecuteReader('SELECT LAST_INSERT_ID() AS id')->GetNextAssoc();
	
	$id=$result['id'];
	
	foreach ($entry as $uid) {
        if (strcasecmp($level, "Patient") == 0) {
            $uid = urldecode($uid);
            $uid = get_magic_quotes_gpc()? $uid : addslashes($uid);
        }
		$db->ExecuteReader("replace export set jobid=$id,class='study',uuid='$uid'");
    }
	$q = "update dbjob set status='submitted',submittime=NOW() where id=$id";
	$db->ExecuteReader($q);
	
	$user=unserialize($_SESSION['AUTH_USER']);
	//print_r(unserialize($_SESSION['AUTH_USER']));
	$query = "insert into schedule (`user_id`,`dbjob_id`,`type`,`status`,`created`,`filename`) values";
    $query .= "({$user->id},$id,'zip','pending',NOW(),'$filename')";
	//print_r($query);exit;
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	$result=$db->ExecuteReader('SELECT LAST_INSERT_ID() AS id')->GetNextAssoc();
	$schedule_id=$result['id'];
	$query="INSERT INTO schedule_dbjob (`schedule_id`,`dbjob_id`) VALUES($schedule_id,$id)";
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	echo json_encode(array('success'=>true,'data'=>$schedule_id));
	return;
	
	while(1){
		//'100% Complete'
		$q = "select status from dbjob WHERE id=$id";
		$result=$db->ExecuteReader($q)->GetNextAssoc();
		if($result['status']=='100% Complete'){//echo json_encode(array('success'=>false,'msg'=>$filename));exit;
			isofile($filename);
			//echo json_encode(array('success'=>true));
		}
		sleep(1);
		//echo $filename,$result['status'];
	}
	
	echo json_encode(array('success'=>true));
	//$result = $db->query("select id from dbjob where details='$directory' and status!='success' and status!='failed'");
	exit;
}

if (strcasecmp($action, "createISO") == 0) 
{
	$db = MySQLDatabase::GetInstance();
	
	//$q = "select * from dbjob ";
	
	//print_r($db->ExecuteReader($q)->GetNextAssoc());exit;
	
	//print_r(get_class($db));
       // $study_record = $db->ExecuteReader("select * from study where uuid = '".$study->uuid."' limit 1")->GetNextAssoc();
//print_r(get_class_methods(get_class($db)));
//exit;
	//var_dump($db);
	$level = "study";
	$label =  trim($option);
	$directory=EXPORT_DIR;
	$directory=str_replace('\\','/',$directory);
	$query = "insert into dbjob (username,aetitle,type,class,uuid,priority,status,details) values";
    $query .= "('$username','_$size','$type','$level','$label',$viewer,'created','$directory')";
	$filename =  trim($option);
	$filename=$directory.$filename; 
	
	if(file_exists($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Chose another name'));
		exit;
	}
	if(!mkdir($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Fail to create directory'));
		exit;
	}
	
	$query="insert into dbjob (username,aetitle,type,class,uuid,priority,status,details) 
						values('root','_650','export','study','testdir',0,'created','$filename')";
	
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	
	$filename =  trim($option);
	
	
	
	$result=$db->ExecuteReader('SELECT LAST_INSERT_ID() AS id')->GetNextAssoc();
	
	$id=$result['id'];
	
	foreach ($entry as $uid) {
        if (strcasecmp($level, "Patient") == 0) {
            $uid = urldecode($uid);
            $uid = get_magic_quotes_gpc()? $uid : addslashes($uid);
        }
		$db->ExecuteReader("replace export set jobid=$id,class='study',uuid='$uid'");
    }
	$q = "update dbjob set status='submitted',submittime=NOW() where id=$id";
	$db->ExecuteReader($q);
	
	$user=unserialize($_SESSION['AUTH_USER']);
	//print_r(unserialize($_SESSION['AUTH_USER']));
	$query = "insert into schedule (`user_id`,`dbjob_id`,`type`,`status`,`created`,`filename`) values";
    $query .= "({$user->id},$id,'iso','pending',NOW(),'$filename')";
	//print_r($query);exit;
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	$result=$db->ExecuteReader('SELECT LAST_INSERT_ID() AS id')->GetNextAssoc();
	$schedule_id=$result['id'];
	$query="INSERT INTO schedule_dbjob (`schedule_id`,`dbjob_id`) VALUES($schedule_id,$id)";
	$study_record = $db->ExecuteReader($query)->GetNextAssoc();
	echo json_encode(array('success'=>true,'data'=>$schedule_id));
	return;
	
	while(1){
		//'100% Complete'
		$q = "select status from dbjob WHERE id=$id";
		$result=$db->ExecuteReader($q)->GetNextAssoc();
		if($result['status']=='100% Complete'){//echo json_encode(array('success'=>false,'msg'=>$filename));exit;
			isofile($filename);
			//echo json_encode(array('success'=>true));
		}
		sleep(1);
		//echo $filename,$result['status'];
	}
	
	echo json_encode(array('success'=>true));
	//$result = $db->query("select id from dbjob where details='$directory' and status!='success' and status!='failed'");
	exit;
}
function isofile($filename){	
	//$filename =  trim($option);
	if(empty($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Invalid file name'));
		exit;
	}
    
	$tempname = tempnam(getenv("TEMP"), "PacsOne");
/*	$files = array();
    foreach ($entry as $uid)
    {
		$uids = array();
        $seriesList = $controller->Find(new Study(array("studyuid"=>$uid)));
        foreach($seriesList as $series)
        {
            $imageList = $controller->Find(new Image(array("seriesuid"=>$series->uuid)))->toObject("Image");
            foreach($imageList as $image)
            {
                array_push($uids, $image->uuid);
            }
        }
		foreach ($uids as $uid) 
		{
			$imageList = $controller->Find(new Image(array("uid"=>$uid)))->toObject("Image");
			$image = $imageList[0];
			if (file_exists($image->path)) 
			{
				$files[] = $image->path;
			}
		}
		$files[]=':';
    }
    //print_r($files);exit;
    
    if (count($files) == 0)
    {
        die('<h1>No files found</h1>Please use "back" button of your browser to continue');
    //    	die('<pre>'.print_r($files, 1).'</pre>');
    } */
	//print_r($files);exit;
	$descriptorspec = array(
	   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
	   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
	   2 => array("pipe", "w") // stderr is a file to write to
	);
	$iso_jar_dir=dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'java';
	$process=proc_open('java -jar main.jar '.$tempname.' "'.$filename.'"',$descriptorspec,$pipes,$iso_jar_dir);
	if (is_resource($process)) {
		
		fwrite($pipes[0], implode("\r\n", $files));
		fclose($pipes[0]);

		$msg=stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		echo stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		// It is important that you close any pipes before calling
		// proc_close in order to avoid a deadlock
		$return_value = proc_close($process);
		if($return_value){
			echo json_encode(array('succes'=>false,'msg'=>$msg));
			exit;
		}
		if(!file_exists( $tempname)){
			echo json_encode(array('succes'=>false,'msg'=>'File not formed'));
			exit;
		}
		if(!filesize($tempname)){
			echo json_encode(array('succes'=>false,'msg'=>'Zero file size'));
			exit;
		}
		if(!$return_value){
			if(empty($_SESSION["isofiles"])){
			//	print_r($_SESSION);
				$_SESSION['zipfiles']=array();
				//print_r($_SESSION["AUTH_USER"]['zipfiles']);
				$_SESSION['isofiles'][]=array('file'=>$tempname,'time'=>time(),'filename'=>$filename);
				$id=0;
			}else{
				$_SESSION['isofiles'][]=array('file'=>$tempname,'time'=>time(),'filename'=>$filename);
				$id=count($_SESSION['isofiles'])-1;
			}
			header('Location: /system/actionItem.php?action=DownloadIso&id='.$id);
			exit;
			//downloadIso($id);
			//echo json_encode(array('success'=>true,'id'=>$id));
		}
		else{
			echo json_encode(array('success'=>false,'msg'=>$msg));
		}
		exit;
		//echo "command returned $return_value\n";
	}else{
		echo json_encode(array('success'=>false,'msg'=>'Process not started'));
		exit;
	}
}


if (strcasecmp($_GET['action'], "DownloadIso") == 0) 
{
	//set_error_handler('err_h');
    require_once ('legacy/download.php');	
	$id=$_GET['id'];
	
	downloadIso($id);
	
}

if (($action == 'Mark Study Read') || ($action == 'Mark Study Un-Read')) 
{
	$worklist = new Worklist();
	$study = new Study();
	foreach ($entry as $uid) 
	{
		$study->uuid = $uid;
		$worklist->studyuid = $uid;
		if (stristr($action, "Un-Read")) 
		{
			$study->reviewed = "null";
		    $worklist->status = $STUDY_STATUS_DEFAULT;
		} 
		else 
		{
			$study->reviewed = $username;
			$worklist->status = $STUDY_STATUS_READ;
		}		
	    $controller->Update($study);
		//$controller->Update($worklist);
	}
	ob_end_clean();
	$return["success"]  = "true";
}

if ($action == "Mark Study Reviewed" || $action == "Mark Study UNReviewed" || $action == "remove the report")
{
    $worklist = new Worklist();
    $study = new Study();
    foreach ($entry as $uid) 
    {
        $study->uuid = $uid;
        $study->reviewed_user_id = $user->id;
        $study->reviewed_date = date('Y-m-d H:i:s');
        if ($action == "Mark Study UNReviewed" || $action == "remove the report")
        {
            $study->reviewed_user_id = 'null';
            $study->reviewed_date = 'null';
            if ($action == "remove the report")
                $study->reviewed = 'null';
        }

        if (($action == "Mark Study Reviewed") && (!file_exists(rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/transcriptions/'.$uid.'.pdf')))
        {
            $return["failure"]  = "Can't find a pdf file for the study. Can't mark the study as reviewed";
            print JSON::Encode($return);
            return 1;
        }
        $controller->Update($study);
        $worklist->studyuid = $uid;
        $controller->Update($worklist);
    }
    $_logEvent = array();
    $_logEvent['event_type'] = 'Study reviewed';
    $_logEvent['event_table'] = 'study';
    $_logEvent['event_table_id'] = $study->uuid;
    $_logEvent['additional_text'] = "Study with id = ".print_r($entry, 1)." reviewed";
    logger::log($_logEvent);

    $return["success"]  = "true";
}

if ($action == 'Mark Study as Critical')
{
    $worklist = new Worklist();
    $study = new Study();
    foreach ($entry as $uid) 
    {
        $study->uuid = $uid;

        $db = MySQLDatabase::GetInstance();
        $study_record = $db->ExecuteReader("select * from study where uuid = '".$study->uuid."' limit 1")->GetNextAssoc();
        $mail_record = $db->ExecuteReader("select * from physician_mail_addresses where upper(username) = upper('".$study_record['referringphysician']."') limit 1")->GetNextAssoc();
        
		if ($mail_record['mail'])
        {
		$return["failure"] = '====adm===='.print_r($mail_record, 1);
		//print JSON::Encode($return);
		//file_put_contents('c:/temp/1.txt', print_r($return, 1));
		//die();
                $args = array();
                $args['studyID'] = $uid;
                $args['mailSubject'] = "A study was marked as critical";
                $args['mailTo'] = $mail_record['mail'];
                $args['mailText'] = $MAIL_FOR_PHYSICIAN_TEMPLATE;
                $args['mailText'] = str_replace('{physician_name}', $study_record['referringphysician'], $args['mailText']);
                $args['noFileAttached'] = true;	

                require_once('controls/MailControl.php');
                $m = new MailControl();
                $result = $m->SendMail($args);
                $study->critical_date = date('Y-m-d H:i:s');
//			echo print_r($study, 1);
        }
        else
        {
            $return["failure"] = 'Mail not sent. Referring phisician\'s email not set';
        }
        $study->is_critical = 1;

        $controller->Update($study);
        $worklist->studyuid = $uid;
        $controller->Update($worklist);
    }

    $_logEvent = array();
    $_logEvent['event_type'] = 'Critical study';
    $_logEvent['event_table'] = 'study';
    $_logEvent['event_table_id'] = $study->uuid;
    $_logEvent['additional_text'] = "Study was marked as critical id = ".print_r($entry, 1);
    logger::log($_logEvent);

    $return["success"]  = "true";
}
if ($action == "remove the report" || $action == "remove the order")
{
    $study = ($controller->Find(new Study(array("uuid"=>$entry[0])))->toObject("Study"));
    $study = $study[0];

    $target = $ORDER_DIRECTORY . $entry[0] . ".pdf"; 
    if(file_exists($target) && $action == "remove the order")
    {
        $study->has_attached_orders = 0;
        unlink($target);
        $controller->Update($study);
	$return["success"]  = "true";
    }
    
    $target = $TRANSCRIPTION_DIRECTORY . $entry[0] . ".pdf"; 
    if(file_exists($target) && $action == "remove the report")
    {
        if ($return["success"] == "true")
        {
            unlink($target);
            $controller->Update($study);
            $return["success"]  = "true";
        }
    }

    print JSON::Encode($return);
    return 1;
}

if ($action == "Mark Study as Uncritical")
{
	$worklist = new Worklist();
	$study = new Study();
	foreach ($entry as $uid) 
	{
		$study->uuid = $uid;
		$study->is_critical = 0;

		$controller->Update($study);
		$worklist->studyuid = $uid;
	    $controller->Update($worklist);
	}
	
	$_logEvent = array();
	$_logEvent['event_type'] = 'Critical study';
	$_logEvent['event_table'] = 'study';
	$_logEvent['event_table_id'] = $study->uuid;
	$_logEvent['additional_text'] = "Study marked as NOT critical. ID = ".print_r($entry, 1);
	logger::log($_logEvent);
	
	$return["success"]  = "true";
}

print JSON::Encode($return);
?>