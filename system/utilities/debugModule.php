<?php

class debug
{
    var $rootPath; 



    /* Makes pdf files for each study in the db and print it's ID in the pdf. Pdf stored in the /translation folder */
    function debugCreateAllPDFs()
    {
        include_once($this->rootPath."system/import.php");
        import("system.controls.*");
        import("system.models.Group");
        import("system.models.GroupUser");
        import("system.controls.control");
        import("system.core.database.MySQLDatabase");
        require_once($this->rootPath.'system/controls/control.php');
        require_once($this->rootPath.'etc/tcpdf/tcpdf.php');
        
        
        $sql = "SELECT uuid, patientid, studydate, (
SELECT patientname
FROM v_patient
WHERE origid = patientid
) patientname
FROM  `v_study` p";
//die($sql);
        $db = MySQLDatabase::GetInstance();
        $patientIDsArr = array();
        $statement = $db->ExecuteReader($sql);
        if (!$statement)
        {
            throw new Exception("could not exec sql:".  mysql_error());
        }
        $return = '';
        while($res = $statement->GetNextAssoc())
        {
            $text = print_r($res, 1);
            $path = $this->rootPath.'transcriptions/'.$res['uuid'].'.pdf';
            if (!file_exists($path))
            {
                $this->createPDF($path, $text);
                $return .= '<li>'.$path;
            }    
        }
        return $return;
    }
    
    public function createPDF($path, $text)
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false); 
        $pdf->SetMargins(20, 25, 25);

        $pdf->SetTextColor(0,0,0);
        $pdf->AddPage('P');
        $pdf->SetDisplayMode('real','default');

        $pdf->SetXY(50,50);
        $pdf->Write(5, $text);

        $_output = $path;
        $pdf->Output($_output, 'F');
        
    }
    
    
    public function testHIPAAFile()
    {
        include_once($this->rootPath."system/import.php");
        import("system.controls.*");
//        import("system.models.Group");
//        import("system.models.GroupUser");
//        import("system.controls.control");
//        import("system.core.database.MySQLDatabase");
//        require_once($this->rootPath.'system/controls/control.php');
//        require_once($this->rootPath.'etc/tcpdf/tcpdf.php');

        $a = array();
        $a['mailTo'] = 'asd@sdf.asdf';
        $a['mailSubject'] = 'Study report';
        $a['mailText'] = 'Report attached';
        $a['includeHippa'] = true;
        $a['studyID'] = 'transcriptions/1.2.410.200013.1.310.1.201101021134300005.pdf';
        $a['to'] = 'asd@sdf.asdf';
        $a['fax'] = 'asd@sdf.asdf';
        $a['phone'] = 'sssss';
        $a['re'] = 'BERNICE COWLING';
        $a['from'] = 'Tech Care X-ray';
        $a['pages'] = '2 including this cover sheet';
        $a['date'] = '10-14-2011  23:47';
        $a['cc'] = 'NONE';
        $a['output'] = 'C:/rentacoder/dpotter/mdi//temp/tempHIPPA25775.pdf';

        $pdf = new PDFControl();
        $pdf->createHIPAAPDF($a);
    }
    
    
}

$c = new debug();
$c->rootPath = $_SERVER["DOCUMENT_ROOT"];
//echo $c->debugCreateAllPDFs();
$c->testHIPAAFile();

?>
