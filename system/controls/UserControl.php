<?php
import("system.models.User");
import("system.controls.control");
import("system.core.database.MySQLDatabase");
class UserControl extends Control
{
    public function printStudyNote($args)
    {
        $db = MySQLDatabase::GetInstance();
        $sql ="select uuid,
referringphysician ,
description ,
datetime ,
(select p.lastname from patient p where p.origid = patientid) patient_lastname ,
(select p.firstname from patient p where p.origid = patientid) patient_firstname ,
(select modality from series where series.studyuid = uuid limit 1) as modality, 
DATE_FORMAT(note_date, '%m/%d/%Y %H:%i') as note_date, 
note_text, 
note_user 
from v_study 
where uuid = '$args[studyID]'";

        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/system/viewer/printNoteDlg.html');
        $arr = $db->ExecuteReader($sql)->GetNextAssoc();
        if ($arr);
        {
                $content = str_replace('%physician%', $arr['referringphysician'], $content);
                $content = str_replace('%descr%', $arr['description'], $content);
                $content = str_replace('%date%', $arr['datetime'], $content);
                $content = str_replace('%name%', $arr['patient_lastname'].' '.$arr['patient_firstname'], $content);
                $content = str_replace('%modality% ', $arr['modality'], $content);
                $content = str_replace('%added_date%', $arr['note_date'], $content);
                $content = str_replace('%text%', $arr['note_text'], $content);
                $content = str_replace('%added_user%', $arr['note_user'], $content);
//			$content = str_replace('', $arr[''], $content);
//			print_r($arr);
//			die('aaaaaa');
        }		

        die($content);
    }

    public function ViewPhysicansGrid($args)
    {
        $db = MySQLDatabase::GetInstance();
        $sortField = 'username';
        if (isset($args['sort']))
                $sortField = $args['sort'];
        $dir = 'ASC';	
        if (isset($args['dir']))
                $dir = $args['dir'];

        $search = '';	
        if (isset($args['search']))
                $search = $args['search'];

        $WHERE = '';
        if (strlen($search) > 0)
                $WHERE = "where `mail` like '%$search%' or `username` like '%$search%' ";

        $sql = "SELECT `id`, `mail`, `username`, `user_id` ".
        " FROM `physician_mail_addresses` ".
        $WHERE.
        " ORDER BY `$sortField` $dir";

//		die('<pre>'.print_r($sql, 1).'</pre>');
        return $db->ExecuteReader($sql)->toJSON();
    }

    public function ViewPhysicans($args)
    {
        $db = MySQLDatabase::GetInstance();
        return $db->ExecuteReader("SELECT distinct `referringphysician` as fullname ".
                                                                " FROM `study` ".
                                                                " ORDER BY `referringphysician`")->toJSON();		
    }

    public function save($args)
    {
        $db = MySQLDatabase::GetInstance();
        if (empty($args['mail']) || empty($args['user']))
        {
            $this->return["error_msg"]= "Mail is empty!";
            return JSON::Encode($this->return);
        }
        if (empty($args['rid']))
            /*$res = $db->ExecuteNonQuery(" insert into `physician_mail_addresses` (`username`, `user_id`, `MAIL`) ".
                                                                        " values ('".mysql_escape_string($args['user'])."', null, '".mysql_escape_string($args['mail'])."') ");*/
			$res = $db->ExecuteNonQuery(" insert into `physician_mail_addresses` (`username`, `user_id`, `MAIL`) ".
                                                                        " values ('".addslashes($args['user'])."', null, '".addslashes($args['mail'])."') ");
        else 
            $res = $db->ExecuteNonQuery(" update `physician_mail_addresses` set `username` = '$args[user]', `MAIL` = '$args[mail]' where `id` = '$args[rid]' ");
        if ($res->error)
        {
            $this->return["error_msg"]= $res->error;
            return JSON::Encode($this->return);
        }

        import('system.logger');
        $_logEvent = array();
        $_logEvent['event_type'] = 'Email updated';
        $_logEvent['event_table'] = 'physician_mail_addresses';
        if (!isset($args['rid']))
            $args['rid'] = '';
        $_logEvent['event_table_id'] = $args['rid'];
        $_logEvent['additional_text'] = "Changes in physician's email - User: $args[user], Email: $args[mail]";
        logger::log($_logEvent);

        $this->return["success"]= 'true';
        return JSON::Encode($this->return);
    }

