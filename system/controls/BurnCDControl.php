<?php
// sequrity check
if (!isset($_SESSION))
	session_start();
import("system.models.User");
if(!isset($_SESSION['AUTH_USER']))
	die('Please login first');


function copy_directory( $source, $destination ) 
{
	if ( is_dir( $source ) ) {
		@mkdir( $destination );
		$directory = dir( $source );
		while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			if ( $readdirectory == '.' || $readdirectory == '..' ) {
				continue;
			}
			$PathDir = $source . '/' . $readdirectory; 
			if ( is_dir( $PathDir ) ) {
				copy_directory( $PathDir, $destination . '/' . $readdirectory );
				continue;
			}
			copy( $PathDir, $destination . '/' . $readdirectory );
		}
 
		$directory->close();
	}else {
		copy( $source, $destination );
	}
}    

function rrmdir($dir) 
{ 
	if (@is_dir($dir)) 
	{ 
		$objects = @scandir($dir); 
		foreach ($objects as $object) 
		{ 
			if ($object != "." && $object != "..") 
			{ 
				if (filetype($dir."/".$object) == "dir") 
					@rrmdir($dir."/".$object); 
				else @unlink($dir."/".$object); 
			} 
		} 
	reset($objects); 
	@rmdir($dir); 
	} 
} 

class BurnCDControl extends Control
{
    protected $error = '';
    
    public function setStatus($text, $progressID)
    {
    	$sql = "update remote_job_status set STATUS = '$text' where id = '$progressID'";
    	$db = MySQLDatabase::GetInstance();
    	$res = $db->ExecuteNonQuery($sql);
    	if ($res->error)
			return false;
    	else 
    		return true;
    }
    
    public function checkProcesStatus($jobID)
    {
    	$sql = "select status FROM dbjob where id = $jobID";
    	$db = MySQLDatabase::GetInstance();
    	$result = $db->ExecuteReader($sql);
    	$row = $result->GetNextAssoc();
    	return $row['status'];
    }

