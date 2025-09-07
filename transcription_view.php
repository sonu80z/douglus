<?php
	session_start();
	$filename = $_GET['filename'];

	// check the validity of the request
	if(	preg_match('/^transcriptions\/[0-9\.]+\.pdf$/', $filename)){
		if(isset($_SESSION['AUTH_USER'])){
			header("Content-type: application/pdf");
			header("Content-Disposition: inline; filename=$filename");
						
			readfile($filename);
		}
		else{
			header('HTTP/1.1 403 Forbidden');
			die('Forbidden!');
		}
	}
	else{
		header('HTTP/1.1 403 Forbidden');
		die('Forbidden');
	}
?>