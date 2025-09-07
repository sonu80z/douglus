<?php

class logger 
{
    public static function log2($eventName, $msg = "", $user = "")
    {
        $path = $_SERVER["DOCUMENT_ROOT"]."/system/logs/";
        if(!file_exists($path))
                mkdir($path, 0700);
        $f = fopen($path . logger::getFileName(), "a");
        fwrite($f, @date("m/d/Y H:i:s") . "," . logger::getUserName($user) . "," . $eventName . "," . str_replace(',', '\\,', str_replace("\n", ' ',$msg)) . " \n");
        fclose($f);
    }

    public static function log($arguments)
    {
//die('<pre>'.print_r($arguments, 1).'</pre>');
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
import("system.core.database.MySQLDatabase");
$db = MySQLDatabase::GetInstance();

//if (empty($arguments['event_date']))
//	$arguments['event_date'] = 'sysdate()';

if (!isset($_SESSION))
{
session_start();
}
$u = @unserialize($_SESSION['AUTH_USER']);
if (!$u)
    $user_id = 1;
else $user_id = $u->id;

if (empty($arguments['user_id']))
$arguments['user_id'] = $user_id;

if (empty($arguments['event_type']))
$arguments['event_type'] = 'default';


if (empty($arguments['event_table']))
$arguments['event_table'] = "null";
else
$arguments['event_table'] = "'".  addslashes($arguments['event_table'])."'";

//	die('==='.$arguments['event_table_id']);

if (empty($arguments['event_table_id']))
$arguments['event_table_id'] = "null";
else
$arguments['event_table_id'] = "'".addslashes($arguments['event_table_id'])."'";

//die('>>>'.$arguments['event_table_id']);

//if (empty($arguments['event_table_id']))
//	$arguments['user_id'] = 'sysdate()';
if (empty($arguments['additional_text']))
$arguments['additional_text'] = '';
else
{
$arguments['additional_text'] = str_replace("\n", ' ', $arguments['additional_text']);
$arguments['additional_text'] = "'".addslashes($arguments['additional_text'])."'";
}
$sql = " insert into `eventlog` (
`user_id`, `event_type`, `event_table`, `event_table_id`, `additional_text`
)
values ('".addslashes($arguments['user_id'])."', '".
                addslashes($arguments['event_type'])."', ".
                $arguments['event_table'].", ".
                $arguments['event_table_id'].", ".
                $arguments['additional_text'].") ";
//die('<pre>'.print_r($sql, 1).'</pre>');
$res = $db->ExecuteNonQuery($sql);

//event_date
//user_id
//event_type
//event_table
//event_table_id
//additional_text
//			die('<pre>'.print_r($arguments, 1).'</pre>');
    }

    private static function getFileName()
    {
        return "activity-log-" . @date("m-Y") . ".csv"; 
    }

    private static function getUserName($user)
    {
        if($user == "" && isset($_SESSION['AUTH_USER']))
                $user = unserialize($_SESSION['AUTH_USER']);

        // fixed the warning:
//<br />
//<b>Warning</b>:  Cannot modify header information - headers already sent by (output started at C:\www\rentacoder\MDI\system\core\reflection\Metadata.php:73) in <b>C:\www\rentacoder\MDI\system\authenticate.php</b> on line <b>40</b><br />
        if(!$user)
                return "Unknown-User (not logged in)";

        return $user->firstname.' '.$user->lastname;
    }
}
?>