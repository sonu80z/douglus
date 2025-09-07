<?php

class Downloads
{
	public function logBurnCDActivity ($args)
	{
		$ip = "";
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		
		$path = $_SERVER["DOCUMENT_ROOT"]."/system/logs/burncd/";
		if(!file_exists($path))
			mkdir($path, 0700);
		$f = fopen($path ."activity-log-" . @date("m-Y") . ".txt", "a");
		$data = @date("m/d/Y H:i:s") ."REMOTE_ADDRESS:".$ip."OUTPUT:".$args['output']."ERROR:".$args['error']."EXITCODE:".$args['exiterror']."\r\n\r\n\r\n";
		fwrite($f, $data);
		fclose($f);
	}

	public function getCDBurner($args)
	{
		$jobID = $args['job_id'];

		include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
		$patientNameFile = $PACSONE_EXPORT_PATH.$args['job_id'].'/patient_name.txt';
		$patientName = '';
		if (file_exists($patientNameFile))
			$patientName = file_get_contents($patientNameFile);
		if ($patientName)
			$patientName = 'MDICDBurner('.$patientName.').exe';
		else
			$patientName = 'MDICDBurner.exe';
		
		$file = $_SERVER['DOCUMENT_ROOT'].'bin/MDICDBurner.exe';
		
		
//test		
//		$patientName = rand().$patientName;
//		$file = 'C:/rentacoder/dpotter/CDBurner/Release/test4.exe';
		
		$keyUrl = '***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345***12345';
		$keyUrlPath = '***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456***123456';
		$url = 'https://'.$_SERVER['HTTP_HOST'].'/system/dispatch.php?control=Downloads&method=getImage&job_id='.$jobID;

		$url 		= $this->appendSlashes2url($url, $keyUrl);
		$basePath 	= $this->appendSlashes2url('https://'.$_SERVER['HTTP_HOST'].'', $keyUrlPath);
		
		header("Content-type: application/octet-stream");
		header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header ("Content-Disposition: attachment; filename=\"$patientName\"");
		header ("Content-Length: ".filesize($file));
		
		$currentPart 	= '';
		$priorPart 		= '';
		$i = 1;
		
		$f = fopen($file, 'r');
		while(!feof($f))
		{
			$priorPart 		= $currentPart;
			$currentPart 	= fread($f, 1024*50);
			$d = $priorPart.$currentPart;
			$d = str_replace($keyUrl, $url, $d);
			$d = str_replace($keyUrlPath, $basePath, $d);
			
			if ($i == 1)
				$i = 0;
			else 
			{
				$i = 1;
				echo $d;
			}
		}
		
		if ($i == 0)
			echo $d;
		
		fclose($f);
	}
	
	public function appendSlashes2url($url, $keyUrl)
	{
		$keyUrlLen = strlen($keyUrl);
		$urlLen = strlen($url);
		if ($keyUrlLen > $urlLen)
		{
			for ($i = 0; $i < $keyUrlLen - $urlLen; $i++)
			{
				$url.='\\';
			}
		}
		return $url;
	}
	
	public function getImage($args)
	{
		include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
		$file = $PACSONE_EXPORT_PATH.$args['job_id'].'/'.$args['job_id'].'.iso';
//		$file		= $_SERVER['DOCUMENT_ROOT'].'/system/MDI_everywhere.iso';
		
		header("Content-type: application/octet-stream");
		header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header ("Content-Disposition: attachment; filename=\"image.iso\"");
		header ("Content-Length: ".filesize($file));
		
		$f = fopen($file, 'r');
		while(!feof($f))
		{
			set_time_limit(30);
			echo fread($f, 1024*25);
		}
		fclose($f);
	}
}

?>