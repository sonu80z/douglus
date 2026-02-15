<?php

session_cache_limiter('private');
session_start();

//imports

require_once($_SERVER["DOCUMENT_ROOT"] . "/system/config.php");

require_once($_SERVER["DOCUMENT_ROOT"] ."/system/import.php");

include_once('dicom.php');

//include_once('dicom.php');
import('system.models.Image');
import('system.models.ImageInfo');
import('system.core.orm.DataController');

//include($_SERVER["DOCUMENT_ROOT"] ."/system/utilities/debug.php");

//getting study info.
//include_once('dicom.php');

//application/json
header("content-type: application/json");

$controller = new DataController($DB_DATABASE);

//required
require_once "RestService.php";
//all methods will be publically exposed via the url.
class API extends RestService {
	public function getStudyDetails($studyid ){		
		global $controller;
		$study = $controller->Find(new Study(array("uuid"=>$studyid )))->toObject("Study");
		$return = null;		
		if(sizeof($study) > 0){
			$patient = $controller->Find(new Patient(array("origid"=>$study[0]->patientid)))->toObject("Patient");
			if(sizeof($patient) > 0 ){
				$return = $patient[0];
				@$return->studies = $study;
				$seriesParam = new QueryParams();
				$seriesParam->conditions = "modality <> 'SR'"; 
				$series = $controller->Find(new Series(array("studyuid"=>$studyid)), $seriesParam)->toObject("Series");
				@$return->studies[0]->series = $series;
				$seriesLength = count($series); 
				for($i = 0; $i < $seriesLength; $i++){
					$images = $controller->Find(new Image(array("seriesuid"=>$series[$i]->uuid)))->toObject("ImageInfo");
					
					for($j = 0; $j < sizeof($images); $j++){
						$tags = new RawTags($images[$j]->path);
						@$images[$j]->info = $tags->returnObject();
					}
					@$return->studies[0]->series[$i]->images = $images;
				}
				
			}
		}
		//file_put_contents('c:/temp/1.txt', print_r($return, 1));
		return $return;
	}
	
	public function convertDicomToJPG($images){
		global $controller, $INSTALL_DIRECTORY;

				
		$imagelist = explode(",",$images);
		for($i = 0; $i < sizeof($imagelist); $i++){
			$uuid = $imagelist[$i];
			$image = $controller->Find(new Image(array("uuid"=>$uuid)))->toObject("Image");
			if(sizeof($image) > 0){

				$image = $image[0];
				$output = $INSTALL_DIRECTORY."/system/html5viewer/"."dicom/".$image->uuid.".jpg";
				$input = $image->path;
				if(!file_exists($input)){
					echo json_encode(array("success"=>false,'msg'=>'Dicom not found'));
					exit;
				}
				if(!file_exists($output)){
					//first we try imagick
					$img_obj=new Imagick;
					$img_obj->readImage ($input);
					//$src = imagick_readimage($input);
					$img_obj->setImageFormat ("jpeg");
					//$success=file_put_contents ($output, $img_obj);
					$success=$img_obj->writeImages($output,true);
					/*if(!imagick_iserror($src)){
						imagick_writeimage($src, $output);
					}else{
						//there was a problem with imagick.. let's try the other converter.
						try{
							//this is a fallback.
							$cmd = '"'.$INSTALL_DIRECTORY.'/bin/dcmj2pnm.exe" +oj +Wm "' . $input . '" "' . $output . '"';
							$shell = new COM("WScript.Shell");
							$exec = $shell->Run($cmd, 0, true);
							if(!$exec){
								//failed
							}
						}catch(Exception $e){
						}
					}*/
					//imagick_destroyhandle($src);
					$img_obj->destroy ();
				}else{
					$success=true;
				}
			}

		}
		return json_encode(array('success'=>(bool)$success,'msg'=>$output));//"{success:true}";
	}
	public function test($studyID){
		global $controller;
		$study = $controller->Find(new Study(array("uuid"=>$studyID)))->toObject("Study");
		$return = null;
		if(sizeof($study) > 0){
			$patient = $controller->Find(new Patient(array("origid"=>$study[0]->patientid)))->toObject("Patient");
			if(sizeof($patient) > 0 ){
				$return = $patient[0];
				$return->studies = $study;

				$series = $controller->Find(new Series(array("studyuid"=>$studyID)))->toObject("Series");
				$return->studies[0]->series = $series;
				$seriesLength = count($series);
				for($i = 0; $i < $seriesLength; $i++){
					$return->studies[0]->series[$i]->images = $controller->Find(new Image(array("seriesuid"=>$series[$i]->uuid)))->toObject("ImageInfo");
				}
			}
		}

		
	}
}

//service initialization
$api = new API();
$api->handleRequest();

