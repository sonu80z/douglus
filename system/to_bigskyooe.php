<?php
define('ORDER_HTTP_HOST','order.bigskymobileimaging.com');
session_start();
//http://18.221.194.47/bigskyooe/admin/order/add?from=dashboard
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
import('system.core.orm.DataController');
import('system.utilities.JSON');
import('system.models.User');
import('system.logger');

$https=false;
if(isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on'){
	$https=true;
}

function randStr(){
    $rand=rand();
    $rand=md5($rand);
    return $rand;
}

$rand=randStr();
//print_r(unserialize($_SESSION['AUTH_USER']));exit;
if(!empty($_SESSION['AUTH_USER'])){
	$user=unserialize($_SESSION['AUTH_USER']);
	if(!empty($user->username)){
		$db=new mysqli($DB_HOST,$DB_USER,$DB_PASS);
		$rs=$db->query('INSERT INTO `tristate`.`to_bigskyooe` (`rand`,`username`) VALUES("'.addslashes($rand).'","'.$user->username.'")');
		if($rs){
			if($https)
				$url='https://'.ORDER_HTTP_HOST.'/admin/auth/login2/'.$rand;
			else
				$url='http://'.ORDER_HTTP_HOST.'/admin/auth/login2/'.$rand;
			header('Location: '.$url);
			exit;
		}else{
			echo 'Error';
		}
	}
}
exit;
