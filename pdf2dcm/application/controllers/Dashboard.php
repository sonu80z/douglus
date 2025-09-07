<?php

/*****
*
* @Author: Nasid Kamal.
* @Project Keyword: PDF2DCM.
*
*****/

require_once('class_dicom.php');
 
class Dashboard extends CI_Controller {

    function __construct() {

        parent::__construct();
        
    }

    public function index() {

    	$data['ctrl'] = $this;

    	$data['pdfs'] = $this->dirToArray(PENDING_PDF_PATH);

        $data['_view'] = 'dashboard';
        $this->load->view('layouts/main', $data);

    }

    public function convert() {

    	$data['ctrl'] = $this;

    	$data['pdfs'] = $this->dirToArray(PENDING_PDF_PATH);

		$logText = '';

	    if(!empty($data['pdfs'])) {
	    	foreach($data['pdfs'] as $pdf) {

	    		$pdfFile = PENDING_PDF_PATH . $pdf['file'];
	    		$jpgFile = TMP_PATH  . $pdf['filename'] . '.jpg';

	    		$im = new Imagick();
				
				$im->setResolution(165, 127.5);
				$im->readImage($pdfFile);
				$im->writeImages($jpgFile, true);
				$imgNo = $im->getNumberImages();
				//var_dump($imgNo);
				//var_dump($pdf['patient']['birthdate']);
				//var_dump($pdf['study']['studydate']);
				//$dn = new dicom_net;
				//$d->file = DCMs_PATH . '/' .$pdf['filename']. '.dcm';

					
				if($imgNo > 1) {

					for($i=0;$i<$imgNo;$i++) {
						$jpg_file = TMP_PATH  .$pdf['filename'].'-'.$i. '.jpg';
						$dcm_file = DCM_PATH  .$pdf['filename'].'-'.$i. '.dcm';
						Execute('img2dcm '.$jpg_file.' '.$dcm_file);

						$d = new dicom_tag;
						$d->file = $dcm_file;
						$new_tags = array(
						  '0008,0020' =>  str_replace('-', '', $pdf['study']['studydate']),
						  '0008,0030' =>  $pdf['study']['studytime'],
						  '0008,0050' => isset($pdf['study']['accessionnum']) ? $pdf['study']['accessionnum'] : '',
						  '0008,0060' => (isset($pdf['study']['modalities'])) ? $pdf['study']['modalities']: '',
						  '0008,0080' => (isset($pdf['patient']['institution'])) ? $pdf['patient']['institution'] : '', 
						  '0008,0090' => (isset($pdf['study']['referringphysician'])) ? $pdf['study']['referringphysician'] : '',
						  '0010,0010' => $pdf['patient']['lastname'] . ' ' . $pdf['patient']['firstname'],
						  '0010,0020' => $pdf['patient']['origid'],
						  '0010,0030' => str_replace('-', '', $pdf['patient']['birthdate']),
						  '0010,0040' => $pdf['patient']['sex'],
						  '0020,0010' => $pdf['study']['uuid'],
						  '0020,000d' => $pdf['filename'].'-'.$i,
						  '0020,000e' => $pdf['filename'].'-'.$i
						);


						$result = $d->write_tags($new_tags);
						unlink($jpg_file);
					}

				} else {

					$jpg_file = TMP_PATH  .$pdf['filename'] . '.jpg';
					$dcm_file = DCM_PATH  .$pdf['filename'] . '.dcm';
					Execute('img2dcm '.$jpg_file.' '.$dcm_file);

					$d = new dicom_tag;
					$d->file = $dcm_file;
					$new_tags = array(
					  '0008,0020' =>  str_replace('-', '', $pdf['study']['studydate']),
					  '0008,0030' =>  $pdf['study']['studytime'],
					  '0008,0050' => isset($pdf['study']['accessionnum']) ? $pdf['study']['accessionnum'] : '',
					  '0008,0060' => (isset($pdf['study']['modalities'])) ? $pdf['study']['modalities']: '',
					  '0008,0080' => (isset($pdf['patient']['institution'])) ? $pdf['patient']['institution'] : '', 
					  '0008,0090' => (isset($pdf['study']['referringphysician'])) ? $pdf['study']['referringphysician'] : '',
					  '0010,0010' => $pdf['patient']['lastname'] . ' ' . $pdf['patient']['firstname'],
					  '0010,0020' => $pdf['patient']['origid'],
					  '0010,0030' => str_replace('-', '', $pdf['patient']['birthdate']),
					  '0010,0040' => $pdf['patient']['sex'],
					  '0020,0010' => $pdf['study']['uuid'],
					  '0020,000d' => $pdf['filename'],
					  '0020,000e' => $pdf['filename']
					);


					$result = $d->write_tags($new_tags);
					unlink($jpg_file);

				}

	    		$logText .= PHP_EOL . $pdf['file'] . ' is converted to ' . $imgNo . ' DCM file(s).';
				copy($pdfFile, PROCESSED_PDF_PATH . $pdf['file']);
				unlink($pdfFile);
				/*var_dump($d->jpg_file);
			//$d->template = DCMs_PATH  . 'jpg_to_dcm.xml';
			$d->temp_dir = DCMs_PATH  . 'dcm_temp';
			$d->jpg_quality = 50;

				$new_tags = array(
				  '0008,0012' =>  $pdf['study']['studydate'],
				  '0008,0013' =>  $pdf['study']['studytime'], 
				  '0008,0050' => $pdf['study']['sourceae'], 
				  '0008,0080' => (isset($pdf['patient']['institution'])) ? $pdf['patient']['institution'] : '', 
				  '0008,0090' => $pdf['study']['referringphysician'],
				  '0008,1030' => $pdf['study']['referringphysician'],
				  '0008,103e' => 'Series Description',
				  '0010,0010' => $pdf['patient']['lastname'] . ' ' . $pdf['patient']['firstname'],
				  '0010,0020' => $pdf['patient']['origid'],
				  '0010,0030' => $pdf['patient']['birthdate'],
				  '0010,0040' => $pdf['patient']['sex'],
				  '0010,21b0' => 'Patient History',
				  '0010,4000' => 'Patient Comments',
				  '0018,0015' => 'Head',
				  '0020,000d' => $pdf['study']['uuid'],
				  '0020,000e' => '1.3.51.5156.4083.' . date('Ymd') .'.42',
				  '0020,0011' => '1',
				  '0020,0012' => '1',
				  '0020,0013' => '1',
				);


				$result = $d->jpg_to_dcm($new_tags);
				//$d->compress(DCMs_PATH  .$pdf['filename']. '.dcm');
				$dt = new dicom_tag;
				$dt->file = DCMs_PATH  .$pdf['filename']. '.dcm';
	$dt->load_tags();
	$ts = $dt->get_tag('0002', '0010');
	//var_dump($ts);
	$fsize = filesize($dt->file);
	var_dump($fsize);*/


	    	}

	    	$logText .= PHP_EOL . PHP_EOL . 'Conversion Successful.' . PHP_EOL;

	    	$data['confirmationMessage'] = 'PDF To DCM Conversion Successful.';

	        $data['_view'] = 'confirmation';
	        $this->load->view('layouts/main', $data);

			//echo 'Conversion Successful!';
	    } else {
	    	
	    	$logText .= PHP_EOL . PHP_EOL . 'No PDF files to convert.' . PHP_EOL;

	    	$data['confirmationMessage'] = 'No New PDF To Convert.';

	        $data['_view'] = 'confirmation';
	        $this->load->view('layouts/main', $data);
	    	//echo 'No PDF files to convert!';
	    }

	    $this->updateLog($logText);

        //$data['_view'] = 'dashboard';
        //$this->load->view('layouts/main', $data);

    }