    public function getProcesStatus($args)
    {
    	$sql = "select status from remote_job_status where id = '$args[process_id]'";
    	$db = MySQLDatabase::GetInstance();
    	$result = $db->ExecuteReader($sql);
    	$row = $result->GetNextAssoc();

    	$jobIDText = $row['status'];
    	if (strpos($jobIDText, 'COMPLETED:') === 0)
    	{
 		    $this->return["status"] = $jobIDText;
		   	$this->return["success"]="true";
			return JSON::Encode($this->return);
    	}
    	
    	// a pattern ".ISO" as critical section
    	if (!strpos($jobIDText, '.ISO'))
    	{
    		$p1 = strpos($jobIDText, ':jobID=');
	    	$jobID = substr($jobIDText, $p1+strlen(':jobID='));
	    	if (!$jobID)
	    	{
				$this->return["error_msg"]= 'ERROR:Could not fetch jobID from the status table. Table message is "'.$jobIDText.'"';
				return JSON::Encode($this->return);		
	    	}
	    	$status = $this->checkProcesStatus($jobID);
	    	if ($status == 'failed')
	    	{
		    	$sql = "update remote_job_status set status='ERROR:External job return fail status. Process broken. Techinfo::jobID=$jobID' where id = '$args[process_id]'";
		    	$result = $db->ExecuteNonQuery($sql);
				$this->return["error_msg"]= "ERROR:External job return fail status. Process broken. Techinfo::jobID=$jobID";
				return JSON::Encode($this->return);
	    	}
	    	if ($status == 'success')
	    	{
		    	set_time_limit(180);
//		    	$sql = "update remote_job_status set status='IN_PROGRESS:Making a .ISO file:jobID=$jobID' where id = '$args[process_id]'";
//		    	$result = $db->ExecuteNonQuery($sql);
	
//		    	$sql = "select uuid from `dbjob` where id = $jobID";
//				$db = MySQLDatabase::GetInstance();
//				$result = $db->ExecuteReader($sql);
//				$row = $result->GetNextAssoc();
				include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
		    	$output = '';
		    	
		    	
	   	    	// coping the pdf reports
	    		@copy_directory($PACSONE_EXPORT_PATH.$jobID.'/reports', $PACSONE_EXPORT_PATH.$jobID.'/vol1'.'/reports');
		    	if (file_exists($PACSONE_EXPORT_PATH.$jobID.'/'.$jobID.'.flag'))
				{
					die('_');
				}

				file_put_contents($PACSONE_EXPORT_PATH.$jobID.'/'.$jobID.'.flag', '1');
				$res = $this->makeISOImage($PACSONE_EXPORT_PATH.$jobID.'/'.$jobID.'.iso', $PACSONE_EXPORT_PATH.$jobID.'/vol1', $output);
				unlink($PACSONE_EXPORT_PATH.$jobID.'/'.$jobID.'.flag');
		    	if (!$res)
		    	{
			    	$output = print_r($output, true);
			    	$errm = "ERROR:Could not make a ISO image ($output). Techinfo::jobID=$jobID";
		    		$sql = "update remote_job_status set status='$errm' where id = '$args[process_id]'";
			    	$result = $db->ExecuteNonQuery($sql);
					$this->return["error_msg"]= "$errm";
					return JSON::Encode($this->return);
		    	}
		    	$str = 'COMPLETED:'.$jobID;
	    		$sql = "update remote_job_status set status='$str' where id = '$args[process_id]'";
		    	$result = $db->ExecuteNonQuery($sql);
		    	
		    	$this->return["status"] = $str;
		   		$this->return["success"]="true";
				return JSON::Encode($this->return);
	    	}
    	}
    	
    	if($jobIDText)
		{
	   		$this->return["success"]="true";
			$this->return["status"] = $jobIDText;
		}
  		else
    	{
			$this->return["error_msg"]= $res->error;
    	}
		return JSON::Encode($this->return);
    }
    
    private function makeISOImage($ISOFullname, $targerDir, &$output)
    {
    	$cmd = '"'.$_SERVER['DOCUMENT_ROOT'].'bin/mkisofs/mkisofs.exe" -r -J -o "'.$ISOFullname.'" "'.$targerDir.'"';

    	$WshShell = new COM("WScript.Shell");
        $oExec = $WshShell->Run($cmd, 0, false);
//    	$res = exec($cmd, $output, $ret_val);
        if ($ret_val != 0)
        {
            return false;
        }

//    	$i = 0;
    	// wait, while app complete 
    	for ($i = 0; $i < 20; $i++)
    	{
    		sleep(1);
	    	if (file_exists($ISOFullname))
	    	{
	    		@rrmdir($targerDir);
	    		return true;
	    	}
    	}
        @rrmdir($targerDir);
        return false;
    }
    
//	public function ViewPatients($args)
//	{
//		$db = MySQLDatabase::GetInstance();
//		$sortField = 'patientid';
//		
//		$dir = 'ASC';	
//		
//		$search = '';	
//		
//		$WHERE = '';
//		if (strlen($search) > 0)
//			$WHERE = "where `patientid` like '%$search%' ";
//			
//		$sql = "SELECT `patientid`".
//		" FROM `study` ".
//		$WHERE.
//		" ORDER BY `$sortField` $dir";
//
//		return $db->ExecuteReader($sql)->toJSON();
//	}
    
