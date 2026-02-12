<?php
//
// authenticate.php
//
// Module for authenticating username/password of a PHP session
//
// CopyRight (c) 2003-2008 RainbowFish Software
//
session_start();
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
import('system.core.orm.DataController');
import('system.utilities.JSON');
import('system.models.User');
import('system.logger');

$var = array();
// wrap up all the variables in the $_GET object
foreach($_GET as $key => $value) $var[$key] = $value;
// wrap up all the variables in the $_POST object
foreach($_POST as $key => $value) $var[$key] = $value;

$return["success"] = "false";
if (!isset($var["action"]))
	$var["action"] = '';
if($var["action"] == "login")
{
	$controller = new DataController($DB_DATABASE);
	//echo (new User($var))->GetTableName();
	$result = $controller->Find(new User($var));
	//print_r($result); exit;
	if($result->recordCount > 0){
            $user = $result->toObject("User");
			$user = $user[0];
			
            $_SESSION['AUTH_USER'] = serialize($user);
            $return["success"] = "true";
            $_logEvent = array();
            $_logEvent['event_type'] = 'Login Successful';
            $_logEvent['additional_text'] = $EVENT_USER_AUTH;
            logger::log($_logEvent);
			// Ensure 'action' parameter is set and equals 'login'
			if (isset($_GET['action']) && $_GET['action'] === 'login') {
				
				// Check if the 'actions' parameter is set and equals 'DicomViewer'
				if (isset($_GET['actions']) && $_GET['actions'] === 'DicomViewer') {
					// Sanitize 'entry' parameter before using it in the URL
					$entry = isset($_GET['entry']) ? urlencode($_GET['entry']) : ''; // Ensure 'entry' is safe to use in the URL
					
					// Redirect to the DICOM viewer page with the sanitized 'entry' parameter
					header("Location: https://apiprinciple.com/system/html5viewer/index.php?entry=$entry&actions=DicomViewer");
					exit();
				} else {
					// Redirect to the default page if 'actions' is not 'DicomViewer'
					header("Location: https://apiprinciple.com/index.php");
					exit();
				}
			}

	}else{
		//edit this message if you want to change what is said for login failure
		$return["error"] = array('type'=>'Login Failure',  'message'=>'Login Failed, invalid password or username.');
	}
	
}else if($var["action"]=="logout"){
	$_logEvent = array();
	$_logEvent['event_type'] = 'Logout Successful';
	$_logEvent['additional_text'] = $EVENT_USER_AUTH;
	logger::log($_logEvent);
	setcookie("sessionCookie", "", time() - 3600);
	unset($_SESSION['AUTH_USER']);
	session_destroy();
	$return["success"] = "true";
	
}else if($var["action"]=="changepassword"){
	if($_SESSION['AUTH_USER']){
		$controller = new DataController($DB_DATABASE);
    	$user = unserialize($_SESSION['AUTH_USER']);
		$user->SetPassword($var["newpassword"]);
		$user->passwordexpired = 0;
		$_SESSION['AUTH_USER'] = serialize($user);
		$controller->Update($user);
		$_logEvent = array();
		$_logEvent['event_type'] = 'Password Changed';
		$_logEvent['additional_text'] = $EVENT_USER_AUTH;
		logger::log($_logEvent);
	}
}else{
  
  if((isset($_SESSION['AUTH_USER'])) && $_SESSION['AUTH_USER']){
    $user = unserialize($_SESSION['AUTH_USER']);
    $controller = new DataController($DB_DATABASE);
    $result = $controller->Find($user);
    if($result->recordCount > 0){
      $return["success"] = "true";
    }else{
    	unset($_SESSION['AUTH_USER']);
    }
  }
}

if((isset($_SESSION['AUTH_USER'])) && $_SESSION['AUTH_USER'])
{
	$user = unserialize($_SESSION['AUTH_USER']);
//	print_r($user);
	$return["username"] = ucwords($user->username);
	$return["admin"] = $user->admin;
	$return["passwordexpired"] = $user->passwordexpired;
	$return["canmailpdf"] = $user->canmailpdf;
	$return["canbatchprintpdfs"] = $user->canbatchprintpdfs;
	$return["canmarkasreviewed"] = $user->canmarkasreviewed;
	$return["canburncd"] = $user->canburncd;
        if (empty($return["canburncd"])){
            $return["canburncd"] = "0";
        }
	$return["canmarkcritical"] = $user->canmarkcritical;
        if (empty($return["canmarkcritical"])){
            $return["canmarkcritical"] = "0";
        }
	$return["canattachorder"] = $user->canattachorder;
        if (empty($return["canattachorder"])){
            $return["canattachorder"] = "0";
        }
	$return["canaddnote"] = $user->canaddnote;
        if (empty($return["canaddnote"])){
            $return["canaddnote"] = "0";
        }
	$return["staffrole"] = $user->staffrole;
        if (empty($return["staffrole"])){
            $return["staffrole"] = "0";
        }
//	print_r($return);
}
session_commit();
//die('==========');
print JSON::Encode($return);
