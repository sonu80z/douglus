<?php
/*
Structured Reports
*/
// sequrity check
if (!isset($_SESSION))
	session_start();
import("system.models.User");
import("system.controls.control");
if(!isset($_SESSION['AUTH_USER']) && $_REQUEST['method'] != 'processSRFiles')
    die('Please login first');

require_once($_SERVER['DOCUMENT_ROOT'].'etc/tcpdf/tcpdf.php');

class SRControl extends Control
{
    var $logo = '';
    var $inFileName = '';
    var $content = '';
    
    var $isFinalReport;
    var $CLINICAL_HISTORY;
    var $TECHNIQUE;
    var $COMPARISON;
    var $IMPRESSION;
    var $PATIENT_DOB;
    var $PATIENT_SEX;
    var $RADIOLOGIST;
    var $REF_PHYS;
    var $INSTITUTION;
    var $SIGN1;
    var $SIGN2;
    
    var $dateOfStudy;
    var $dateApproved;
    var $dateReceive;
    
    var $PATIENT_NAME;
    
    function getSubText($word1, $word2, $context)
    {
        $index1 = strpos(strtoupper($context), strtoupper($word1));
        $index1 += strlen($word1);
        $index2 = strpos(strtoupper($context), strtoupper($word2), $index1);
        return substr($context, $index1, $index2 - $index1);
    }
    
    function hex2string($hexstr)
    {
        $i = 0;
        $ret = "";
        $arr = explode(" ", $hexstr);
        $c = count($arr);
        
        for($i = 0; $i < $c; $i++){
            $ret .= chr(hexdec($arr[$i]));
        }

        return $ret;
    }

    function conver2Date($text)
    {
        return substr($text, 0, 4).'-'.substr($text, 4, 2).'-'.substr($text, 6, 2);
    }
    
    function isSRFile($filePath)
    {
        $content = file_get_contents($filePath);
        if (!$content)
            return false;
        if (-1 == strpos($content, "Impression@"))
            return false;
        if (-1 == strpos($content, "CLINICAL HISTORY: "))
            return false;

        return true;
    }
    
