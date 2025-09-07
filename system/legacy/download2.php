<?php 
//require_once "locale.php";

function zipFiles2(&$files, $filename)
{
    if (count($files) == 0) {
        die ("<p><font color=red>" . pacsone_gettext("No files to compress.") . "</font></p>");
    }
    error_reporting(E_ERROR);
    ob_start();
    // Allow sufficient execution time to the script:
    set_time_limit(0);

    $zip = new ZipArchive;
	
    
  //  $data = $zipfile -> file();
    /*
     * must use a temporary file as buffer when downloading a large number of
     * images because of the PHP output buffer control
     */
    $tempname = tempnam(getenv("TEMP"), "PacsOne");
  //  unlink($tempname);
    $tempname = $tempname . ".zip";
	//$tempname='c:\\zip\\a.zip';
	$zip->open($tempname,ZipArchive::CREATE);
	$rootPath="c:\\efilm";
	$efilm_files= new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);
	foreach ($efilm_files as $name => $file)
	{
		// Skip directories (they would be added automatically)
		if (!$file->isDir())
		{
        // Get real and relative path for current file
			$filePath = $file->getRealPath();
		
			$relativePath = substr($filePath, strlen($rootPath) + 1);
			$filePath = str_replace('\\','/',$filePath);
			$relativePath = str_replace('\\','/',$relativePath);
		//   echo $relativePath,'</br />';
		
        // Add current file to archive
		//$zip->addFile('C:/efilm/efCmprss.dll','dimpl8.dll');
			$zip->addFile($filePath, $relativePath);
		//echo $zip->status;
		}
	}
	//$zip->close();exit;
	foreach ($files as $uid => $path) {
		//echo $path;
	    $zip->addFile($path, 'dicomdir\\'.$uid.'.dcm');
		//$zip->addFromString('dicomdir\\'.$uid.'.dcm',file_get_contents($path));
    }
	$result=$zip->close();
	if($result){
		echo json_encode('success'=>true,'file'=> $tempname);
	}else{
		echo json_encode('success'=>false,'msg'=>'Zip error');
	}
	
    exit;
    // MSIE handling of Content-Disposition
    if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
        $contentType = "application/force-download";
        $filename = str_replace(".", "_", $filename);
        $filename .= ".zip";
        $disposition = "Content-disposition: file; filename=$filename";
    } else {
        $contentType = "application/x-zip";
        $filename .= ".zip";
        $disposition = "Content-disposition: attachment; filename=$filename";
    }
    while (@ob_end_clean());
    // the next three lines force an immediate download of the zip file: 
    header("Cache-Control: cache, must-revalidate");   
    header("Pragma: public");
    header("Content-type: $contentType");    
    header($disposition);    
    header("Content-length: " . filesize($tempname));    
    /*
     * Must use temporary file here instead
     * 
     * echo $data;
     */
    $fp = fopen($tempname, "rb");
    fpassthru($fp);
    fclose($fp);
    //unlink($tempname);
    exit();
}




?>