    public function remove($args)
    {
        $db = MySQLDatabase::GetInstance();
        if (empty($args['rid']))
        {
                $this->return["error_msg"]= "Incorrect parameter!";
                return JSON::Encode($this->return);
        }
        $res = $db->ExecuteNonQuery(" delete from  `physician_mail_addresses` where id = '$args[rid]'");
        if ($res->error)
        {
                        $this->return["error_msg"]= $res->error;
                        return JSON::Encode($this->return);
        }

        import('system.logger');
        $_logEvent = array();
        $_logEvent['event_type'] = 'Email deleted';
        $_logEvent['event_table'] = 'physician_mail_addresses';
        $_logEvent['event_table_id'] = $args['rid'];
        $_logEvent['additional_text'] = "Physician's email deleted: id = $args[rid]";
        logger::log($_logEvent);

        $this->return["success"]= 'true';
        return JSON::Encode($this->return);
    }

    public function View($args)
    {
        $db = MySQLDatabase::GetInstance();
//		$params = $this->GetQueryParams($args, array("start"=>"0", "limit"=>"12", "dir"=>"ASC", "sort"=>"username"));
        if(!isset($args["groupid"]) || 0 == strlen($args["groupid"]))
        {
            $sortField = 'USERNAME';
            $dir = 'ASC';
            if (isset($args['sort']))
            {
                $arr = json_decode(stripslashes($_REQUEST['sort']));
                $sortField = $arr[0]->property;
                $dir = $arr[0]->direction;
            }
            
            $search = '';	
            if (isset($args['search']))
                    $search = $args['search'];

            $WHERE = '';
            if (isset($args['search']) && (strlen($args['search'])>0))
            {
                    $WHERE = " where `USERNAME` like '%$args[search]%' or 
                                                     `FIRSTNAME` like '%$args[search]%'  or 
                                                     `LASTNAME` like '%$args[search]%' ";
            }	
            $sql = "select `ID`, `USERNAME`, `PASSWORD`, `FIRSTNAME`, `MIDDLENAME`, `LASTNAME`, ".
                            " `SELFONLY`, `ADMIN`, `PASSWORDEXPIRED`, `CANBATCHPRINTPDFS`, `CANMAILPDF`, `CANMARKASREVIEWED`, IFNULL(`CANBURNCD`, 0) CANBURNCD, IFNULL(`CANMARKCRITICAL`, 0) CANMARKCRITICAL, IFNULL(`CANATTACHORDER`, 0) CANATTACHORDER, IFNULL(`CANADDNOTE`, 0) CANADDNOTE, IFNULL(`STAFFROLE`, 0) STAFFROLE".
                            " from `rprs_users` ".
            $WHERE.
            " ORDER BY `$sortField` $dir";
//			die($sql);
            return $db->ExecuteReader($sql)->toJSON();
        }
        else
        {
            return $db->ExecuteReader("select u.id, u.username from rprs_users u where u.admin = 0 and u.selfonly = 0 and u.id not in (select gu.userid from rprs_group_users gu where gu.groupid = " . $args["groupid"] . " ) order by u.username")->toJSON();
        }
    }
    public function Delete($args)
    {
        if($args["username"] != "root")
        {
            import('system.logger');
            $_logEvent = array();
            $_logEvent['event_type'] = 'User deleted';
            $_logEvent['event_table'] = 'rprs_group_users';
            $_logEvent['event_table_id'] = $args['id'];
            $_logEvent['additional_text'] = 'User deleted'.': '.print_r($args, 1);
            logger::log($_logEvent);
        }
        else
        {
            $this->return["msg"] = "You cannot delete the root user account.";
            return $this->GetJSON($this->return);
        }
        $gu = new GroupUser();
        $gu->userid = $args['id'];
        $res = $this->controller->Delete($gu);
        $res = $this->controller->Delete(new User($args));
        $this->return["success"] = 'true';
        unset($this->return["msg"]);
        return $this->GetJSON($this->return);
    }
    public function Add($args)
    {
        import('system.logger');

        $_logEvent = array();
        $_logEvent['event_type'] = 'User added';
        $_logEvent['event_table'] = 'rprs_group_users';
//		$_logEvent['event_table_id'] = $args['groupid'];
        $_logEvent['additional_text'] = 'User added: '.print_r($args, 1);
        logger::log($_logEvent);		

        return $this->GetJSON($this->controller->Add(new User($args)));
    }
    public function Update($args)
    {
        if($args["password"] == '') unset($args["password"]);
        import('system.logger');
        $_logEvent = array();
        $_logEvent['event_type'] = 'User updated';
        $_logEvent['event_table'] = 'rprs_group_users';
//		$_logEvent['event_table_id'] = $args['groupid'];
        $_logEvent['additional_text'] = 'User updated: '.print_r($args, 1);
        logger::log($_logEvent);		

        return $this->GetJSON($this->controller->Update(new User($args)));
    }
}

?>