<?php 
//this will turn error reporting off for this page.
error_reporting(0);
require_once "locale.php";
define('ADD_DIR',"c:\\efilm");
/* 

Zip file creation class 
makes zip files on the fly... 

use the functions add_dir() and add_file() to build the zip file; 
see example code below 

by Eric Mueller 
http://www.themepark.com 

v1.1 9-20-01 
  - added comments to example 

v1.0 2-5-01 

initial version with: 
  - class appearance 
  - add_file() and file() methods 
  - gzcompress() output hacking 
by Denis O.Philippov, webmaster@atlant.ru, http://www.atlant.ru 

*/   

// official ZIP file format: http://www.pkware.com/appnote.txt 

class zipfile    
{    
    var $num_entries = 0;
    var $datasec = "";
    var $ctrl_dir = ""; // central directory     
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record 
    var $old_offset = 0;   

    /**
     * Converts an Unix timestamp to a four byte DOS date and time format (date
     * in high two bytes, time in low two bytes allowing magnitude comparison).
     *
     * @param  integer  the current Unix timestamp
     *
     * @return integer  the current date in a four byte DOS format
     *
     * @access private
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method

    function add_dir($name, $time = 0)     

    // adds "directory" to archive - do this before putting any files in directory! 
    // $name - name of directory... like this: "path/" 
    // ...then you can add files using add_file with names like "path/file.txt" 
    {    
        $name = str_replace("\\", "/", $name);    
        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1]; 
        eval('$hexdtime = "' . $hexdtime . '";');

        $this->datasec .= "\x50\x4b\x03\x04";   
        $this->datasec .= "\x0a\x00";    // ver needed to extract 
        $this->datasec .= "\x00\x00";    // gen purpose bit flag 
        $this->datasec .= "\x00\x00";    // compression method 
        $this->datasec .= $hexdtime;     // last mod time and date 

        $this->datasec .= pack("V",0); // crc32 
        $this->datasec .= pack("V",0); //compressed filesize 
        $this->datasec .= pack("V",0); //uncompressed filesize 
        $this->datasec .= pack("v", strlen($name) ); //length of pathname 
        $this->datasec .= pack("v", 0 ); //extra field length 
        $this->datasec .= $name;    
        // end of "local file header" segment 

        // no "file data" segment for path 

        // "data descriptor" segment (optional but necessary if archive is not served as file) 
        $this->datasec .= pack("V",0); //crc32 
        $this->datasec .= pack("V",0); //compressed filesize 
        $this->datasec .= pack("V",0); //uncompressed filesize 

        $new_offset = strlen($this->datasec);   

        // ext. file attributes mirrors MS-DOS directory attr byte, detailed 
        // at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp 

        // now add to central record 
        $cdrec = "\x50\x4b\x01\x02";   
        $cdrec .="\x00\x00";    // version made by 
        $cdrec .="\x0a\x00";    // version needed to extract 
        $cdrec .="\x00\x00";    // gen purpose bit flag 
        $cdrec .="\x00\x00";    // compression method 
        $cdrec .= $hexdtime;    // last mod time & date 
        $cdrec .= pack("V",0); // crc32 
        $cdrec .= pack("V",0); //compressed filesize 
        $cdrec .= pack("V",0); //uncompressed filesize 
        $cdrec .= pack("v", strlen($name) ); //length of filename 
        $cdrec .= pack("v", 0 ); //extra field length     
        $cdrec .= pack("v", 0 ); //file comment length 
        $cdrec .= pack("v", 0 ); //disk number start 
        $cdrec .= pack("v", 0 ); //internal file attributes 
        $ext = "\x00\x00\x10\x00";   
        $ext = "\xff\xff\xff\xff";    
        $cdrec .= pack("V", 16 ); //external file attributes  - 'directory' bit set 

        $cdrec .= pack("V", $this -> old_offset ); //relative offset of local header 
        $this -> old_offset = $new_offset;   

        $cdrec .= $name;    
        // optional extra field, file comment goes here 
        // save to array 
        $this -> ctrl_dir .= $cdrec;
        $this -> num_entries++;
           
    }   


    function add_file(&$data, $name, $time = 0)     

    // adds "file" to archive     
    // $data - file contents 
    // $name - name of file in archive. Add path if your want 

    {    
        $name = str_replace("\\", "/", $name);    
        //$name = str_replace("\\", "\\\\", $name); 
        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1]; 
        eval('$hexdtime = "' . $hexdtime . '";');

        $this->datasec .= "\x50\x4b\x03\x04";   
        $this->datasec .= "\x14\x00";    // ver needed to extract 
        $this->datasec .= "\x00\x00";    // gen purpose bit flag 
        $this->datasec .= "\x08\x00";    // compression method 
        $this->datasec .= $hexdtime;     // last mod time and date 

        $unc_len = strlen($data);    
        $crc = crc32($data);    
        $zdata = gzcompress($data);    
        $zdata = substr( substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug 
        $c_len = strlen($zdata);    
        $this->datasec .= pack("V",$crc); // crc32 
        $this->datasec .= pack("V",$c_len); //compressed filesize 
        $this->datasec .= pack("V",$unc_len); //uncompressed filesize 
        $this->datasec .= pack("v", strlen($name) ); //length of filename 
        $this->datasec .= pack("v", 0 ); //extra field length 
        $this->datasec .= $name;    
        // end of "local file header" segment 
           
        // "file data" segment 
        $this->datasec .= $zdata;    

        // "data descriptor" segment (optional but necessary if archive is not served as file) 
        /*
        $this->datasec .= pack("V",$crc); //crc32 
        $this->datasec .= pack("V",$c_len); //compressed filesize 
        $this->datasec .= pack("V",$unc_len); //uncompressed filesize 
        */

