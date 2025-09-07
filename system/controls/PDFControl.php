<?php
import("system.models.Group");
import("system.models.GroupUser");
import("system.controls.control");
import("system.core.database.MySQLDatabase");
require_once('Control.php');
require_once($_SERVER['DOCUMENT_ROOT'].'etc/tcpdf/tcpdf.php');

class PDFControl extends Control
{
	public function createHIPAAPDF($args)
	{
//      die('<pre>'.print_r($args, 1).'</pre>');
        $_To 		= $args['to'];
		$_Fax 		= $args['fax'];
		$_Phone		= $args['phone'];
		$_Re 		= $args['re'];
		
		$_From 		= $args['from'];
		$_Pages		= $args['pages'];
		$_Date		= $args['date'];

		$_output 	= $args['output'];
		
//		$pdf= new FPDF();
		$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false); 
		$pdf->SetMargins(25, 25, 25);
//		$pdf->AddPage()
//		
        include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
        $pdf->SetAuthor($PRODUCT_NAME);
		$pdf->SetTitle('Confidential Facsimile');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(0,0,0);
		$pdf->AddPage('P');
		$pdf->SetDisplayMode('real','default');
		
		$Y = 20;
        $X = 25;
		$pdf->SetXY($X,$Y);
		$pdf->SetFont('arialn', 'I');
		$pdf->SetFontSize(18);
		$pdf->Write(5,'Confidential Facsimile');

		// begin table
		$Y+=17;
		$pdf->SetFont('arialn', '');
		$pdf->SetFontSize(12);
		$h_pdf = &$pdf;
		$this->drawTextLine($h_pdf, $X, $Y, 'To:', 'From:', $_To, $_From);
		$Y+=10;
		$this->drawTextLine($h_pdf, $X, $Y, 'Fax:', 'Phone:', $_Fax, $_Phone);
		$Y+=10;
		$this->drawTextLine($h_pdf, $X, $Y, 'Date:', '', $_Date, '');
		$Y+=10;
		$this->drawTextLine($h_pdf, $X, $Y, 'RE:', 'Pages ____  including cover', $_Re, '');
		
		$Y+=8;
		$pdf->SetXY($X+11, $Y);

		// drow horizontal line
		$Y+=8;
        $pdf->SetLineWidth(0.4);
		$pdf->Line($X, $Y, $X+165, $Y);

		$Y+=13;
		$pdf->SetXY($X,$Y);
		$pdf->Write(5,'Confidentiality Notice:');

		$Y+=11  ;
		$pdf->SetXY($X, $Y);
		$pdf->Write(5,'This facsimile transmission contains confidential information, some or all of which may be protected health information as defined by the federal Health Insurance Portability and Accountability Act (HIPAA) Privacy rule.');
		
		$Y+=20;
		$pdf->SetXY($X, $Y);
		$pdf->Write(5,'This transmission is intended for the exclusive use of the individual or entity to which it is addressed and may contain information that is proprietary, privileged, confidential and/or exempt from disclosure under applicable law.  If you are not the intended recipient (or an employee or agent responsible for delivering this facsimile transmission to the intended recipient), you are hereby notified that any disclosure, dissemination, distribution or copying of this information is strictly prohibited and may be subject to penalties under state and federal law.');
		
		$Y+=35;
		$pdf->SetXY($X, $Y);
		$pdf->SetFont('arialn', 'B');
		$pdf->Write(5,'If you receive this fax in error, please contact the sender immediately and then destroy the faxed materials.');
		
//		$Y+=15;
//		$pdf->SetXY(10, $Y);
//		$pdf->Write(5,'CONFIDENTIALITY NOTICE:');
//
//		$Y+=5;
//		$pdf->SetXY(10, $Y);
//		$pdf->Write(5,'The information contained in this facsimile message is privileged and confidential information intended for the use of the individual or entity named above. Health Care Information is personal and sensitive and should only be read by authorized individuals. Failure to maintain confidentiality is subject to penalties under state and federal law.');
		
