<?php
$YOUR_DB_ADDRESS = 'localhost';
$YOUR_DB_USER = 'root';
$YOUR_DB_PASS = '111';
$YOUR_DB = 'archive';
$your_path = 'c:/temp/reports/';

$db = mysql_connect($YOUR_DB_ADDRESS, $YOUR_DB_USER,$YOUR_DB_PASS) or die("Database error"); 
mysql_select_db($YOUR_DB, $db); 


$query = "select uuid, reviewed from study"; 
$result = mysql_query($query);
$res = array();
while ($row = mysql_fetch_array($result))
{
	die(($your_path.$row['uuid'].'.pdf'));
	if (file_exists($your_path.$row['uuid'].'.pdf'))
	{
		$res[] = $row['uuid'];
	}
}

foreach ($res as $id)
{
	$query = "update study set reviewed = 1, REVIEWED_USER_ID = , REVIEWED_DATE = sysdate where uuid = $id"; 
	die($query);
	if (!mysql_query($query))
		die("An error occured: $query");	
}



?>