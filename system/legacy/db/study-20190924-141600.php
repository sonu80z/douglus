<?php

$i = 0;
global $additional_where;
$additional_where = "\n ";
//		die('<pre>'.print_r($_REQUEST, 1).'</pre>');
for ($i = 1; $i <= 8; $i++)
{
	$key = 'searchColumn'.$i;
	if (isset($_REQUEST[$key]))
	{
		$field		= $_REQUEST[$key];
	
		$key = 'search'.$i;
		$fieldValue	= str_replace('T00:00:00', '', $_REQUEST[$key]);
	
		if (strlen($fieldValue) > 0)
		{
			$_condition = "\n and upper($field) like upper('%$fieldValue%')";
			if ($i == 3)
			{
				$_condition = "\n and $field='$fieldValue'";
			}
			if ($i == 4)
			{
				$_condition = "\n and ifnull((select `series`.`modality` AS `modality` from `series` where (`study`.`uuid` = `series`.`studyuid`) order by `series`.`seriesnumber` limit 1), study.modality)='$fieldValue'";
			}
			if ($i == 5)
			{
				$_condition = "\n and $field='$fieldValue'";
			}
			if (($i == 7) || ($i == 8))
			{
				$_condition = "\n and $field='$fieldValue'";
			}
			$additional_where .= $_condition;
		}
	}
}

//
// db/Study.php
//
// Database file for retreiving all study information from the database.
//
// 
//
//error_reporting(E_ALL); 
ini_set("display_errors", 1);
session_start();
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/utilities/debug.php");
import("system.utilities.Format");
import("system.core.database.MySQLDatabase");
import("system.models.User");

global $SEARCH_LAST_MONTHS;
if(!isset($_SESSION['AUTH_USER']))exit();
$user = unserialize($_SESSION['AUTH_USER']);

$username = $user->username;
$viewAccess = $user->admin || $user->staffrole;

$rows = array();
//this is used to capture all variables passed through the url
//so any defaults will be overwritten
$var = array();
//lets take what's passed in the url first
/* DEFAULT ATTRIBUTES START*/
$var['sort'] = 'datetime';
$var['dir']  = 'DESC';
$var['start'] = 0;
if (isset($_REQUEST['start']))
$var['start'] = $_REQUEST['start'];
$var['limit'] = 15;
if (isset($_REQUEST['limit']))
    $var['limit'] = $_REQUEST['limit'];
$var['searchColumn'] = '';
/* DEFAULT ATTRIBUTES END */
//updating the variables to what was passed
foreach($_REQUEST as $key => $value) $var[$key] = $value;
if (isset($_REQUEST['sort']))
{
    $arr = json_decode(stripslashes($_REQUEST['sort']));
	$var['sort'] = $arr[0]->property;
	$var['dir'] = $arr[0]->direction;
}
if(isset($_REQUEST['searchColumn']) && strlen($_REQUEST['searchColumn']))
	$var['searchColumn'] = $_REQUEST['searchColumn'];
else
	$var['searchColumn'] = 'patient.patientname';
/* CREATING SEARCH CONDITIONS START */
$conditions = "";
if(isset($var['search']) && $var['search'] != "")
{
	$keywords = explode(" ", $var['search']);
	if (!isset($_REQUEST['searchColumn']))
		$col = 'study.patientid';
	else
		$col = $var['searchColumn'];
	$and = array();
	$or = array();
	for($i = 0; $i < count($keywords) && count($keywords) > 1;$i++)
	{
		if($col == 'study.studydate')
			array_push($and, $col." like '%".convertDateForMysql($keywords[$i])."%'");
		else
			array_push($and, $col." = '".convertDateForMysql($keywords[$i])."'");

	}
	if(count($and) > 0){
		array_push($or, "(".join($and, " AND ").")");
	}
    // about || $col == 'study.patientid' - we have 2 patients with simulary IDs. So we must use "=", not like
	if($col == 'study.studydate' || $col == 'study.patientid')
		array_push($or, $col." = '".convertDateForMysql($var['search'])."'");
	else
		array_push($or, $col." like '%".convertDateForMysql($var['search'])."%'");	
	$conditions = "(".join($or, " OR ").")";
}
/* CREATING SEARCH CONDITIONS END*/