    public function parse($filePath)
    {
    	$this->inFileName = $filePath;
        $this->isFinalReport = true;
        
        $this->content = file_get_contents($this->inFileName);
        $listArray = explode($this->hex2string("08 00"), $this->content);
        
        $this->CLINICAL_HISTORY = $this->getSubText("CLINICAL HISTORY: ", $this->hex2string("0D 0A 0D 0A"), $this->content);
        $this->TECHNIQUE = $this->getSubText("TECHNIQUE: ", $this->hex2string("FE FF 00"), $this->content);
        $comparisonPos = strpos(strtoupper($this->TECHNIQUE), 'COMPARISON:');
        if ($comparisonPos >= 0){
            $this->TECHNIQUE = substr($this->TECHNIQUE, 0, $comparisonPos);
        }
        
        if (strpos(strtoupper($this->content), "COMPARISON: ") > 0)
            $this->COMPARISON = $this->getSubText("COMPARISON: ", $this->hex2string("FE FF 00"), $this->content);
        else
            $this->COMPARISON = '';
        $this->IMPRESSION = $this->getSubText("Impression@".$this->hex2string("00 60 A1 55 54 00 00"), chr(254).chr(255), $this->content);
        $this->IMPRESSION = substr($this->IMPRESSION, 4);
                      
        $matches = array();
        preg_match('/SQ.+PN.?\x00([\^\-a-zA-Z\s]+)\W+LO/i', $this->content, $matches);
        $name = $matches[1];
        
        $p = explode("^", $name);
        $this->PATIENT_NAME = @$p[1]." ".$p[0];      
        
        $this->PATIENT_DOB = $this->getSubText($this->hex2string("10 00 30 00 44 41 08 00"), $this->hex2string("10 00 32 00"), $this->content);
        $this->PATIENT_DOB = $this->conver2Date($this->PATIENT_DOB);
        $t = explode("-", $this->PATIENT_DOB);
        $this->PATIENT_DOB = $t[1]."-".$t[2]."-".$t[0];
        
        $this->PATIENT_SEX = $this->getSubText($this->hex2string("40 00 43 53 02 00"), $this->hex2string("20 10 00 10 10"), $this->content);
        
        $this->RADIOLOGIST = $this->getSubText("Person Observer Name".$this->hex2string("40 00 23 A1 50 4E"), $this->hex2string("FE FF 00"), $this->content);
        $this->RADIOLOGIST = substr($this->RADIOLOGIST, 2, strlen($this->RADIOLOGIST)- 2);
        
        $this->REF_PHYS = $this->getSubText($this->hex2string("08 00 90 00 50 4E"), $this->hex2string("08 00 10 10 53"), $this->content);
        $this->REF_PHYS = substr($this->REF_PHYS, 2);
        $p = explode("^", $this->REF_PHYS);
        $this->REF_PHYS = @$p[1]." ".$p[0];
        
        $this->INSTITUTION = $this->getSubText($this->hex2string("80 00 4C 4F"), $this->hex2string("08 00 90 00 50"), $this->content);
        $this->INSTITUTION = substr($this->INSTITUTION, 2);

        $this->SIGN1 = "Electronically signed by ".$this->RADIOLOGIST;
        $this->SIGN2 = "CONFIDENTIALITY STATEMENT: The information contained herein is legally privileged and confidential information intended only for the addressee.  If you are not the intended recipient, any dissemination, distribution or any action taken, or admitted to be taken in reliance on it is prohibited.  If you have received this message in error, please immediately notify the send.  Thank you for your cooperation.";
        
        $delimiter = $this->hex2string("08 00 30 00 54 4D 06 00");
        $this->dateOfStudy = $listArray[10];
        $studyTime = $this->getSubText($delimiter, $this->hex2string("08 00"), $this->content);
        $this->dateOfStudy = $this->formatDate($this->dateOfStudy).' '.$this->formatTime($studyTime);
        
        
        $t = $this->content;
        
        $matches = array();
        preg_match('/SEPARATE@.+DT.*(\d{14}).*\x08/i', $t, $matches);
//        print_r($matches);
        if (count($matches) >= 2){
            $this->dateApproved = $matches[1];
            $d = substr($this->dateApproved, 0, 8);
            $t = substr($this->dateApproved, 8, 6);
            $this->dateApproved = $this->formatDate($d).' '.$this->formatTime($t);
        }
        else{
            $this->isFinalReport = false;
            $this->dateApproved = '-';
        }
//        $this->dateApproved = $this->getSubText($this->hex2string("44 54 0E 00"), $this->hex2string("40 00 75 A0"), $listArray[31]);
//        die("aaa=".$studyTime);
//        $dateOfStudy = 
        
//        echo "<pre>";
//        $this->content = '';
//        print_r($this);
//        echo "</pre>";
//        die();
//        die('<pre>'.print_r($listArray, 1).'</pre>');
//        die($this->dateApproved);
        return true;
    }
    public function formatDate($dateText)
    {
        $year = substr($dateText, 0, 4);
        $mounth = substr($dateText, 4, 2);
        $day = substr($dateText, 6, 2);
        
        return $mounth."-".$day."-".$year;
    }
    public function formatTime($timeText)
    {
        $hours = substr($timeText, 0, 2);
        $minutes = substr($timeText, 2, 2);
        
        return $hours.":".$minutes;
    }
    public function processPDF($outputFile)
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false); 
        $pdf->SetMargins(34, 25, 25);

