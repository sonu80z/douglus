<?php
//
// db/Study.php
//
// Database file for retreiving all study information from the database.
//
// 
//
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
$var['limit'] = 15;
/* DEFAULT ATTRIBUTES END */
//updating the variables to what was passed
foreach($_GET as $key => $value) $var[$key] = $value;


/* CREATING SEARCH CONDITIONS START */
$conditions = "";
if($var['search'] != "" && $var['searchColumn'] != ""){
	$keywords = split(" ", $var['search']);
	$col = $var['searchColumn'];
	$and = array();
	$or = array();
	for($i = 0; $i < count($keywords) && count($keywords) > 1;$i++){
		array_push($and, $col." = '".Format::MySQL($keywords[$i])."'");
	}
	if(count($and) > 0){
		array_push($or, "(".join($and, " AND ").")");
	}
	array_push($or, $col." = '".Format::MySQL($var['search'])."'");
	$conditions = "(".join($or, " OR ").")";
}
/* CREATING SEARCH CONDITIONS END*/

/* CREATING FILTER CONDITIONS START */
if($var['toDate']){
	if($conditions != "") $conditions .= " AND ";
	$conditions .= " study.studydate <= '".$var['toDate']."' ";
}
if($var['fromDate']){
	if($conditions != "") $conditions .= " AND ";
	$conditions .= " study.studydate >= '".$var['fromDate']."' ";
}
/* CREATING FILTER CONDITIONS END */

/* CREATING CONDITION FOR STUDIES RECIEVED TODAY */
//this is only used when we don't want to 
//show all records and there are no other conditions
if(!$var['showAll'] && $conditions == ""){
  $conditions .= " DATE(study.received)=CURDATE() ";
}

/* QUERY DEFAULT CONFIG START */
$columns = <<<heredoc
		study.uuid as uid
		,study.patientid
		,study.referringphysician
		,study.description
		,study.reviewed
		,study.datetime
		,patient.patientname
		,(select modality from series where series.studyuid = study.uuid limit 1) as modality
		,CONCAT((select count(*) from studynotes where uuid = study.uuid),' Note(s) & ', \n(select count(*) from attachment where uuid = study.uuid), ' Attachment(s)') as notes
heredoc;
//this is a special condition that allows us to pull results based on username
/*
if(trim($user->lastname) != "" && $user->lastname != "null"){
	$privilegeCondition = "\n(study.referringphysician like '%".$user->lastname."%' ";
}else{
	$privilegeCondition = "\n(";
}
*/
$privilegeCondition = "\n(";
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
if($var['sort'] == "patientname")$var['sort'] = 'patient.lastname';
$order = " ORDER BY ".$var['sort']." ".$var['dir']." ";
if($var['sort'] == "datetime")$order = "\nORDER BY StudyDate ". $var['dir'] . "\n";//.$var['dir'].", StudyTime ".$var['dir']."\n";
/* SORTING CONFIG END */

/* PAGINATION CONFIG START */
$recordcount = 0;
$limit = " LIMIT ".$var['start'].",".$var['limit']."\n\n";
/* PAGINATION CONFIG END */

if($var['showAll'] || $conditions != ""){
  $result = search($columns, $conditions, $join, $order, $limit);
}
//if there isn't any records to show lets populate the fromRange
//and not trying to search
if($result->recordCount == 0 && $var['search'] == "" ){
	$result = search($columns, "", $join, $order, $limit);
}

print $result->toJSON();

/******************************************************/
/** FUNCTIONS                                        **/
/******************************************************/
function search($columns, $conditions, $join, $order, $limit){
	global $DB_DATABASE;
	$db = MySQLDatabase::GetInstance($DB_DATABASE);
	global $privilegeCondition;
	global $viewAccess;
	$username = $dbcon->username;
	$queryLimit = "SELECT ".$columns." FROM v_study study ".$join;
	$queryCount = "SELECT count(*) as num FROM v_study study ".$join;
	$list = array();
	if(!$viewAccess) array_push($list, $privilegeCondition);
	if($conditions != "")array_push($list, $conditions);
	if(count($list) > 0 )$query .= " \nWHERE ".join(" \nAND ", $list);
	$queryCount .= $query;
	$queryLimit .= $query." ".$order." ".$limit;
	$cresult = $db->ExecuteReader($queryCount);
	$crow = $cresult->GetNextArray();
	$recordcount = $crow[0];
	debug($queryLimit);
	$result = $db->ExecuteReader($queryLimit);
	//print $queryLimit;
	$result->recordCount = $recordcount;
  return $result;
}
?>