/* CREATING FILTER CONDITIONS START */
if(isset($var['toDate']) && $var['toDate']){
	if($conditions != "") $conditions .= " AND ";
	$conditions .= " study.studydate <= '".$var['toDate']."' ";
}
if(isset($var['fromDate']) && $var['fromDate']){
	if($conditions != "") $conditions .= " AND ";
	$conditions .= " study.studydate >= '".$var['fromDate']."' ";
}
/* CREATING FILTER CONDITIONS END */

/* CREATING CONDITION FOR STUDIES RECIEVED TODAY */
//this is only used when we don't want to 
//show all records and there are no other conditions
if(!isset($var['showAll']) && $conditions == ""){
  $conditions .= " DATE(study.received)=CURDATE() ";
}
else
{
//    print_r($_REQUEST);
//    die();
}    

/* QUERY DEFAULT CONFIG START */
$columns = "
		study.uuid as uid
		,study.patientid
		,study.referringphysician
		,study.description
		,study.reviewed
		,study.REVIEWED_USER_ID
		,study.REVIEWED_DATE
		,study.REVIEWED_USER
		,study.datetime
		,patient.patientname
        ,patient.firstname
		,patient.lastname
		,patient.institution
		,DATE_FORMAT(patient.birthdate, '%m/%d/%Y') as dob
		,ifnull((select `series`.`modality` AS `modality` from `series` where (`study`.`uuid` = `series`.`studyuid`) order by `series`.`seriesnumber` limit 1), study.modality) as modality
        ,CONCAT(
				(select count(*) from studynotes where uuid = study.uuid),
				' Note(s) & ', 
				(select count(*) from attachment where uuid = study.uuid), 
				' Attachment(s)'
				)
		as notes,
		case when study.REVIEWED_USER_ID then
		concat('Reviewed by ',
				study.REVIEWED_USER,
				' on ',
				DATE_FORMAT(REVIEWED_DATE, '%m/%d/%y %H:%i')
				) 
		end as reviewed_text,
		case when study.is_critical = 1 then '!' else '' end as is_critical,
		DATE_FORMAT(study.critical_date, '%m/%d/%Y %H:%i') as critical_date,
		DATE_FORMAT(study.mailed_date, '%m/%d/%Y %H:%i') as mailed_date,
		(select count(*) from series s inner join image i on i.seriesuid = s.uuid where s.studyuid = study.uuid) as images_cnt,
		study.has_attached_orders,
		DATE_FORMAT(note_date, '%m/%d/%Y %H:%i') as note_date,
		note_text,
		note_user,
		has_tech_notes
";
//this is a special condition that allows us to pull results based on username
if(trim($user->lastname) != "" && $user->lastname != "null"){
	$privilegeCondition = "\n(study.referringphysician like '%".$user->lastname."%' ";
}else{
	$privilegeCondition = "\n(";
}

$db = MySQLDatabase::GetInstance($DB_DATABASE);
$result = $db->ExecuteReader("select filterdata, gt.name type from rprs_users u\n".
							"join rprs_group_users gu on u.id = gu.userid\n".
							"join rprs_groups g on g.id = gu.groupid\n".
							"join rprs_group_types gt on g.grouptypeid = gt.id\n".
							"where username = '".$username."'");