	public function getDisplayString($array) {

		$string = '';

    	foreach ($array as $key => $value) {

    		if($value) {

    			$string = $string . $key . ': ' . $value . '<br>';

    		}

    	}

    	return $string;

    }


    private function dirToArray($dir) { 
   
	    $result = array();

	    $cdir = scandir($dir);

	    foreach ($cdir as $key => $value) { 

	      	if(!in_array($value,array('.','..'))) { 

	         	if (!is_dir($dir . DIRECTORY_SEPARATOR . $value)) {

	         		$pdfInfo['file'] = $value;

	         		$studyUuid = explode('.pdf', $value)[0];

	         		$pdfInfo['filename'] = $studyUuid;

	         		$pdfInfo['study'] = $this->Study_model->get_study($studyUuid);

	         		$patientOrigId = $pdfInfo['study']['patientid'];

	         		$pdfInfo['patient'] = $this->Patient_model->get_patient($patientOrigId);

					$result[] = $pdfInfo; 

	        	}

	      	}

	   	}
	   
	   	return $result;

	}

	public function updateLog($action_performed) {

		$documentText = '';

		$current_time = date("m-d-Y H:i:s");

		$logFile = LOG_PATH . 'PDF2DCM_Log.txt';

		$ct = PHP_EOL . $this->formatwhitespaces('STARTING CONVERSION: ' . $current_time, 50) . PHP_EOL;

		$documentText .= <<<EOT
{$ct}
----------------------------------------
{$action_performed}
__________________________________________________________________________________

EOT;
		$documentText .= "\n";

		$f = fopen($logFile, 'a');
    	fwrite($f, $documentText);
    	fclose($f);

	}


	public function formatwhitespaces($text, $length) {
		
		return str_pad ($text, $length," ");

	}


}