        $new_offset = strlen($this->datasec);   

        // now add to central directory record 
        $cdrec = "\x50\x4b\x01\x02";   
        $cdrec .="\x00\x00";    // version made by 
        $cdrec .="\x14\x00";    // version needed to extract 
        $cdrec .="\x00\x00";    // gen purpose bit flag 
        $cdrec .="\x08\x00";    // compression method 
        $cdrec .= $hexdtime;    // last mod time & date 
        $cdrec .= pack("V",$crc); // crc32 
        $cdrec .= pack("V",$c_len); //compressed filesize 
        $cdrec .= pack("V",$unc_len); //uncompressed filesize 
        $cdrec .= pack("v", strlen($name) ); //length of filename 
        $cdrec .= pack("v", 0 ); //extra field length     
        $cdrec .= pack("v", 0 ); //file comment length 
        $cdrec .= pack("v", 0 ); //disk number start 
        $cdrec .= pack("v", 0 ); //internal file attributes 
        $cdrec .= pack("V", 32 ); //external file attributes - 'archive' bit set 

        $cdrec .= pack("V", $this -> old_offset ); //relative offset of local header 
//        echo "old offset is ".$this->old_offset.", new offset is $new_offset<br>"; 
        $this -> old_offset = $new_offset;   

        $cdrec .= $name;    
        // optional extra field, file comment goes here 
        // save to central directory 
        $this -> ctrl_dir .= $cdrec;    
        $this -> num_entries++;
    }   

    function file() { // dump out file     
        return     
            $this->datasec.    
            $this -> ctrl_dir.    
            $this -> eof_ctrl_dir.    
            pack("v", $this -> num_entries).	    // total # of entries "on this disk" 
            pack("v", $this -> num_entries).        // total # of entries overall 
            pack("V", strlen($this->ctrl_dir)).     // size of central dir 
            pack("V", strlen($this->datasec)).      // offset to start of central dir 
            "\x00\x00";                             // .zip file comment length 
    }   
}    

function addFile(&$zipfile, $uid, $path) {
	$handle = fopen($path, "rb");
	$data = fread($handle, filesize($path));
	fclose($handle);
	$name = $uid . ".dcm";
    // add the binary data stored in the string 'filedata' 
    $zipfile -> add_file($data, "$name");    
	return $data;
}

