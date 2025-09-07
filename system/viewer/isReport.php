<?php
	session_cache_limiter('private');
	include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
	$UID = $_REQUEST["UID"];
	if(file_exists("$TRANSCRIPTION_DIRECTORY"."$UID.pdf")){
		echo "true";
	}else{
		echo "false";
	}
?>