//        $pdf->SetAuthor('$PRODUCT_NAME');
        $pdf->SetTitle('Structured report');
        $pdf->SetTextColor(0,0,0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->AddPage('P');
        $pdf->SetDisplayMode('real','default');

        $Y = 30;
        $X = 120;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn', 'B');
        $pdf->SetFontSize(18);
        if ($this->isFinalReport)
            $pdf->Write(5,'Final Report');
        else
            $pdf->Write(5,'Preliminary Report');
        
        $pdf->Image($_SERVER['DOCUMENT_ROOT']."img/pdf_img.jpg", 30, 20, 70, 30, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        $Y = 50;
        $X = 34;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn', 'B');
        $pdf->SetFontSize(12);
        $pdf->Write(5,'Patient: '.$this->PATIENT_NAME);

        $X += 60;
        $pdf->SetXY($X,$Y);
        $pdf->Write(5,'Institution: '.$this->INSTITUTION);
        
        
        $Y += 6;
        $pdf->SetXY($X,$Y);
        $pdf->Write(5,'Study Time(local): '.$this->dateOfStudy);
        $Y -= 6;
        
        $X += -60;
        
        $Y += 6;
        $pdf->SetFont('arialn');
        $pdf->SetXY($X,$Y);
        $pdf->Write(5,'DoB: '.$this->PATIENT_DOB.'   Sex: '.$this->PATIENT_SEX);

        $Y += 6;
        $pdf->SetXY($X,$Y);
        $pdf->Write(5,'Approved: '.$this->dateApproved);

        $Y += 6;
        $pdf->SetXY($X,$Y);
        $pdf->Write(5,'Radiologist: '.$this->RADIOLOGIST);
        
        $Y += 6;
        $pdf->SetXY($X,$Y);
        $pdf->Write(5,'Ref Physician: '.$this->REF_PHYS);

        $Y += 20;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn', 'B');
        $pdf->SetFontSize(16);
        $pdf->Write(5,'OBSERVATION');
        
        $Y = $pdf->getY() + 10;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn');
        $pdf->SetFontSize(12);
        $pdf->Write(5,'CLINICAL HISTORY: '.$this->CLINICAL_HISTORY);

        $Y = $pdf->getY() + 6;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn');
        $pdf->SetFontSize(12);
        $pdf->Write(5,'TECHNIQUE: '.$this->TECHNIQUE);

        $Y = $pdf->getY() + 6;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn');
        $pdf->Write(5,'COMPARISON: '.$this->COMPARISON);

        $Y = $pdf->getY() + 12;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn', 'B');
        $pdf->SetFontSize(16);
        $pdf->Write(5,'IMPRESSION');
        
        $Y = $pdf->getY() + 10;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn');
        $pdf->SetFontSize(12);
        $pdf->Write(5,  $this->IMPRESSION);
//        $this->IMPRESSION

        
        
        $this->SIGN1 = "\n\n\n\n\n".$this->SIGN1;
//        $Y += 52;
//        $pdf->SetXY($X,$Y);
        $pdf->SetFont('arialn', 'I');
        $pdf->Write(5,  $this->SIGN1);

//        $Y += 12;
//        $pdf->SetXY($X,$Y);
        $pdf->Write(5,  "\n\n".$this->SIGN2);
        
//        $pdf->Output('doc.pdf', 'I');die();
        
        $pdf->Output($outputFile, 'F');
        
        return true;
    }
    function processSRFiles ()
    {
        include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");   
        
     
        $sql = "SELECT st.uuid, i.uuid image_uid, i.path FROM series se, study st, image i
WHERE sourceae = '247SCHUB3'
AND se.uuid = i.seriesuid
AND se.studyuid = st.uuid
AND COMPLETION in ('COMPLETE', 'PARTIAL')
 AND i.observationdatetime IS NULL
-- AND (i.observationdatetime IS NULL OR studydate >= '2012-07-15')
ORDER BY st.uuid limit 1000   ";
     
        $resArr = array();
        $db = MySQLDatabase::GetInstance();
        $statement = $db->ExecuteReader($sql);
        while($res = $statement->GetNextAssoc())
        {
            $row = array();
            $row['uuid'] = $res['uuid'];
            $row['path'] = $res['path'];
//            $row['path'] = 'c:/temp/11/1.2.826.0.1.3680043.2.93.4.2831183617.9511.1342835872.4';
            
            $row['image_uid'] = $res['image_uid'];
            $resArr[] = $row;
        }
        
        foreach ($resArr as $row){
            set_time_limit(15);
            $output_file = $TRANSCRIPTION_DIRECTORY.$row['uuid'].".pdf";
            
            if (file_exists($output_file)){
                copy($output_file, $output_file."-old.pdf");
                unlink($output_file);
            }
            
            if ($this->isSRFile($row['path'])){
                $res2 = false;
                $res1 = $this->parse($row['path']);
                  if ($res1){
                    $res2 = $this->processPDF($output_file);
//                    die('1 file success');
                    if ($res2 == true){
                        $sql = "UPDATE image SET observationdatetime = SYSDATE() WHERE UUID = '".$row['image_uid']."'";
                        $resdb = $db->ExecuteNonQuery($sql);
                        if ($resdb->error)
                            echo "<br> Can't update database UUID = ".$row['uuid'];
                        else{
                            echo "<br> Processed UUID = ".$row['uuid'];
                            setStudyRead($row['uuid'], true);
                        }
                        import('system.logger');
                        $_logEvent = array();
                        $_logEvent['event_type'] = 'Structured Report';
                        $_logEvent['event_table'] = 'study';
                        $_logEvent['event_table_id'] = $row['uuid'];
                        if ($resdb->error)
                            $_logEvent['additional_text'] = "Structured Report processed";
                        else
                            $_logEvent['additional_text'] = "Error when processing study id=$row[uuid]";
//                        die(print_r($_logEvent, 1));
                        logger::log($_logEvent);
                    }
                }
            }
        }
    }
}

function setStudyRead($studyID, $isRead)
{
    include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
    $db = MySQLDatabase::GetInstance();

    $user_id = 1;
    if ($_SESSION && $_SESSION["AUTH_USER"] && strlen($_SESSION["AUTH_USER"])){
        $user = unserialize($_SESSION["AUTH_USER"]);
        $username = $user->username;
        $user_id = $user->id;
    }
    else
    {
        $user_id = 1;
        $username = "root";
    }
    $reviewed = $username;
    
    $sql = '';
    if ($isRead)
        $sql = "update study set reviewed = '$reviewed' where uuid = '".$studyID."'";
    else
        $sql = "update study set reviewed = null, reviewed_user_id = null, reviewed_date = null,  where uuid = '".$studyID."' ";
//    die($sql);
    $resdb = $db->ExecuteNonQuery($sql);
    if ($resdb->error)
        return false;
    
    return true;
}

?>