		$pdf->Output($_output, 'F');
		return true;
	}
	
	function drawSQ($pdf, $x, $y, $width)
	{
		$pdf->Line($x, $y, $x+$width, $y);
		$pdf->Line($x, $y, $x, $y+$width);
		$pdf->Line($x, $y+$width, $x+$width, $y+$width);
		$pdf->Line($x+$width, $y, $x+$width, $y+$width);
	}
	
	
	function drawTextLine($pdf, $x, $y, $t1, $t2, $v1, $v2)
	{
		$pdf->SetXY($x, $y);
		$pdf->Write(5, $t1);
		
		$x+=15;
		$pdf->SetXY($x, $y);
		$pdf->Write(5, $v1);

		$x+=70;
		$pdf->SetXY($x, $y);
		$pdf->Write(5, $t2);
		
		$x+=15;
		$pdf->SetXY($x, $y);
		$pdf->Write(5, $v2);
	}
        
        //calculate years of age (input string: MM/DD/YYYY)
        function getAge ($birthday, $today)
        {
//            die("$birthday, $today");
            list($month, $day, $year) = explode("/", $birthday);
            list($today_month, $today_day, $today_year) = explode("/", $today);
            $year_diff  = $today_year - $year;
            $month_diff = $year_diff - $month;
            $day_diff   = $today_day - $day;
            if ($day_diff < 0 || $month_diff < 0)
              $year_diff--;
            return $year_diff;
        }        
	
        function getPatientID($patientFirstName, $patientLastName, $DOB, $patientID)
        {
            $sql = "SELECT `origid`
FROM `patient` p
WHERE (upper(`p`.`origid`) = upper('$patientID'))
        OR
       (
        upper(`p`.`firstname`) = upper('$patientFirstName')
        and 
        upper(`p`.`lastname`) = upper('$patientLastName')
       )
        OR
       (upper(`p`.`origid`) LIKE upper('%".$patientID."%'))
        ";
"and `p`.`birthdate` = STR_TO_DATE('$DOB', '%m/%d/%Y')";
//die($sql);
            $db = MySQLDatabase::GetInstance();
            $patientIDsArr = array();
            $statement = $db->ExecuteReader($sql);
            while($res = $statement->GetNextAssoc())
            {
                $patientIDsArr[] = $res['origid'];
            }
            if (!$patientIDsArr)
            {
                throw new Exception("Could not fetch the patientID by next data: First Name: $patientFirstName, Last Name: $patientLastName, DOB: $DOB, patientID: $patientID");
            }
            return $patientIDsArr;
        }
        
        function getStudyID($patientID, $studyDate, $studyTime)
        {
            $sql = "SELECT `uuid`
FROM `v_study` s
WHERE `s`.`patientid` = '$patientID' 
and `s`.`studydate` = STR_TO_DATE('$studyDate', '%m/%d/%Y') 
and `s`.`studytime` >= STR_TO_DATE('$studyTime', '%H:%i:%s') 
ORDER BY studydate, studytime
";
//and `s`.`studytime` <  DATE_ADD(STR_TO_DATE('$studyTime', '%H:%i:%s'), INTERVAL 1 MINUTE)
//die($sql);
            $db = MySQLDatabase::GetInstance();
            $res = $db->ExecuteReader($sql)->GetNextAssoc();
            if (!$res)
            {
                return false;
            }
            $studyID = $res['uuid'];
            return $studyID;
        }

        public function processTextFile($args)
        {
//            $filename = 'C:/rentacoder/dpotter/testtxtfile2.txt';
//            die($args['filename']);
            $fileContent = file_get_contents($args['filename']);

            $patientData = $this->parsePatientData($fileContent);
            $patientIDsArr = $this->getPatientID($patientData['FIRSTNAME'], $patientData['LASTNAME'], $patientData['DOB'], $patientData['PATIENT_ID']);
            $c_ = count($patientIDsArr);
			$C_RESULT = false;
			//die(print_r($patientIDsArr, 1));
            for ($k = 0; $k < $c_; $k++)
            {
                $patientID = $patientIDsArr[$k];
                $studyID = $this->getStudyID($patientID, $patientData['EXAM_DATE'], $patientData['EXAM_TIME']);
                if (strlen($studyID) > 0)
                {
                    //die("$patientID, $patientData[EXAM_DATE], $patientData[EXAM_TIME]");
                    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false); 
                    $pdf->SetMargins(20, 25, 25);

                    $pdf->SetTextColor(0,0,0);
                    $pdf->AddPage('P');
                    $pdf->SetDisplayMode('real','default');

                    $Y = 10;
                    $rowPadding = 6;

                    $pdf->SetFont('arialbd');
                    $pdf->SetFontSize(9);

                    $X = 129;
                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,'Mobile Medical Diagnostic Services');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X + 26,$Y);
                    $pdf->Write(5,'48 Silver Lake Ave');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X + 26,$Y);
                    $pdf->Write(5,'Newton, MA 02458');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X + 14,$Y);
                    $pdf->Write(5,'Phone:             6172449729');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X + 18,$Y);
                    $pdf->Write(5,'Fax:             6172449730');

                    $Y+=15;
                    $pdf->Line(20, $Y, 200, $Y);

                    $X = 20;
                    $pdf->SetFont('arialbd');
                    $pdf->SetFontSize(10);

                    $Y+=$rowPadding * 1;
                    $pdf->SetXY(85,$Y);
                    $pdf->Write(5,'RADIOLOGY REPORT');

                    $pdf->SetFont('Times');
                    $pdf->SetFontSize(10);

                    $Y+=$rowPadding * 2;
                    $pdf->SetXY(20,$Y);
                    $pdf->Write(5,'PATIENT NAME:');
                    $pdf->SetXY(140,$Y);
                    $pdf->Write(5,'PATIENT ID:');

                    $X = 20;
                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,'DOB:');
                    $pdf->SetXY(100,$Y);
                    $pdf->Write(5,'AGE:');
                    $pdf->SetXY(152,$Y);
                    $pdf->Write(5,'SEX:');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,'FACILITY:');
                    $pdf->SetXY(141,$Y);
                    $pdf->Write(5,'LOCATION:');

                    $Y+=$rowPadding;
                    $pdf->SetXY(20,$Y);
                    $pdf->Write(5,'REFERRING PHYSICIAN:');
                    $pdf->SetXY(152,$Y);
                    $pdf->Write(5,'DOS:');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,'PROCEDURE:');

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,'HISTORY:');

                    $Y=$Y-($rowPadding * 5);
                    $X = 62;

                    $pdf->SetFont('arialbd', 'B');

                    $pdf->SetXY($X, $Y);
                    $pdf->Write(5,  strtoupper($patientData['LASTNAME']).' , '.strtoupper($patientData['FIRSTNAME']));

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['DOB']));

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['FACILITY']));

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['REFERRING_PHYSICIAN']));

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['PROCEDURE']));

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['HISTORY']));

                    $Y=$Y-($rowPadding * 4);
                    $X = 110;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,$patientData['AGE']);

                    $Y=$Y-($rowPadding * 1);
                    $X = 162;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,$patientData['PATIENT_ID']);

                    $Y+=$rowPadding;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['SEX']));

                    $Y+=$rowPadding * 2;
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5,strtoupper($patientData['DOS']));

                    $X = 20;
                    $Y+=$rowPadding * 3;
                    $pdf->SetXY($X,$Y);

                    $rowsArr = explode("\n", $patientData['TEXT']);
                    $count = count ($rowsArr);
                    for ($j = 0; $j <= $count; $j++)
                    {
                        if (($j + 1) < $count)
                        {
                            if ($rowsArr[$j] == $rowsArr[$j + 1])
                            {
                                $i++;
                            }
                        }
                        $row = @$rowsArr[$j];

                        $X = 20;

                        if (strpos($row, ":") > 0 && strpos($row, ":") < 20)
                        {
                            $Y+=$rowPadding*2;
                            $pdf->SetXY($X,$Y);
                            $header = substr($row, 0, strpos($row, ":") + 1);
                            $pdf->SetFont('arialbd', 'B');
                            $pdf->Write(5, $header);
                            $row = substr($row, strpos($row, ":") + 1);
                            //$row = str_replace("\n", "", $row);

                        }
                        if (strlen($row) > 2)
                        {
                            $Y+=$rowPadding;
                            $pdf->SetXY($X,$Y);
    //                        echo $row;
                            $pdf->SetFont('arial');
    //                        echo '<br>-----------'.$row;
                            $rowsC = $pdf->getNumLines($row);
                            $pdf->MultiCell(0, 0, $row, 0, 'L');

                            $Y+=$rowPadding * ($rowsC - 1);
                            $pdf->SetXY($X,$Y);
                        }
                    }


                    //footer text
                    $X = 20;
                    $Y=250;

                    $pdf->SetFont('arialbd');
                    $pdf->SetXY($X,$Y);
                    $pdf->Write(5, 'DICTATED & E-SIGNED BY:');

                    $pdf->SetFont('arial');
                    $pdf->SetXY($X + 60,$Y);
                    $pdf->Write(5, $patientData['REFERRING_PHYSICIAN']);

                    $pdf->SetFont('arialbd');
                    $pdf->SetXY(145,$Y);
                    $pdf->Write(5, 'ON:');

                    $pdf->SetFont('arial');
                    $pdf->SetXY(152,$Y);
                    $pdf->Write(5, $patientData['DOS']);

                    $pdf->SetFont('arial');
                    $pdf->SetXY(169,$Y);
                    $pdf->Write(5, $patientData['TOS']);

                    $Y+=8;
                    $pdf->Line(20, $Y, 200, $Y);

                    $pdf->SetFont('arial', 'I');
                    $pdf->SetFontSize(9);
                    $Y+=$rowPadding - 3;
                    $pdf->SetXY(30,$Y);
                    $pdf->Write(5, 'PERSONAL & CONFIDENTIAL: if you have received this message in error, please call (617) 244-9729');
                    $Y+=$rowPadding;
                    $pdf->SetXY(45,$Y);
                    $pdf->Write(5, 'or fax (617) 244-9730, and please destroy this document and/or delete this e-mail.');

                    $pdf->SetFont('arial');



                    // HEADER IMAGE
                    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                    $pdf->setJPEGQuality(75);
                    $pdf->Image($_SERVER['DOCUMENT_ROOT'].'img/'.'blue_logo.jpg' , 20, 18, 35, 20, '', '', '', true, 150);
                    $pdf->SetFont('timesbd');
                    $pdf->SetFontSize(10);
                    $pdf->SetTextColor(2,2,132);
                    $pdf->SetXY(20,40);
                    $pdf->Write(5, 'MMDS OF BOSTON');
                    $pdf->SetFontSize(6);
                    $pdf->SetXY(20,43);
                    $pdf->Write(5, 'Mobile Medical Diagnostic Services');


                    $pdf->Output("../../transcriptions/$studyID.pdf", 'F');
                    $_REQUEST['actions'] = 'Mark Study Reviewed';
                    $_REQUEST['entry'] = $studyID;
                    $_REQUEST['option'] = 'Study';
                    global $internal_service;
                    $internal_service = true;
                    require_once ('../actionItem.php');
					if ((strtolower($return["success"]) == "true"))
						$C_RESULT = true;
                    return $C_RESULT;
                }
            }
			if (!$C_RESULT)
				throw new Exception("Could not find a study for patient's data: FIRSTNAME: $patientData[FIRSTNAME], LASTNAME: $patientData[LASTNAME], DOB:$patientData[DOB], PATIENT_IDs: ".print_r($patientIDsArr, 1));
        }
        
        private function getKeywordsText($arrayOfRow, $keyword)
        {
            $c = count($arrayOfRow);
            $resText = '';
            for ($i = 1; $i < $c; $i++)
            {

                if (strpos(strtolower($arrayOfRow[$i]), strtolower($keyword)) === 0)
                {
                    $res = //substr($arrayOfRow[$i], 0, strlen($keyword)).
                                    //"\n".
                                    substr($arrayOfRow[$i], strlen($keyword) + 1);
                    return $res;
                }
            }
            return '';
        }
        
        public function prepareTextFile($rowsArray) 
        {
            $rowsArray[0] = '';
            $res = implode("\n", $rowsArray);
            $p = strpos($res, '|');
            if ($p)
                $res = substr($res, 0, $p);
            return $res;
        }
        
        public function parsePatientData($patientTXTFile)
        {
            
            $fileRowArray = explode("\n", $patientTXTFile);
            $firstRow = $fileRowArray[0];
            $arr = explode("|", $firstRow);
            
//            die('_________'.print_r($arr, 1));
            $resArr = array();
            $resArr['LASTNAME'] = $arr[0];
            $resArr['FIRSTNAME'] = $arr[1];
            $resArr['DOB'] = $arr[3];
            $resArr['FACILITY'] = $arr[7];
            $resArr['REFERRING_PHYSICIAN'] = $arr[18];
            $resArr['HISTORY'] = $arr[29];
            
            $resArr['DOS'] = $arr[16];
            $resArr['TOS'] = $arr[17];
            $resArr['AGE'] = $this->getAge($resArr['DOB'], $resArr['DOS']);

            $resArr['PATIENT_ID'] = $arr[5];
            $resArr['SEX'] = $arr[4];
            
            $resArr['TEXT'] = $this->prepareTextFile($fileRowArray);
            $resArr['PROCEDURE'] = $this->getKeywordsText($fileRowArray, 'PROCEDURE');
            $resArr['RESULTS'] = $this->getKeywordsText($fileRowArray, 'RESULTS');;
            $resArr['IMPRESSION'] = $this->getKeywordsText($fileRowArray, 'IMPRESSION');;
//            die('<pre>'.print_r($resArr, 1).'</pre>');
            $resArr['EXAM_DATE'] = str_replace(' ', '', $arr[12]);
            $resArr['EXAM_TIME'] = $arr[13];
//            die('<pre>'.print_r($resArr, 1).'</pre>');
            
            
            $arr = explode("\n", $patientTXTFile);
            $resArr['PROCEDURE'] = str_replace('Procedure: ', '', $arr[2]);
            
//            $firstRow = (substr($patientTXTFile, 0, strpos($patientTXTFile, "\n")));
            
//            $resArr[''] = $arr[];
//            die('<pre>'.print_r($resArr, 1).'</pre>');
            return $resArr;
        }
        
        public function createPDFFromTextFile($args)
	{
        
        
        


    //		$pdf= new FPDF();
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false); 
            $pdf->SetMargins(20, 25, 25);
    //		$pdf->AddPage()
    //		
    //		$pdf->SetAuthor('MDI everywhere');
    //		$pdf->SetTitle('MDI everywhere Report');
            $pdf->SetTextColor(50,60,100);
            $pdf->AddPage('P');
            $pdf->SetDisplayMode('real','default');

            $Y = 20;
            $pdf->SetXY(50,$Y);
            $pdf->SetDrawColor(50,60,100);
    //		$pdf->Cell(100,10,'FPDF Tutorial',1,0,'C',0);

            $pdf->SetXY(10,$Y);
            $pdf->SetFont('arial_black', 'B');
            $pdf->SetFontSize(36);
            $pdf->Write(5,'Hipaa Cover Sheet');


            // begin table
            $Y+=20;
            $X = 15;
            $pdf->SetFontSize(9);
			$h_pdf = &$pdf;
            $this->drawTextLine($h_pdf, $X+15, $Y, 'To:', 'From:', $_To, $_From);
            $Y+=6;
            $this->drawTextLine($h_pdf, $X+15, $Y, 'Fax:', 'Pages:', $_Fax, $_Pages);
            $Y+=6;
            $this->drawTextLine($h_pdf, $X+15, $Y, 'Phone:', 'Date:', $_Phone, $_Date);
            $Y+=6;
            $this->drawTextLine($h_pdf, $X+15, $Y, 'Re:', 'cc:', $_Re, $_cc);

            $Y+=8;
            $pdf->SetXY($X+11, $Y);
            $pdf->Write(5,'        Urgent        For Review        Please Comment        Please Reply        Please Recycle');

            // drow checkbixes
            $x1 = $X-16;
            $pdf->SetXY($x1+32,$Y);
            $pdf->Write(5,'x');
			$h_pdf = &$pdf;
            $this->drawSQ($h_pdf, $x1+53, $Y+1.3, 2);
            $this->drawSQ($h_pdf, $x1+81, $Y+1.3, 2);
            $this->drawSQ($h_pdf, $x1+119, $Y+1.3, 2);
            $this->drawSQ($h_pdf, $x1+150, $Y+1.3, 2);


            // drow horizontal line
            $Y+=8;
            $pdf->Line($X+15, $Y, $X+160, $Y);

            $Y+=13;
            $pdf->SetXY(10,$Y);
            $pdf->Write(5,'IMPORTANT: This facsimile transmission contains confidential information, some or all of which may be protected health information as defined by the federal Health Insurance Portability & Accountability Act (HIPAA) Privacy Rule. This transmission is intended for the exclusive use of the individual or entity to whom it is addressed and may contain information that is proprietary, privileged, confidential and/or exempt from disclosure under applicable law. If you are not the intended recipient (or an employee or agent responsible for delivering this facsimile transmission to the intended recipient), you are hereby notified that any disclosure, dissemination, distribution or copying of this information is strictly prohibited and may be subject to legal restriction or sanction. Please notify the sender by telephone (number listed above) to arrange the return or destruction of the information and all copies.');

            $Y+=55;
            $pdf->SetXY(10, $Y);
            $pdf->SetFont('Times','', 12);
    //		$pdf->Link(41,$Y+5, 12, 4, 'http://mdi/');
            $pdf->Write(5,'The Department of Public Welfare for the Commonwealth of New York, in a HIPAA Privacy Rule Implementation ');

            $pdf->Write(5,'memo', 'https://mdi/');
            $pdf->Write(5,', requires that employees use the following text on fax cover sheets when sending this type of information:');

            $Y+=20;
            $pdf->SetFont('arial_black', 'B', 9);
            $pdf->SetXY(10, $Y);
            $pdf->Write(5,'IF YOU RECEIVE THIS FAX IN ERROR, PLEASE CONTACT THE SENDER IMMEDIATELY AND THEN DESTROY THE FAXED MATERIALS.');

            $Y+=15;
            $pdf->SetXY(10, $Y);
            $pdf->Write(5,'CONFIDENTIALITY NOTICE:');

            $Y+=5;
            $pdf->SetXY(10, $Y);
            $pdf->Write(5,'The information contained in this facsimile message is privileged and confidential information intended for the use of the individual or entity named above. Health Care Information is personal and sensitive and should only be read by authorized individuals. Failure to maintain confidentiality is subject to penalties under state and federal law.');

            $pdf->Output($_output, 'F');
            return true;
    }
}

?>