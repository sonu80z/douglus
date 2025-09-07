<html>
<body>
<?php
	session_cache_limiter('private');
	include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
	
	$UID = $_REQUEST["UID"];
	if(file_exists("$TRANSCRIPTION_DIRECTORY"."$UID.pdf")){
		print "<script type='text/javascript'>location.href = '$TRANSCRIPTION_VIRTUAL_DIRECTORY"."$UID.pdf';</script>";
	}else{
		print "<script type='text/javascript'>alert('A signed report is not yet available for this study, please check back soon.');window.close();</script>";
	}
?>
</body>
</html>
