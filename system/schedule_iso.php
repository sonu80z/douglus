<?php
define('SCRIPT_DIR',dirname(__FILE__));
//define('ZIP_ISO_DIR',SCRIPT_DIR.DIRECTORY_SEPARATOR.'zip_iso'.DIRECTORY_SEPARATOR);
define('ZIP_ISO_DIR','C:\\Program Files\\PacsOne\\php\\RPRS\\system\\zip_iso\\');
define('EXPORT_DIR','C:/Program Files/PacsOne/export/');
//define('EFILM_DIR','C:\\efilmLite');
define('EFILM_DIR','C:\\eFilmLite_3.0');
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','%Mobile!23');
define('DB_DB','mayipacs');
$db=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_DB);
if($db->connect_errno){
	echo 'Database connection fail';
	exit(1);
}
//echo ZIP_ISO_DIR;exit;
$result=$db->query('SELECT * FROM schedule WHERE `status`="pending" ORDER BY `id` DESC ');
while($record=$result->fetch_assoc()){
	$q = "select * from dbjob WHERE id={$record['dbjob_id']}";
	$result2=$db->query($q);
	$record2=$result2->fetch_assoc();
	if($record2['status']=='100% Complete' OR $record2['status']=='success'){
		//echo $record2['status'],"\n";
		if($record['type']=='zip'){
			$tempname=zipbuilder2($record);
		}else{
			$tempname=isofile($record);
		}
		$q = "UPDATE schedule SET `status` = 'complete', `tempname`='{$tempname}'  WHERE id={$record['id']}";
		$result3=$db->query($q);
	//	break;
	//	zipBuilder($filename);
		//echo json_encode(array('success'=>true));
	}elseif($record2['status']=='failed'){
		//echo json_encode(array('success'=>false,'msg'=>'Error in pacsone '.$id,'raw'=>$result));
	//	break;
	}
}

function zipbuilder2($record){
	$zip=new ZipArchive;
	$tempname=random_str().'-'.$record['id'].'.zip';
	$zip_path=ZIP_ISO_DIR.$record['filename'].'-'.$tempname;
	$zip->open($zip_path,ZipArchive::CREATE);
	$filename=$record['filename'];
	//if(file_exists())
	addDirRecursively($zip,EXPORT_DIR.$filename.'/VOL1');
	if(defined('EFILM_DIR')){
		//$zip->addDirRecursively('C:/efilm');
		addDirRecursively($zip,EFILM_DIR);
	}
	//$zip->addFromString('test.txt', 'file content goes here');
	$zip->close();
	return $tempname;
}

function addDirRecursively($zip,$dir){
	$files= new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir),
			RecursiveIteratorIterator::LEAVES_ONLY
		);
	foreach ($files as $name => $file)
		{
		//	echo $file->getRealPath(),"\n";
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
			// Get real and relative path for current file
				$filePath = $file->getRealPath();
			
				$relativePath = substr($filePath, strlen($dir) + 1);
				$filePath = str_replace('\\','/',$filePath);
				$relativePath = str_replace('\\','/',$relativePath);
			//   echo $relativePath,'</br />';
			
			// Add current file to archive
			//$zip->addFile('C:/efilm/efCmprss.dll','dimpl8.dll');
				$return=$zip->addFile($filePath, $relativePath);
				
				/*if($return===false){
					return false;
				}*/
			//echo $zip->status;
			}
		}
}

function random_str($len=10){
	$str='';
	for($i=0;$i<$len;$i++){
		$rand=rand(ord('a'),ord('z'));
		$str.=chr($rand);
	}
	return $str;
}

