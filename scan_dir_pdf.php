<?php

ini_set('display_errors',1);

set_time_limit(0);
ini_set('memory_limit',-1);

//Folder to scan
define('SCAN_DIR', 'C:\\Program Files (x86)\\PacsOne\php\\RPRS\\Transcriptions\\');

//connect db
mysql_connect('localhost', 'root', '662smain') or die ('Can not connect mysql');
mysql_select_db('archive') or die ('Can not select db');

//scan dir and store file need process into array
$iterator = new DirectoryIterator(SCAN_DIR);
$count = 0;
foreach ($iterator as $fileinfo)
{
	if ($fileinfo->isFile())
	{
		processFile($fileinfo->getFilename());
	}
}
mysql_close();

echo 'DONE';

function processFile($filename)
{
	$temp		= explode('.', $filename);
	unset($temp[count($temp)-1]);

	$fileIndb	= mysql_real_escape_string(implode('.', $temp));

	$query = "UPDATE study
				SET reviewed = 'read'
				WHERE uuid = '$fileIndb'";

	echo $query."<br/>";

	$result = mysql_query($query);
}