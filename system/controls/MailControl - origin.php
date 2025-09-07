<?php
import("system.models.Group");
import("system.models.GroupUser");
import("system.controls.control");
import("system.core.database.MySQLDatabase");
require_once('Control.php');
//include_once('fix_mysql.inc.php');

//		die('<pre>'.print_r($_REQUEST, 1).'</pre>');
class MailControl extends Control
{
    public function ViewMails($args)
    {
        $db = MySQLDatabase::GetInstance();
        $institution_res = $db->ExecuteReader("select institution ".
            " from patient ".
            " where origid = (select patientid from study where ".
            " uuid = '" . $args['studyID'] . "' limit 1) ".
            " limit 1")->GetNextAssoc();
        $parameter = array("institution"=>$institution_res['institution']);
        
        $params = $this->GetQueryParams($parameter, array("sort"=>"mail", 'dir'=>'ASC'));
        $m = new MailType();
        $m->institution = $institution_res['institution'];
        return $this->controller->FindAll($m, $params)->toJSON();
    }
    
    public function save($args)
    {
        $db = MySQLDatabase::GetInstance();
        if (empty($args['mail']) || empty($args['institution']))
        {
            $this->return["error_msg"]= "Mail is empty!";
            return JSON::Encode($this->return);
        }
        if (empty($args['rid']))
            $res = $db->ExecuteNonQuery(" insert into `institution_mail_addresses` (`institution`, `MAIL`) ".
                " values ('$args[institution]','$args[mail]') ");
            else
                $res = $db->ExecuteNonQuery(" update `institution_mail_addresses` set `institution` = '$args[institution]', `MAIL` = '$args[mail]' where `id` = '$args[rid]' ");
                if ($res->error)
                {
                    $this->return["error_msg"]= $res->error;
                    return JSON::Encode($this->return);
                }
                
                import('system.logger');
                
                $_logEvent = array();
                $_logEvent['event_type'] = 'Email updated';
                //		$_logEvent['event_table'] = '';
                //		$_logEvent['event_table_id'] = '';
                $_logEvent['additional_text'] = "Changes in institution's email - Institution: $args[institution], Mail: $args[mail]";
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
        $res = $db->ExecuteNonQuery(" delete from  `institution_mail_addresses` where id = '$args[rid]'");
        if ($res->error)
        {
            $this->return["error_msg"]= $res->error;
            return JSON::Encode($this->return);
        }
        
        import('system.logger');
        $_logEvent = array();
        $_logEvent['event_type'] = 'Email updated';
        //		$_logEvent['event_table'] = '';
        //		$_logEvent['event_table_id'] = '';
        $_logEvent['additional_text'] = "Institution's email deleted - ID: $args[rid]";
        logger::log($_logEvent);
        
        $this->return["success"]= 'true';
        return JSON::Encode($this->return);
    }
    
    public function ViewInstitution($args)
    {
        $db = MySQLDatabase::GetInstance();
        
        $userData = unserialize($_SESSION['AUTH_USER']);
        $username = $userData->username;
        
        $sql = "select filterdata, gt.name type from rprs_users u\n".
            "join rprs_group_users gu on u.id = gu.userid\n".
            "join rprs_groups g on g.id = gu.groupid\n".
            "join rprs_group_types gt on g.grouptypeid = gt.id\n".
            "where username = '".$username."'\n".
            "  and gt.name = 'Institution'";
        
        $result = $db->ExecuteReader($sql);
        $institutionData = array();
        if($result->recordCount > 0 && ($userData->selfonly != "1" || !$userData->selfonly))
        {
            $institutionData = array();
            while($row = $result->GetNextAssoc())
            {
                $criteria = explode("|", $row["filterdata"]);
                $institutionData = array_merge($criteria, $institutionData);
            }
        }
        
        $where_condition = '';
        if(count($institutionData ) > 0)
        {
            $where_condition = "where institution in ('". join("','", $institutionData). "')";
        }
        
        $sql = "SELECT DISTINCT `institution` ".
            " FROM `patient` ".
            $where_condition.
            " ORDER BY `institution`";
            //                die(print_r($institutionData));
            //                die($sql);
            
            return $db->ExecuteReader($sql)->toJSON();
            
    }
    
    public function ViewInstifutionMails($args)
    {
        $db = MySQLDatabase::GetInstance();
        $sortField = 'institution';
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
                        $WHERE = "where `mail` like '%$search%' or `institution` like '%$search%' ";
                        
                        $sql = "SELECT `id`, `mail`, `institution` ".
                            " FROM `institution_mail_addresses` ".
                            $WHERE.
                            " ORDER BY `$sortField` $dir";
                            
                            return $db->ExecuteReader($sql)->toJSON();
    }
    
