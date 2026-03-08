<?php
session_start();
if((isset($_SESSION['AUTH_USER'])) && $_SESSION['AUTH_USER']){
	header('Location: http://18.221.194.47/bigskyooe/admin/order');
	exit;
}else{
	echo "Unauthorised";
}