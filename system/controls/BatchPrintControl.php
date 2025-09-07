<?php
include_once('fix_mysql.inc.php');
import("system.models.User");
import("system.core.database.MySQLDatabase");
set_time_limit(10);

class BatchPrintControl extends Control {

    protected $error = '';

    public function __construct() {
        // sequrity check
        if (!isset($_SESSION))
            session_start();
        if (!isset($_SESSION['AUTH_USER']))
            die('Please login first');
		session_write_close();
    }

    public function gluePDFs($filesArray, $resultFileName) {
        $params = '';
        foreach ($filesArray as $file) {
            $fileName = $file;
            $params .= ' "' . $fileName . '"';
        }
        $execString = '"' . $_SERVER['DOCUMENT_ROOT'] . 'bin/pdftk.exe"' . $params . ' cat output "' . $resultFileName . '"';
        $execString = stripslashes($execString);
//		echo ($execString.'<br><br>');
        $ret_val = $this->exec_and_get_result($execString);
        if ($ret_val != 0) {
            $this->error = 'Operation filed. Try to run ' . print_r($execString, 1) . ' for detail';
            return false;
        }
        return true;
    }

    public function ViewStudies($args) {
        // sequrity check
        if (!isset($_SESSION))
            session_start();

        import("system.models.User");
        if (!isset($_SESSION['AUTH_USER']))
            exit();
        $user = unserialize($_SESSION['AUTH_USER']);

        $sql = "SELECT study.uuid,
study.id,
study.patientid,
study.studydate,
study.studytime,
study.accessionnum,
study.modalities,
study.referringphysician,
study.description,
study.readingphysician,
study.admittingdiagnoses,
study.interpretationauthor,
study.private,
study.received,
study.sourceae,
study.reviewed,
study.compressed,
study.matched,
study.studymatchworklist,
study.requestingphysician,
study.updated,
study.REVIEWED_USER_ID,
study.REVIEWED_DATE ";
        if ($args['dstart'] == $args['dstop'])
            $sql .= "FROM `study` inner join patient on study.patientid = patient.origid WHERE  (study.studydate between '" . $args['dstart'] . "' and date_add('" . $args['dstop'] . "', INTERVAL 1 DAY)) ";
        else
            $sql .= "FROM `study` inner join patient on study.patientid = patient.origid WHERE  (study.studydate between '" . $args['dstart'] . "' and '" . $args['dstop'] . "') ";

        $db = MySQLDatabase::GetInstance();


        if (true) {
            $privilegeCondition = " and (";
            $result = $db->ExecuteReader("select u.id, gu.groupid, g.filterdata, g.name, gt.name AS type from rprs_users u\n" .
                    "join rprs_group_users gu on u.id = gu.userid\n" .
                    "join rprs_groups g on gu.groupid = g.id\n" .
                    "join rprs_group_types gt on g.grouptypeid = gt.id\n" .
                    "where username = '" . $user->username . "'");
					/*print_r("select u.id, gu.groupid, g.filterdata, g.name, gt.name AS type from rprs_users u\n" .
                    "join rprs_group_users gu on u.id = gu.userid\n" .
                    "join rprs_groups g on gu.groupid = g.id\n" .
                    "join rprs_group_types gt on g.grouptypeid = gt.id\n" .
                    "where username = '" . $user->username . "'");*/
            if ($result->recordCount > 0 ) {
                $institutionData = array();
                $referringData = array();
                $consultData = array();
                $institutionName = '';
                while ($row = $result->GetNextAssoc()) {
                    $criteria = explode("|", $row["filterdata"]);
                    $insName = $row["name"];
                    if ($row["type"] == "Referring Physician") {
                        //$referringData = array_merge($criteria, $referringData);
                    } else if ($row["type"] == "Institution") {
                        $institutionData = array_merge($criteria, $institutionData);
                        //array_push($institutionData, var)
                        $institutionName = $insName;
                    } else if ($row["type"] == "Consult") {
                        $consultData = array_merge($criteria, $consultData);
                    }
                }
                if (sizeof($institutionData) > 0) {
                    if ($privilegeCondition != "\n(")
                    $privilegeCondition .= "patient.institution in ('" . join("','", $institutionData) . "')";
                    //$privilegeCondition .= "patient.institution = '" . $institutionName . "'";
                }
                if (sizeof($consultData) > 0) {
                    if ($privilegeCondition != "\n(")
                        $privilegeCondition .= " or ";
                    $privilegeCondition .= "patient.origid in ('" . join("','", $consultData) . "')";
                }
            }
            $sql .=$privilegeCondition . ')';
        }
		//print_r($sql);
        $studiesFiltered = $db->ExecuteReader($sql);
		//print_r($studiesFiltered);
        $filesArr = array();
        while ($rtudyArr = $studiesFiltered->GetNextAssoc()) {
            $fileID = $rtudyArr['uuid'];
            $filesArr[] = $fileID;
        }
//		print_r($filesArr);
        return $filesArr;
    }