    public function SendMail($args)
    {
        set_time_limit(120);
        include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
        $my_file = $args['studyID'];
        $my_path = $_SERVER["DOCUMENT_ROOT"];
        $my_name = $PRODUCT_NAME;
        $my_mail = $MAIL_FROM;
        $my_replyto = $MAIL_FROM;
        $my_subject = $args['mailSubject'];
        
        $my_message = $args['mailText'];
        $mailTo = $args['mailTo'];
        $this->return["success"]  = "true";
        
        include_once($_SERVER["DOCUMENT_ROOT"]."/system/legacy/locale.php");
        include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
        
        import('system.utilities.*');
        import('system.core.orm.DataController');
        import('system.models.*');
        
        $user = unserialize($_SESSION["AUTH_USER"]);
        $controller = new DataController($DB_DATABASE);
        
        $worklist = new Worklist();
        $study = new Study();
        
        $study->uuid = str_replace('transcriptions/', '', $args['studyID']);
        $study->uuid = str_replace('.pdf', '', $study->uuid);
        
        $study = $controller->Find(new Study(array("uuid"=>$study->uuid)))->toObject("Study");
        if (!count($study))
        {
            $this->return["message"]  = "Cannot locate the study! ";
            return JSON::Encode($this->return);
        }
        $study = $study[0];
        
        if (isset($args['noFileAttached']))
        {
            $my_file = '';
            $my_path = '';
        }
        else
        {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'].$args['studyID']))
            {
                $this->return["message"]  = "Mark the study as reviewed: Could not find a pdf file for the study. Can't mark the study as reviewed";
                return JSON::Encode($this->return);
            }
            $files = $my_path.$my_file;
        }
        if (isset($args['includeHippa']) && $args['includeHippa'] == 'true')
        {
            $db = MySQLDatabase::GetInstance();
            $sql = "select institution, lastname, firstname from patient where origid = '$study->patientid' limit 1";
            $institution_res = $db->ExecuteReader($sql)->GetNextAssoc();
            
            $args['to'] 		= $mailTo;
            $args['fax'] 		= $mailTo;
            
            $args['phone']		= $CONTACT_PHONE;
            $args['re'] 		= $institution_res['firstname'].' '.$institution_res['lastname'];
            
            $args['from'] 		= $LOGIN_TITLE;
            $args['pages']		= "2 including this cover sheet";
            $args['date']		= date('m-d-Y  H:i');
            $args['cc'] 		= 'NONE';
            $args['output'] 	= $_SERVER["DOCUMENT_ROOT"]."/temp/tempHIPPA".rand().'.pdf';
            if (file_exists($args['output']))
                unlink($args['output']);
                require_once('controls/PDFControl.php');
                $pdf = new PDFControl();
                $files = array();
                $files[] = $my_path.$my_file;
                $files[] = $args['output'];
                $pdf->createHIPAAPDF($args);
        }
        
        $ok = $this->mail_attachment($files, $mailTo, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
        //die($args['output']);
        // delete temp file
        if (isset($args) && isset($args['output']) && file_exists($args['output']))
            unlink($args['output']);
            
            import('system.logger');
            
            if ($ok)
            {
                $study->mailed_date = date('Y-m-d H:i:s');
                if ($MAIL_MARK_THE_STUDY_AS_REVIEWED)
                {
                    $_logEvent = array();
                    $_logEvent['event_type'] = 'Study reviewed';
                    $_logEvent['event_table'] = 'study';
                    $_logEvent['event_table_id'] = $study->uuid;
                    $_logEvent['additional_text'] = "Study was marked as reviewed (emailed). StudyDateTime: $study->studydate, StudyPatientName: $institution_res[firstname] $institution_res[lastname],  StudyUID: $study->uuid";
                    logger::log($_logEvent);
                    
                    $study->reviewed_user_id = $user->id;
                    $study->reviewed_date = date('Y-m-d H:i:s');
                }
                $_logEvent = array();
                $_logEvent['event_type'] = 'Emailed By Report';
                $_logEvent['event_table'] = 'study';
                $_logEvent['event_table_id'] = $study->uuid;
                $_logEvent['additional_text'] = "Study emailed. StudyDateTime: $study->studydate, StudyPatientName: $institution_res[firstname] $institution_res[lastname],  StudyUID: $study->uuid. Additional information: ".print_r($args, 1);
                logger::log($_logEvent);
            }
            
            if (!$ok)
            {
                $controller->Update($study);
                $this->return["success"]	=	"false";
                $this->return["message"]	=	"Sorry but the email could not be sent. Please try again!";
            }
            $controller->Update($study);
            
            return JSON::Encode($this->return);
    }
    
    function mail_send_using_php_mailer()
    {
	error_reporting(E_STRICT);
	
	date_default_timezone_set('America/Toronto');
	
	require_once('../../phpmailer/class.phpmailer.php');
	//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
	
	$mail             = new PHPMailer();
	
	$body             = file_get_contents('contents.html');
	$body             = preg_replace('/[\\\]/','',$body);
	
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host       = "mail.yourdomain.com"; // SMTP server
	$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
	                                           // 1 = errors and messages
	                                           // 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "smtpout.secureserver.net";      // sets GMAIL as the SMTP server
	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
	$mail->Username      = "report@mdidemos.com"; // SMTP account username
	$mail->Password      = "BeProAct2020$";        // SMTP account password
	
	$mail->SetFrom('report@mdidemos.com', 'CAlDEVON Support');
	$mail->AddReplyTo('report@mdidemos.com', 'CALDEVON Support');
	
	$mail->Subject    = "PHPMailer Test Subject via smtp (Gmail), basic";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	
	$mail->MsgHTML($body);
	
	$address = "designtop1888@gmail.com";
	$mail->AddAddress($address, "John Doe");
	
	$mail->AddAttachment("images/phpmailer.gif");      // attachment
	$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
	
	if(!$mail->Send()) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	  echo "Message sent!";
	}
	
    }
    
