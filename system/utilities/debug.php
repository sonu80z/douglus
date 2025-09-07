<?php
$debugStart = getMicroTime();
function debug($msg){
    echo $msg;
	$fp = fopen('debug.txt', 'a');
	fwrite($fp, "\n[".date("m/d/Y H:i:s")."]:-> Execution Time(".getExecutionTime()."):".$msg);
	fclose($fp);	
}
function getExecutionTime(){
	global $debugStart;
	return round(getMicroTime() - $debugStart,5);
}
function getMicroTime(){
	$mtime = microtime();
	$marray = explode(" ", $mtime);
	return $marray[1] + $marray[0];
}
?>