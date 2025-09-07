<?php
import("system.models.Group");
import("system.models.GroupUser");
import("system.controls.control");
import("system.core.database.MySQLDatabase");
require_once('Control.php');

class LogsControl extends Control
{
	var $dateStart;
	var $dateStop;
	var $logType;
	var $resultArray = array();
	var $resultRecordsCount = 0;
	
	public function __construct()
	{
//		$this->test();
	}
	
	private function setLocalVars($startParameters)
	{
//		die('<pre>'.print_r($startParameters, 1).'</pre>');
		$this->dateStart	= strtotime($startParameters['dstart']);
		$this->dateStop		= strtotime($startParameters['dstop']);
		$this->dateStop = strtotime("+1 day", $this->dateStop);

		$this->logType		= $_REQUEST['logType'];
//				die(date("Y-m-d", $this->dateStart).' ---------------  '.date("Y-m-d", $this->dateStop).' ---------------  '.date("Y-m-d", $mk));

	}
	
	public function test()
	{
		$_POST['dstart'] = '2010-01-01';
		$_POST['dstop'] = '2014-06-25';
//		$this->setLocalVars($_POST);
		$arr = $this->getFileList(strtotime($_POST['dstart']), strtotime($_POST['dstop']));
		
$res = array(
0 => 'activity-log-01-2010.csv',
1 => 'activity-log-02-2010.csv',
2 => 'activity-log-03-2010.csv',
3 => 'activity-log-04-2010.csv',
4 => 'activity-log-05-2010.csv',
5 => 'activity-log-06-2010.csv',
6 => 'activity-log-07-2010.csv',
7 => 'activity-log-08-2010.csv',
8 => 'activity-log-09-2010.csv',
9 => 'activity-log-10-2010.csv',
10 => 'activity-log-11-2010.csv',
11 => 'activity-log-12-2010.csv',
12 => 'activity-log-01-2011.csv',
13 => 'activity-log-02-2011.csv',
14 => 'activity-log-03-2011.csv',
15 => 'activity-log-04-2011.csv',
16 => 'activity-log-05-2011.csv',
17 => 'activity-log-06-2011.csv',
18 => 'activity-log-07-2011.csv',
19 => 'activity-log-08-2011.csv',
20 => 'activity-log-09-2011.csv',
21 => 'activity-log-10-2011.csv',
22 => 'activity-log-11-2011.csv',
23 => 'activity-log-12-2011.csv',
24 => 'activity-log-01-2012.csv',
25 => 'activity-log-02-2012.csv',
26 => 'activity-log-03-2012.csv',
27 => 'activity-log-04-2012.csv',
28 => 'activity-log-05-2012.csv',
29 => 'activity-log-06-2012.csv',
30 => 'activity-log-07-2012.csv',
31 => 'activity-log-08-2012.csv',
32 => 'activity-log-09-2012.csv',
33 => 'activity-log-10-2012.csv',
34 => 'activity-log-11-2012.csv',
35 => 'activity-log-12-2012.csv',
36 => 'activity-log-01-2013.csv',
37 => 'activity-log-02-2013.csv',
38 => 'activity-log-03-2013.csv',
39 => 'activity-log-04-2013.csv',
40 => 'activity-log-05-2013.csv',
41 => 'activity-log-06-2013.csv',
42 => 'activity-log-07-2013.csv',
43 => 'activity-log-08-2013.csv',
44 => 'activity-log-09-2013.csv',
45 => 'activity-log-10-2013.csv',
46 => 'activity-log-11-2013.csv',
47 => 'activity-log-12-2013.csv',
48 => 'activity-log-01-2014.csv',
49 => 'activity-log-02-2014.csv',
50 => 'activity-log-03-2014.csv',
51 => 'activity-log-04-2014.csv',
52 => 'activity-log-05-2014.csv',
53 => 'activity-log-06-2014.csv');
		if ($arr !== $res)
		{
			throw new Exception(__FILE__.'unit test failed!');
		}
	}
	
	private function processCsvFile($file)
	{
		$handle = fopen($file, 'r');
		if (!$handle)
			throw new Exception('could not open file '.$file);
		while (($data = fgetcsv($handle)) !== FALSE)
		{
			$tempRes = array();
//			echo '<br><br><br><br>*********'.print_r($data, 1);
			if($this->isCsvRowUnderCondition($data))
			{
				$tempRes['date'] = $data[0];
				$tempRes['user'] = $data[1];
				$tempRes['type'] = $data[2];
				$tempRes['text'] = $data[3];
				$this->resultArray[] = $tempRes;
			}
		}
		fclose($handle);
		
		$v = '';
		$this->resultRecordsCount = count($this->resultArray);
//		foreach ($this->resultArray as $cur)
//		{
//			$v .= implode($cur, ',')."\n";
////			die('<pre>'.print_r($cur, 1).'</pre>');
//		}
		return $this->resultArray;
	}
	