    private function checkExists($path, $extension, $fileArr) {
        $res = array();
        foreach ($fileArr as $file) {
            if (file_exists($path . $file . $extension)) {
                $res[] = $path . $file . $extension;
            }
        }
        return $res;
    }

    function exec_and_get_result($cmd) {
        include($_SERVER["DOCUMENT_ROOT"] . "/system/config.php");
        $result = '';
        if (strtolower($PDF_TYPE_PROCESSING) == 'windows_com_exec') {
            $result = $this->_exec($cmd);
            return $result;
        } else {
            $output = array();
            $ret_val = 0;
            $res = exec($cmd, $output, $ret_val);
            if ($ret_val != 0) {
                return false;
            }
            $result = implode("\n", $output);
            if (0 == strlen($result))
                return 0;
            return $result;
        }
    }

    function _exec($cmd) {
        $WshShell = new COM("WScript.Shell");
        $oExec = $WshShell->Exec($cmd)->StdOut->ReadAll;
        return $oExec;
    }

    private function fileEncrypt($filein, $fileout, $password) {
        $execString = '"' . $_SERVER['DOCUMENT_ROOT'] . 'bin/pdfcrypt.exe" "' . $filein . '" "' . $fileout . '" decrypt ' . $password;
        $execString = stripslashes($execString);
//		echo ($execString.'<br><br>');

        $ret_str = $this->exec_and_get_result($execString);
        if (strpos($ret_str, 'Done.') > 0) {
            return true;
        }
            
        $this->error = "fileEncrypt('$filein', '$fileout', '$password') failure. Output is '$ret_str'. Tecnical information: ".$execString;
        return false;
    }

    public function getProcessID($args) {
        $sql = "insert into remote_job_status (ID, STATUS) VALUES (null, 'IN_PROGRESS')";
        $db = MySQLDatabase::GetInstance();
        $res = $db->ExecuteNonQuery($sql);
        if ($res->error) {
            $this->return["error_msg"] = $res->error;
        } else {
            $this->return["success"] = "true";
            $this->return["process_id"] = $res->lastId;
        }
        $sql = "commit;";
        $res = $db->ExecuteNonQuery($sql);
        return JSON::Encode($this->return);
    }

    public function setStatus($text, $progressID) {
		$text2 = str_replace("'", "\\'", $text);
        $sql = "update remote_job_status set STATUS = '$text2' where id = '$progressID'";
        $db = MySQLDatabase::GetInstance();
        $res = $db->ExecuteNonQuery($sql);

        $sql = "commit";
        $res = $db->ExecuteNonQuery($sql);

        if ($res->error)
            return false;
        else
            return true;
    }

    public function getProcesStatus($args) {
        set_time_limit(5);
        $sql = "select status from remote_job_status where id = '$args[process_id]'";
        $db = MySQLDatabase::GetInstance();
        $result = $db->ExecuteReader($sql);

        if ($result->recordCount > 0) {
            while ($row = $result->GetNextAssoc()) {
                $this->return["success"] = "true";
                $this->return["status"] = $row["status"];
            }
        } else {
            $this->return["error_msg"] = $res->error;
        }
        //return JSON::Encode($this->return);
		return json_encode($this->return);
    }

    public function processPriors($args) {
        $vars = array('progress_id' => $_REQUEST['progress_id'], 'studies' => explode(',', $args['studies']));
        return $this->process($vars);
    }

