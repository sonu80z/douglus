<?php
import("system.models.patient");
import("system.models.study");
import("system.controls.control");
import("system.core.database.MySQLDatabase");


class StudyControl extends Control
{
    var $controller;
    
    public function __construct()
    {
        parent::__construct();
        include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
        $this->controller = new DataController($DB_DATABASE);        
    }
    
    public function PatientList($args)
    {
        $params = $this->GetQueryParams($args, array("start"=>"0", "limit"=>"1000", "dir"=>"ASC", "sort"=>"lastname"));
        if (isset ($args['query']))
            $params->conditions = "concat(upper(firstname), ' ', upper(lastname), origid) like '%".  strtoupper($args['query'])."%'";
        $params->limit = 1000;
        $patients = $this->controller->Find(new Patient(), $params)->toObject("Patient");
        if (!$patients)
        {
            $return["failure"]  = "An error uccured when trying to fetch patients data";
            return json_encode($return);
        }
        $c = count($patients);
        $i = 0;
        $arr = array();
        $arr_item = array();
        for($i = 0; $i < $c; $i++)
        {
            $arr_item['origid']     = $patients[$i]->origid;
            $arr_item['firstname']  = $patients[$i]->firstname;
            $arr_item['lastname']   = $patients[$i]->lastname;
            $arr_item['patientname']= $patients[$i]->firstname.' '.$patients[$i]->lastname;
            $arr_item['birthdate']  = $patients[$i]->birthdate;
            if (!in_array($arr_item, $arr))
                $arr[] = $arr_item;
        }
        $return["success"]  = "true";
        $return["data"]  =  $arr;
        return json_encode($return);
    }
    public function referringPhysicianList($args)
    {
        $params = $this->GetQueryParams($args, array("start"=>"0", /*"limit"=>"20", */"dir"=>"ASC", "sort"=>"referringphysician"));
        $params->conditions = "referringphysician != '' and upper(referringphysician) like '%".strtoupper($args['query'])."%'";
        $params->limit = 1000;
        $studies = $this->controller->Find(new Study(), $params)->toObject("Study");
        if (!$studies)
        {
            $return["failure"]  = "An error uccured when trying to fetch referringphysicians";
            return json_encode($return);
        }
        
        $c = count($studies);
        $i = 0;
        $arr = array();
        $arr_item = array();
        for($i = 0; $i < $c; $i++)
        {
            $arr_item['referringphysician']     = $studies[$i]->referringphysician;
            if (!in_array($arr_item, $arr))
                $arr[] =  $arr_item;
        }
        $return["success"]  = "true";
        $return["data"]  = $arr;
        return json_encode($return);
    }
    
    public function Add($args)
    {
        $return["success"]  = "false";
        $dbController = new DataController();
        if (isset($args['new_patient']))
        {
            $patient = new Patient();
            
            $patient->origid = strtoupper($args['neworigid']);
            $patient->origid = preg_replace ("/[^a-zA-Z0-9]/","",$patient->origid);
            
            $patient->lastname = strtoupper($args['lastname']);
            $patient->firstname = strtoupper($args['patname']);
            $patient->middlename = strtoupper($args['middlename']);
            $patient->birthdate = strtoupper($args['birthday']);
            $patient->sex = strtoupper($args['sex']);
            
            $res = $dbController->Add($patient);
            if (!$res->rowsAffected)
            {
                $return['error_msg'] = 'The Patient ID you entered confilts with existing Patient,  Please confirm and re-enter';
                return json_encode($return);
            }
            $args['patientid'] = $patient->origid;
        }
        
        $study = new Study();
        $study->studydate = strtoupper($args['studydate']);
        $study->modalities = strtoupper($args['modality']);
        $study->studytime = strtoupper($args['studytime']);
        $study->description = strtoupper($args['description']);
        $study->referringphysician = strtoupper($args['referringphysician']);
        $study->patientid = strtoupper($args['patientid']);
        $study->sourceae ='PRS';
        $study->updated = date('Y-m-d H:i:s');

        $dateobj=strtotime($study->studydate);
        $newdate = date('m.d.Y',$dateobj);

        $study->uuid = '1.2.'.$newdate.  str_replace(':', '.', $study->studytime); 
//        die('<pre>'.print_r($study, 1).'</>');
        $res = $dbController->Add($study);
		// print_r($res);
        if (!$res->rowsAffected)
        {
            $return['error_msg'] = 'Could not insert new study. An error occured';
            return json_encode($return);
        }
        $return["success"]  = "true";
        return json_encode($return);
    }

    public function getTechNote($args)
    {
      include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
      include_once $_SERVER["DOCUMENT_ROOT"]."/system/utilities/utilFunctions.php";
        
        $NOTES_DIR = $INSTALL_DIRECTORY.'tech_notes/';
        $res = findFile($NOTES_DIR, $args['study_id']);

        if ($res){
            $file = str_replace($NOTES_DIR, '', $res);
            
            header("Content-type: application/octet-stream");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Disposition: attachment; filename=\"$file\"");
            header("Content-Length: ".filesize($res));

            echo file_get_contents($NOTES_DIR.$file);
        }
        else
            echo "file not found";
    }
}


?>