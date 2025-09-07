<?php
session_cache_limiter('private');
session_start();

include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");

include_once('dicom.php');

import('system.models.Image');
import('system.core.orm.DataController');
include($_SERVER["DOCUMENT_ROOT"]."/system/utilities/debug.php");

$uid = $_REQUEST['uid'];
//$uid = urlClean($uid, 64);
$controller = new DataController($DB_DATABASE);
$image = $controller->Find(new Image(array("uuid"=>$uid)))->toObject("Image");
$image = $image[0];
$path = $image->path;
$outputPath = "";
$tagsXML = "";
if (file_exists($path)) {
    $url = "file://$path";
    // display report content
    $dump = new RawTags($path);
    $tagsXML = $dump->returnXML();

	//Converting
	$inputFilePath = $_REQUEST["dcm"];
	$pathArray = explode("/", $inputFilePath);
	$inputFileName = $pathArray[count($pathArray)-1];
	
	$outputPath = "images/".$image->uuid.".jpg";
	
	$inputFilePath = implode("\\", explode("/", $inputFilePath));
	try{
		if(!file_exists($outputPath))
		{
            $cmd = '"'.$_SERVER['DOCUMENT_ROOT'].'/bin/dcmj2pnm.exe" +oj +Wm "'.$inputFilePath.'" "'.$INSTALL_DIRECTORY.$DW_DicomViewerPath.$outputPath.'"' ;

            $WshShell = new COM("WScript.Shell");
            $oExec = $WshShell->Run($cmd, 0, true);
            if (!$oExec)
            {
                debug("failed to convert image to JPG\n");
                debug("command line:  $cmd\n ");
            }
			
			if(!file_exists($INSTALL_DIRECTORY.$DW_DicomViewerPath.$outputPath))
			{
                #if (!extension_loaded('php_imagick'))
                #    dl ('php_imagick.' . PHP_SHLIB_SUFFIX);
                $handle = imagick_readimage($inputFilePath);
                if (imagick_iserror($handle)) {
                    $reason      = imagick_failedreason($handle);
                    $description = imagick_faileddescription($handle);
                    debug("imagick_readimage() failed!\n");
                    debug("Reason: $reason<BR>\nDescription: $description<BR>\n");
                    exit();
                }
                if (!imagick_writeimage($handle, $INSTALL_DIRECTORY.$DW_DicomViewerPath.$outputPath))
                {
                    $reason  = imagick_failedreason($handle) ;
                    $description = imagick_faileddescription( $handle ) ;
                    debug("failed to convert image to JPG\n");
                    debug("Reason: $reason<BR>\nDescription: $description<BR>\n");
                    exit();
                }
                imagick_destroyhandle($handle);
			}
		}
		/*
		
		*/
	}catch(Exception $e){
		//lets take the image magic route somehow it can't process the image
		if(!file_exists($outputPath))
		{
			$execCommand="dcm2jpg.exe -o images $inputFilePath";
			exec($execCommand);
			rename("images/".$inputFileName.".jpg", $outputPath);
		}
	}
	
}
echo "<fileInfo><path>$outputPath</path>$tagsXML</fileInfo>";

?>