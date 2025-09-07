<?php
session_start();

include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
import("system.core.database.MySQLDatabase");
import("system.models.Study");

//$transcriptSharePath = "\\\\nywsmc-nas01\\Imaging\\";
$transcriptSharePath = "\\\\Jesse-desktop\\c\\Users\\Administrator\\Desktop\\MDI\\transcripts\\";

//get instance of database to execute queries.
$db = MySQLDatabase::GetInstance();
//traverse through all the files in the directory ($transcriptSharePath)
if ($handle = opendir($transcriptSharePath)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            echo "$file<br/>";
            copy($transcriptSharePath.$file, $TRANSCRIPTION_DIRECTORY.$file);
            $strippedfile = str_replace("MRN_", "", $file);
            $name = preg_replace("/([^\d]+).+/i", "$1", $strippedfile);
            preg_match("/(\d+)_(\d+)_(\d+)/i", $strippedfile, $matches);
            $accessionNumber = $matches[2];
            $accessionNumberShort = substr($accessionNumber, sizeof($accessionNumber)-3, 2);
            $name = split(" ", trim(str_replace("_", " ", $name)));
            $sqlpatient = implode("%' AND patientname LIKE '%", $name);
            $sql = <<<heredoc
            	SELECT study.uid
				FROM v_patient patient
				JOIN study ON study.patientid = patient.origid
				WHERE patientname LIKE '%${sqlpatient}%'
				AND (accessionnum LIKE '%${accessionNumber}%' OR accessionnum LIKE '%${accessionNumberShort}%')
heredoc;
            //print $sql;
            $db->ExecuteReader($sql).toObject("Study");
            if($study->uuid != ""){
            	$sql = "UPDATE study SET reviewed = 'root' WHERE uuid = '$study->uuid'";
				$db->ExecuteNonQuery($sql);
				//print $TRANSCRIPTION_DIRECTORY.$file;
				//unlink($transcriptSharePath.$file);
				$newfile = $TRANSCRIPTION_DIRECTORY.$study->uuid;
				if(file_exists($newfile.".pdf")){
					$i = 1;
					while (file_exists($newfile."_".$i.".pdf")){
						$i++;
					}
					$newfile .= "_". $i;
				}
				rename($TRANSCRIPTION_DIRECTORY.$file, $newfile.".pdf");
            }
            
        }
    }
    closedir($handle);
}

?>