function zipFiles(&$files, $filename)
{
    if (count($files) == 0) {
        die ("<p><font color=red>" . pacsone_gettext("No files to compress.") . "</font></p>");
    }
    error_reporting(E_ERROR);
    ob_start();
    // Allow sufficient execution time to the script:
    set_time_limit(0);

    $zipfile = new zipfile();
    foreach ($files as $uid => $path) {
	    addFile($zipfile, 'dicomdir\\'.$uid.'.dcm', $path);
    }
    $data = $zipfile -> file();
    /*
     * must use a temporary file as buffer when downloading a large number of
     * images because of the PHP output buffer control
     */
    $tempname = tempnam(getenv("TEMP"), "PacsOne");
    unlink($tempname);
    $tempname = $tempname . ".zip";
    $fp = fopen($tempname, "w+b");
    fwrite($fp, $data);
    fclose($fp);
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
  //  header("Cache-Control: cache, must-revalidate");   
   // header("Pragma: public");
  //  header("Content-type: $contentType");    
   // header($disposition);    
 //   header("Content-length: " . filesize($tempname));    
    /*
     * Must use temporary file here instead
     * 
     * echo $data;
     */
	 //echo __LINE__;exit;
	 $fr = fopen($tempname, "rb");
	 $fw = fopen('c:\\zip\\a.zip', "wb");
	 if($fw===false){
		 echo 'write file';
		 exit;
	 }
	 while (($buffer = fgets($fr, 4096)) !== false) {
        fputs($fw,$buffer);
    }
	fclose($fw);
	fclose($fr);
	echo __LINE__;exit;
	 echo (int)file_put_contents('c:\\zip\\a.zip',file_get_contents($tempname) );
	 echo __LINE__;exit;
    $fp = fopen($tempname, "rb");
    fpassthru($fp);
    fclose($fp);
    unlink($tempname);
    exit();
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
		$this->zip->open($this->filename,ZipArchive::CREATE);
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
			
				$this->zip->addFile($filePath, $relativePath);
				echo $zip->status;
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

function zipFiles2(&$files, $filename)
{
    if (count($files) == 0) {
        die ("<p><font color=red>" . pacsone_gettext("No files to compress.") . "</font></p>");
    }
    error_reporting(E_ERROR);
    ob_start();
    // Allow sufficient execution time to the script:
    set_time_limit(0);

    $zip = new ZipHelper;
	
	$zip->init();
	
	//echo $tempname=$zip->getFilename();exit;
	
	$zip->addDirRecursively(ADD_DIR);
	
	
	$result=$zip->close();
	
	
	
	$tempname=$zip->getFilename();
	
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
	if($result){
		echo json_encode(array('success'=>true,'id'=> $id));
	}else{
		echo json_encode(array('success'=>false,'msg'=>'Zip error','result'=>(bool)$result));
	}
	
	exit;
	
    
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
	$rootPath=ADD_DIR;
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
	if($result){
		echo json_encode(array('success'=>true,'id'=> $id));
	}else{
		echo json_encode(array('success'=>false,'msg'=>'Zip error'));
	}
	
    exit;
}

function downloadZip($id){
    // MSIE handling of Content-Disposition
	if(empty($_SESSION['zipfiles'][$id])){
		echo 'Error: Zip file not created';
		exit;
	}
	$filename=$_SESSION['zipfiles'][$id]['filename'];
	$tempname=$_SESSION['zipfiles'][$id]['file'];	
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
    unlink($tempname);
    exit();
}


function downloadIso($id){
	
    // MSIE handling of Content-Disposition
	if(empty($_SESSION['isofiles'][$id])){
		echo 'Error: ISO file not created';
		exit;
	}
	$filename=$_SESSION['isofiles'][$id]['filename'];
	$tempname=$_SESSION['isofiles'][$id]['file'];	
    if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
        $contentType = "application/force-download";
        $filename = str_replace(".", "_", $filename);
        $filename .= ".iso";
        $disposition = "Content-disposition: file; filename=$filename";
    } else {
        $contentType = "application/x-zip";
        $filename .= ".iso";
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
    unlink($tempname);
    exit();
}


function zipFiles3(&$files, $filename)
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
	$tempname='c:\\zip\\a.zip';
	$zip->open($tempname,ZipArchive::CREATE);
	$rootPath="c:\\efilm";
	$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
		if(pathinfo($filePath,PATHINFO_EXTENSION)!='dll'){
			continue;
		}
		$relativePath = substr($filePath, strlen($rootPath) + 1);
		$filePath = str_replace('\\','/',$filePath);
		$relativePath = str_replace('\\','/',$relativePath);
     //   echo $relativePath,'</br />';
		
        // Add current file to archive
		//$zip->addFile('C:/efilm/efCmprss.dll','dimpl8.dll');
      //  echo (int)$zip->addFile($filePath, $relativePath);
		//echo $zip->status;
    }
}
	//$zip->close();exit;
	foreach ($files as $uid => $path) {
		//echo $path;
	    $zip->addFile($path, 'dicomdir\\'.$uid.'.dcm');
    }
	echo (int)$zip->close();
	echo 'h';
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
