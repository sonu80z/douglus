<?php
session_cache_limiter('private');
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");


import('system.core.database.MySQLDatabase');
$StudyList = $_REQUEST["StudyUIDList"];
header("content-type: application/xml");
session_start();

$db = MySQLDatabase::GetInstance($DB_DATABASE);

//Get all study names
$StudyListArr = explode(",",$StudyList);
$RequestList = "";
foreach($StudyListArr as $StudyUID) $RequestList.='"'.$StudyUID.'", ';
$RequestList = substr($RequestList, 0, strlen($RequestList)-2);
//SQL request for study
$query = "SELECT study.uuid as uid,study.id FROM study WHERE study.uuid in ($RequestList);";

$result = $db->ExecuteReader($query);
while ($row = $result->GetNextArray() ) $rows[] = $row;
$StudyRows = $rows;

//SQL request for series
unset($rows);
$query = "SELECT series.studyuid,series.uuid as uid,series.seriesnumber as number FROM series WHERE series.studyuid in ($RequestList);";

$result = $db->ExecuteReader($query);
$RequestList="";
while ($row = $result->GetNextArray()) 
{
	$rows[] = $row;
	$RequestList.="\"$row[1]\", ";
}
$RequestList = substr($RequestList, 0, strlen($RequestList)-2);
$SeriesRows = $rows;

//SQL request for images
unset($rows);
$query = "SELECT image.seriesuid,image.uuid as uid,image.instance,image.path FROM image WHERE image.seriesuid in ($RequestList);";

$result = $db->ExecuteReader($query);
while ($row = $result->GetNextArray()) $rows[] = $row;
$ImageRows = $rows;

echo "<list>";
for($iStudy=0; $iStudy<count($StudyListArr); $iStudy++)//Add studies to list
{
	$curStudyID = $StudyRows[$iStudy][1];
	$curStudyUID = $StudyRows[$iStudy][0];
	echo "<Study id=\"$curStudyID\" uid=\"$curStudyUID\">";
	for($iSeries=0; $iSeries<count($SeriesRows); $iSeries++)//Add series to list
	{
		if($SeriesRows[$iSeries][0] == $curStudyUID)
		{
			$curSeriesNumber = $SeriesRows[$iSeries][2];
			$curSeriesUID = $SeriesRows[$iSeries][1];
			echo "<Series id=\"$curSeriesNumber\" uid=\"$curSeriesUID\">";
			
			$indexArray = array();

			$maxImageNumber = 0;
			
			for($iImage=0;$iImage<count($ImageRows);$iImage++)
			{
				if($ImageRows[$iImage][0] == $curSeriesUID)
				{
					$curImageInstance = $ImageRows[$iImage][2];
					$indexArray[intval($curImageInstance)] = $iImage;
					if($maxImageNumber<intval($curImageInstance)) $maxImageNumber = intval($curImageInstance);
				}
			}

			for($iIndex=0;$iIndex<$maxImageNumber+1;$iIndex++)
			{
				if(isset($indexArray[$iIndex])) 
				{
					$iImage = $indexArray[$iIndex];
					$curImageInstance = $ImageRows[$iImage][2];
					$curImageUID = $ImageRows[$iImage][1];
					$curImagePath = $ImageRows[$iImage][3];
					
					echo "<Image id=\"$curImageInstance\" uid=\"$curImageUID\" path=\"$curImagePath\">";
					echo "</Image>";
				}
			}
			
			echo "</Series>";
		}
	}
	echo "</Study>";
}
echo "</list>";
?>