    function mail_attachment($filePath, $mailto, $from_mail, $from_name, $replyto, $subject, $message)
    {
        if (is_array($filePath))
        {
            return $this->mailSeveralAttachments($filePath, $message, $from_mail, $mailto, $subject);
        }
        
        $header = "From: ".$from_name." <".$from_mail.">\r\n";
        $header .= "Reply-To: ".$replyto."\r\n";
        
        $file = $filePath;
        $filename = basename($filePath);
        if (strlen($filename) > 0)
        {
            $file_size = filesize($file);
            $handle = fopen($file, "r");
            $content = fread($handle, $file_size);
            fclose($handle);
            $content = chunk_split(base64_encode($content));
            $uid = md5(uniqid(time()));
            $name = basename($file);
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
            $header .= "This is a multi-part message in MIME format.\r\n";
            $header .= "--".$uid."\r\n";
            $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
            $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $header .= $message."\r\n\r\n";
            $header .= "--".$uid."\r\n";
            $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
            $header .= "Content-Transfer-Encoding: base64\r\n";
            $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
            $header .= $content."\r\n\r\n";
            $header .= "--".$uid."--";
            
            echo 'mailto = '.$mailto.'   subject ='.$subject;
            
            if (mail($mailto, $subject, "test message")) //, $header))
            {
                return true;
            }
            else
            {
                { echo __LINE__.' : '; print_r(error_get_last()); }
                return false;
            }
        }
        else
        {
            if (mail($mailto, $subject, $message, $header))
            {
                return true;
            }
            else
            {
                { echo __LINE__.' : '; print_r(error_get_last()); }
                return false;
            }
        }
    }
    
    public function mailSeveralAttachments($filesArray, $mailMessage, $mailFrom, $mailTo, $mailSubject)
    {
        $from = "<".stripslashes($mailFrom).">";
        $semi_rand = md5(time());
        $mime_boundary = "Multipart_Boundary_x{$semi_rand}x";
        
        $message = '';
        $message .= "--$mime_boundary\n".
            "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\r\n" .
            $mailMessage."\n\n".
            "--$mime_boundary";
            
            $headers = "From: $mailFrom\r\n" . "MIME-Version: 1.0\r\n" . "Content-Type: text/plain; charset=iso-8859-1\nContent-Transfer-Encoding: 8bit";
            
            
            $i = 0;
            foreach ($filesArray as $currentFilePath)
            {
                if ($i == 0)
                    $message .= "\n";
                    $i++;
                    $headers = "From: $from\r\n" . "MIME-Version: 1.0\r\n" . "Content-Type: multipart/mixed;\r\n" . " boundary=\"$mime_boundary\"";
                    
                    $fileContent = file_get_contents($currentFilePath);
                    $fileContent = chunk_split(base64_encode($fileContent));
                    
                    $filename = basename($currentFilePath);
                    if (strpos($filename, 'tempHIPPA') !== false)
                    {
                        $filename = 'HippaCover.pdf';
                    }
                    
                    $mt = 'application/octet-stream';
                    //			if ()
                    $mt = 'application/pdf';
                    $message .= "Content-Type: ".$mt."; name=\"".$filename."\"\n" .
                        "Content-Disposition: attachment; filename=\"".$filename."\"\n" .
                        "Content-Transfer-Encoding: base64\n\n" . $fileContent;
                    if ($i == count($filesArray))
                        $message .= "--$mime_boundary--\n";
                        else
                            $message .= "--$mime_boundary\n";
            }
            if ($i == 0)
            {
                $message .= "--\n";
            }
            
            if (!$headers)
            {
                $headers = 'From: '.$from;
            }
                //		die($headers);
            if (mail($mailTo, $mailSubject, $message, $headers))
                return true;
                else
                { echo __LINE__.' : '; print_r(error_get_last()); return false; }
    }
}

?>