    public function getProcessID($args)
    {
		$sdudiesString = $args['selected_studies'];

		$studiesArr = explode(',', $sdudiesString);
    	
		$sdudiesString = "'$sdudiesString'";
    	$sdudiesString = str_replace(',', "','", $sdudiesString);

		$db = MySQLDatabase::GetInstance();
		include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
		$exportDir = '';
		
    	$sql = "insert into dbjob (id, username, aetitle, type, class, uuid, schedule, priority, submittime, starttime, finishtime, status, retries, details) 
    	VALUES (NULL, 'root', '_650', 'export', 'study', 'empty_folder', -1, 100, sysdate(), NULL, NULL, 'failed', 0, 'row is registered... Need to update true values')";
    	$res = $db->ExecuteNonQuery($sql);
    	if ($res->error)
    	{
			$this->return["error_msg"]= 'Could not register the job: '.$res->error;
			return JSON::Encode($this->return);
    	}
		
    	$jobID = $res->lastId;

    	$exportDir = $PACSONE_EXPORT_PATH.$jobID;
		@mkdir($exportDir);
		$exportDir = str_replace('/', '//', $exportDir);

    	$sql = "update dbjob set status = 'submitted', details = '$exportDir', uuid='$jobID' where id = $jobID";
    	$res = $db->ExecuteNonQuery($sql);
    	if ($res->error)
    	{
			$this->return["error_msg"]= 'Could not update the registered job: '.$res->error;
			return JSON::Encode($this->return);
    	}
		
    	$sql = "SELECT uuid FROM `study` WHERE uuid in ($sdudiesString)";
    	$result = $db->ExecuteReader($sql);
    	
		while($row = $result->GetNextAssoc())
		{
	    	$sql = "insert into export (seq, jobid, class, uuid) 
	    	VALUES (NULL, $jobID, 'study', '$row[uuid]')";
	    	$res = $db->ExecuteNonQuery($sql);
	    	if ($res->error)
	    	{
				$this->return["error_msg"]= 'Cold not register the export item: '.$res->error;
				return JSON::Encode($this->return);
	    	}
		}
    	
    	$sql = "insert into remote_job_status (ID, STATUS) VALUES (null, 'IN_PROGRESS:jobID=$jobID')";
    	$res = $db->ExecuteNonQuery($sql);
    	if ($res->error)
    	{
			$this->return["error_msg"]= 'Could not register the process: '.$res->error;
			return JSON::Encode($this->return);
    	}
		
    	// coping the pdf reports
    	@mkdir($PACSONE_EXPORT_PATH.$jobID.'/reports');
    	foreach ($studiesArr as $study)
    	{
    		@copy($_SERVER["DOCUMENT_ROOT"].'/transcriptions/'.$study.'.pdf', $PACSONE_EXPORT_PATH.$jobID.'/reports/'.$study.'.pdf');
    	}
    	
    	// save a patients name into the file for updating CDBurner application name in the future
    	$sql = "select patientname from v_patient where origid in (select  distinct `patientid` FROM `study` WHERE `uuid` in ($sdudiesString))";
    	$result = $db->ExecuteReader($sql);
    	$row = $result->GetNextAssoc();
    	
    	file_put_contents($PACSONE_EXPORT_PATH.$jobID.'/patient_name.txt', $row['patientname']);
    	
    	$this->return["success"]="true";
    	$this->return["process_id"]= $res->lastId;
    	return JSON::Encode($this->return);
    }

    public function ViewPatientList($args)
	{
		$db = MySQLDatabase::GetInstance();
		$sortField = 'firstname';
		if (isset($args['sort']))
			$sortField = $args['sort'];
		
		$dir = 'ASC';
		if (isset($args['dir']))
			$dir = $args['dir'];
		
		$search = '';
		if (isset($args['search']))
			$search = $args['search'];
		
		$WHERE = '';
		if (strlen($search) > 0)
			$WHERE = "and (UPPER(p.`firstname`) like UPPER('%$search%') or UPPER(p.`lastname`) like UPPER('%$search%')) ";
			
		$sql = " SELECT DISTINCT p.`firstname`, p.`lastname`, p.`origid`, p.`institution` \n".
" FROM `v_patient` p, `study` s \n".
" WHERE p.`origid` = s.`patientid` \n".
		$WHERE.
		" ORDER BY `$sortField` $dir";

		return $db->ExecuteReader($sql)->toJSON();
	}
}
?>