function zipBuilder($record){
	$filename=$record['filename'];
	if(empty($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Invalid file name'));
		exit;
	}
	//include_once 'legacy/download.php';
	error_reporting(E_ALL);
	$tempname=random_str().'-'.$record['id'].'.zip';
	$zip_path=ZIP_ISO_DIR.$tempname;
    $zip = new ZipHelper;
	$zip->init('C:\\Program Files\\PacsOne\\php\\zip_iso\\test.zip');
	//return;
	//echo $tempname=$zip->getFilename();exit;
	$return=$zip->addDirRecursively(EXPORT_DIR.$filename);
	echo (int)$zip->close();
	return;
	/*if($return==false){
		echo 'Some files fail to zip';
		return false;
	}*/
	
	if(defined('EFILM_DIR')){
		//$zip->addDirRecursively('C:/efilm');
		$zip->addDirRecursively(EFILM_DIR);
	}
	$result=$zip->close();
	
	//if(!$re)
	
	//$tempname=$zip->getFilename();
	
		return $tempname;
			if(empty($_SESSION["zipfiles"])){
			//	print_r($_SESSION);
				$_SESSION['zipfiles']=array();
				//print_r($_SESSION["AUTH_USER"]['zipfiles']);
				$_SESSION['zipfiles'][]=array('file'=>$tempname,'time'=>time(),'filename'=>$filename);
				$id=0;
			}else{
				$_SESSION['zipfiles'][]=array('file'=>$tempname,'time'=>time(),'filename'=>$filename);
				$id=count($_SESSION['zipfiles'])-1;
			}
			echo json_encode(array('success'=>true,'id'=>$id));
		
		exit;
		//echo "command returned $return_value\n";
	
}

function isofile($record){
	$filename=$record['filename'];
	if(empty($filename)){
		echo json_encode(array('success'=>false,'msg'=>'Invalid file name'));
		exit;
	}
    $files=array();
	//$tempname = tempnam(getenv("TEMP"), "PacsOne");
	$tempname=random_str().'-'.$record['id'].'.iso';
	$iso_path=ZIP_ISO_DIR.$record['filename'].'-'.$tempname;
	$descriptorspec = array(
	   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
	   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
	   2 => array("pipe", "w") // stderr is a file to write to
	);
	$iso_jar_dir='C:\Program Files\PacsOne\php\RPRS'.DIRECTORY_SEPARATOR.'java';
	//$process=proc_open('java -jar main.jar "'.$iso_path.'" "'.EXPORT_DIR.$filename.'" "C:/efilm/"',$descriptorspec,$pipes,$iso_jar_dir);
	//echo 'java -jar main.jar "'.$iso_path.'" "'.EXPORT_DIR.$filename.'" "'.EFILM_DIR.'"';exit;
	$process=proc_open('java -jar main.jar "'.$iso_path.'" "'.EXPORT_DIR.$filename.'" "'.EFILM_DIR.'"',$descriptorspec,$pipes,$iso_jar_dir);
	if (is_resource($process)) {
		
		fwrite($pipes[0], implode("\r\n", $files));
		fclose($pipes[0]);

		$msg=stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		
		echo stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		// It is important that you close any pipes before calling
		// proc_close in order to avoid a deadlock
		$return_value = proc_close($process);
		if($return_value){
			echo json_encode(array('succes'=>false,'msg'=>$msg));
			exit;
		}
		if(!file_exists( $iso_path)){
			echo json_encode(array('succes'=>false,'msg'=>'File not formed'));
			exit;
		}
		if(!filesize($iso_path)){
			echo json_encode(array('succes'=>false,'msg'=>'Zero file size'));
			exit;
		}
		if(!$return_value){
			return $tempname;			
		}
		else{
			echo json_encode(array('success'=>false,'msg'=>$msg));
		}
		exit;
		//echo "command returned $return_value\n";
	}else{
		echo json_encode(array('success'=>false,'msg'=>'Process not started'));
		exit;
	}
}

class ZipHelper{
	private $filename;
	private $zip;
	function __construct(){
	}
	function init($filename=null){
		if(empty($filename)){
			$tempname = tempnam(getenv("TEMP"), "PacsOne");
			$tempname = $tempname . ".zip";
			$this->filename=$tempname;
		}else{
			$this->filename=$filename;
		}
		$this->zip= new ZipArchive;
		echo $this->filename;
		echo (int)$this->zip->open($this->filename,ZipArchive::CREATE);
		//$this->zip->addFromString('test.txt', 'file content goes here');
		//echo (int)$this->zip->close();
	}
	function addDirRecursively($dir){
		$files= new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir),
			RecursiveIteratorIterator::LEAVES_ONLY
		);
		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
			// Get real and relative path for current file
				$filePath = $file->getRealPath();
			
				$relativePath = substr($filePath, strlen($dir) + 1);
				$filePath = str_replace('\\','/',$filePath);
				$relativePath = str_replace('\\','/',$relativePath);
			//   echo $relativePath,'</br />';
			
			// Add current file to archive
			//$zip->addFile('C:/efilm/efCmprss.dll','dimpl8.dll');
				$return=$this->zip->addFile($filePath, $relativePath);
				echo (int)$return;
				/*if($return===false){
					return false;
				}*/
			//echo $zip->status;
			}
		}
	}
	function addFile($file,$path){
		//$this->zip->addFile($path, 'dicomdir\\'.$uid.'.dcm');
		return $this->zip->addFile($file,$path);
		//echo $this->zip->getStatusString();
	}
	function close(){
		$result=$this->zip->close();
		return $result;
	}
	function getFilename(){
		return $this->filename;
	}
}