if($result->recordCount > 0 && ($user->selfonly != "1" || !$user->selfonly)){							
	$institutionData = array();
	$referringData = array();
	$consultData = array();
	while($row = $result->GetNextAssoc()){
		$criteria = explode("|", $row["filterdata"]);
		if($row["type"] == "Referring Physician"){
			$referringData = array_merge($criteria, $referringData);
		}else if ($row["type"] == "Institution"){
			$institutionData = array_merge($criteria, $institutionData);
		}else if ($row["type"] == "Consult"){
			$consultData = array_merge($criteria, $consultData);
		}
	}
	if(sizeof($referringData) > 0){
		if($privilegeCondition != "\n(") $privilegeCondition .= " or ";
		$privilegeCondition .= "study.referringphysician in ('". join("','", $referringData) . "')";
	}	
	if(sizeof($institutionData ) > 0){
		if($privilegeCondition != "\n(") $privilegeCondition .= " or ";
		$privilegeCondition .= "patient.institution in ('". join("','", $institutionData). "')";
	}
	if(sizeof($consultData ) > 0){
		if($privilegeCondition != "\n(") $privilegeCondition .= " or ";
		$privilegeCondition .= "patient.origid in ('". join("','", $consultData). "')";
	}
	
}
$privilegeCondition .= ")";
$join = "\n LEFT JOIN v_patient patient ON study.patientid = patient.origid ";
/* QUERY DEFAULT CONFIG END*/

/* SORTING CONFIG START */
if($var['sort'] == "patientname")
	$order = " ORDER BY patient.lastname ".$var['dir']." ";
elseif($var['sort'] == "patientid")
{
	$order = " ORDER BY CONVERT(patientid, UNSIGNED INTEGER) ".$var['dir'].", patientid ".$var['dir']." ";
}
elseif(!isset($_REQUEST['sort']))
{
	$order = "\nORDER BY `study`.`is_critical` DESC , `study`.`studydate` desc, `study`.`studytime` desc";
}
else
{
    if ($var['sort'] == 'datetime')
    $order = " ORDER BY STR_TO_DATE(datetime, '%m/%d/%Y %H:%i') ".$var['dir']." ";
        else
    $order = " ORDER BY ".$var['sort']." ".$var['dir']." ";
}
/* SORTING CONFIG END */

/* PAGINATION CONFIG START */
$recordcount = 0;
$limit = " LIMIT ".$var['start'].",".$var['limit']."\n\n";
/* PAGINATION CONFIG END */

if(isset($var['showAll']) || $conditions != ""){
  $result = search($columns, $conditions, $join, $order, $limit);
}
//if there isn't any records to show lets populate the fromRange
//and not trying to search

if($result->recordCount == 0 && (!isset($var['search']) || $var['search'] == "")){
	$result = search($columns, "study.studydate >= '".@date('Y-m-d',mktime(0,0,0,@date("m")-$SEARCH_LAST_MONTHS,@date("d"),@date("Y")))."'", $join, $order, $limit);
}
//echo '<pre>'.print_r($result, 1).'</pre>';

print $result->toJSON();

/******************************************************/
/** FUNCTIONS                                        **/
/******************************************************/
function search($columns, $conditions, $join, $order, $limit){
	global $DB_DATABASE;
	$query = '';
	$db = MySQLDatabase::GetInstance($DB_DATABASE);
	global $privilegeCondition;
	global $viewAccess;
//	$username = $dbcon->username;
	$queryStr = "SELECT ".$columns." FROM v_study study ".$join;
	$queryCount = "SELECT count(*) as num FROM v_study study ".$join;
	$list = array();
	if(!$viewAccess) array_push($list, $privilegeCondition);
	if($conditions != "")array_push($list, $conditions);
	if(count($list) > 0 )$query .= " \nWHERE ".join(" \nAND ", $list);
	if (strlen($query) == 0)
		$query = ' where 1 = 1';
	global $additional_where;
	$query .= $additional_where;
	$queryCount .= $query;
	$queryStr .= $query." ".$order." ".$limit;
	$cresult = $db->ExecuteReader($queryCount);
	$crow = $cresult->GetNextArray();
	$recordcount = 0;
	if ($crow && is_array($crow) && isset($crow[0]))
		$recordcount = $crow[0];
	$result = $db->ExecuteReader($queryStr);
	echo "/*" . $queryStr.'*/';
	$result->recordCount = $recordcount;
  return $result;
}
// input MM/DD/YYYY, output is YYYY-MM-DD
function convertDateForMysql($dateStr)
{
//	return "DATE_FORMAT('$dateStr','%m/%d/%Y') ";
	$arr = explode('/', $dateStr);
	if (count($arr) != 3)
		return $dateStr;
	$res = $arr[2].'-'.$arr[0].'-'.$arr[1];
	return $res;
}