    public function process($args) {
//        file_put_contents('c:/temp/1.txt', print_r($args, 1));
        $pdfMaxCount = 5;
        $progress_id = $args['progress_id'];

        if (isset($args['dstart']))
            $filesArr = $this->ViewStudies($args);
        else
            $filesArr = $args['studies'];
        $filesCountOriginal = count($filesArr);

        if ($filesCountOriginal == 0) {
            $this->return["error_msg"] = 'Information about studies not found (in the database). Please check your start and stop dates. Tecnical info: ' . print_r($filesArr, 1);
            $this->setStatus('ERROR:' . $this->return["error_msg"], $progress_id);
            return json_encode($this->return);
        }

        $filesArr2 = $filesArr;
        $filesArr = $this->checkExists($_SERVER['DOCUMENT_ROOT'] . 'transcriptions/', '.pdf', $filesArr);
        $filesCount = count($filesArr);
        $this->setStatus('IN_PROGRESS:(0/' . $filesCount . ')', $progress_id);
        if ($filesCount == 0) {
            $this->return["error_msg"] = 'No reports available. Tecnical info: ' . $filesCountOriginal . ' studies found in the database, but this studies not contains the reports. Studies in the db: ' . print_r($filesArr2, 1);
            $this->setStatus('ERROR:' . $this->return["error_msg"], $progress_id);
            return json_encode($this->return);
        }

        $tempFile = '';
        $tempFile2 = '';
        $arrayLength = count($filesArr); {
            $currentCount = 0;
            $tempArray = array();
            $i = 0;
            foreach ($filesArr as $filename) {
                set_time_limit(600);
//					sleep(3);
                $decryptedFile = $this->getFileName($_SERVER['DOCUMENT_ROOT'] . 'temp/');

                include($_SERVER["DOCUMENT_ROOT"] . "/system/config.php");

				if (strlen($PDF_DEFAULT_PASSWORD) > 0)
				{
					if (!$this->fileEncrypt($filename, $decryptedFile, $PDF_DEFAULT_PASSWORD)) {
						$this->return["success"] = "false";
						$this->return["error_msg"] = 'fileEncryption: An error occured (1):' . stripslashes($this->error);
						$this->setStatus('ERROR:' . $this->return["error_msg"], $progress_id);
						return json_encode($this->return);
					}
				}
                $tempArray[] = $decryptedFile;
                if ($currentCount == $pdfMaxCount || ($arrayLength - 1 == $i)) {
                    $tempFile = $this->getFileName($_SERVER['DOCUMENT_ROOT'] . 'temp/');
                    if (count($filesArr) == 1) {
                        $tempFile = $this->getFileName($_SERVER['DOCUMENT_ROOT'] . 'temp/');
                        $this->setStatus('COMPLETED:' . $tempArray[0], $progress_id);
                        copy($tempArray[0], $tempFile);
                        $res = 1;
                        break;
                    } else {
                        $res = $this->gluePDFs($tempArray, $tempFile);
                    }
                    if (!$res) {
                        $this->return["error_msg"] = 'An error occured (2): ' . stripslashes($this->error);
                        $this->setStatus('ERROR:' . $this->return["error_msg"], $progress_id);
                        return json_encode($this->return);
                    }
                    $this->setStatus('IN_PROGRESS:(' . $i . '/' . $filesCount . ')', $progress_id);

                    if ($tempFile2) {
                        unlink($tempFile2);
                        $tempFile2 = '';
                    }
                    $tempFile2 = $tempFile;
                    foreach ($tempArray as $tempFile1) {
                        @unlink($tempFile1);
                    }
                    $tempArray = array();
                    $tempArray[] = $tempFile;

//						die(substr($tempFile, 0, strlen($tempFile) - 4));
                    $currentCount = 1;
                }
                $currentCount++;
                $i++;
            }
            $this->return["success"] = "true";
            $this->setStatus('COMPLETED:/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $tempFile), $progress_id);
            $this->return["filename"] = '/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $tempFile);
        }


        return JSON::Encode($this->return);
    }

    // unique filename
    function getFileName($path) {
        $i = 0;
        while (true) {
            $i++;
            $res = $path . 'temp_' . @date('Y-m-d_H-i-s') . rand(0, 10000) . '_' . $i . '.pdf';
            if (!file_exists($res))
                return $res;
        }
    }

}

?>