	private function isCsvRowUnderCondition($csvRow)
	{
		$dateTimeStr = $csvRow[0];
		list($m, $d, $y) = explode('/', $dateTimeStr);
		$t = substr($y, 5);

		$y = substr($y, 0, 4);
		list($hh, $mm, $ss) = explode(':', $t);
		$mk = mktime($hh, $mm, $ss, $m, $d, $y);
//		$mk = strtotime("+1 day", $mk);

//		print_r($_REQUEST);
//		die(date("Y-m-d", $this->dateStart).' ---------------  '.date("Y-m-d", $this->dateStop).' ---------------  '.date("Y-m-d", $mk));

		if (($this->dateStart <= $mk) && ($mk < $this->dateStop) && (($csvRow[2] == $this->logType) || ($this->logType == 'All')))
		{
//			echo '<pre>'.print_r($csvRow, 1).'</pre>';
			return true;
		}
		else 
			return false;
//		die();
	}
	
	public function getLogs($args)
	{
            return $this->getLogsFromDB($args);
	}
	
	public function getLogsFromDB($args)
	{
//		die(print_r($args, 1));
		$this->setLocalVars($args);
		$dstart = strtotime($args['dstart']);
		$dstop = strtotime($args['dstop'].' +1 day');
		$db = MySQLDatabase::GetInstance();
//		$search = '';	
//		if (isset($args['search']))
//			$search = $args['search'];
		
		$WHERE = ' where `event_date` between \''.date('Y-m-d', 
		$dstart
		).'\' and \''.date('Y-m-d', 
		$dstop
		).'\' '
		;
		if ($this->logType != 'All'){
			//$WHERE .= ' and `event_type` = \''.mysql_escape_string($this->logType)."'"; 
			$WHERE .= ' and `event_type` = \''.addslashes($this->logType)."'"; 
		}
//		die($args['study_id']);
		if(isset($args['study_id']) && $args['study_id'] && strlen($args['study_id']) > 0)
		{
			$WHERE = ' WHERE `event_table_id` = \''.$args['study_id']."' and `event_table` = 'study' "; 
		}
//		$WHERE = '';
//		if (strlen($search) > 0)
//			$WHERE = "where `mail` like '%$search%' or `institution` like '%$search%' ";

		$sql = "SELECT 
	`event_date`, 
	`user_id`, 
	(SELECT CONCAT(  `FIRSTNAME` ,  ' ',  `LASTNAME` ) 
	FROM  `rprs_users` 
	WHERE id = e.user_id
	) `user_name`,  
	`event_type` ,  
	`event_table`, 
	'' studydate, 
	'' patient_name, 
	`event_table_id`, 
	`additional_text` 
FROM  `eventlog` e
".$WHERE.
" AND  `e`.`event_table` !=  'study' ".
' ORDER BY `e`.`event_date` ';
        if(isset($args['study_id'])){        
		$sql = "SELECT 
	`event_date`, 
	`user_id`, 
	(SELECT CONCAT(  `FIRSTNAME` ,  ' ',  `LASTNAME` ) 
	FROM  `rprs_users` 
	WHERE id = e.user_id
	) `user_name`,  
	`event_type` ,  
	`event_table`, 
	DATE_FORMAT(`s`.`studydate`, '%m/%d/%Y' ) studydate, 
	(SELECT patientname
	FROM v_patient p
	WHERE p.origid = s.patientid
	) patient_name, 
	`event_table_id`, 
	`additional_text` 
FROM  `eventlog` e
LEFT JOIN  `study` s ON s.uuid =  '$args[study_id]'
AND  `e`.`event_table` =  'study' 
".$WHERE.
' ORDER BY `e`.`event_date` ';
        }
//		die($sql);
//		die("a__".  rand(0, 2000)." ".$sql);
                $result = $db->ExecuteReader($sql);
                $res = array();
                while($row = $result->GetNextAssoc())
                {
                    if (true)
                    {
                        $row['additional_text'] = substr($row['additional_text'], 0, strpos($row['additional_text'], 'Array')).'none';
                    }
                    $res[] = $row;
                }

//                die('<pre>'.print_r($row, 1).'</pre>');
		return json_encode($res);
	}
	
	private function getFileList($dateStrStart, $dateStrStop)
	{
		$this->setLocalVars($_POST);
		
		$monthFrom	= date('m', $dateStrStart);
		$monthTo	= date('m', $dateStrStop);
		
		$yearFrom	= date('Y', $dateStrStart);
		$yearTo	= date('Y', $dateStrStop);
		$arr = array();
		for($curYear = $yearFrom; $curYear <= $yearTo; $curYear++)
		{
			$_curYear = $curYear;
//			echo '<li>'.$_curYear;
			for($curMonth = $monthFrom; $curMonth <= 12; $curMonth++)
			{
				$_curMonth = $curMonth;
				$_curMonth = $_curMonth + 0;
				if ($_curMonth < 10)
					$_curMonth = '0'.$_curMonth;
				
//				echo '<li>'.$_curYear.'-'.$_curMonth;
				
				
				$logFileName = 'activity-log-'.$_curMonth.'-'.$_curYear.'.csv';
				$arr[] = $logFileName;
				if (($curYear == $yearTo) && ($curMonth == $monthTo))
					break;
//				$logFileName = 
			}
		}
		return $arr;
	}
}

?>