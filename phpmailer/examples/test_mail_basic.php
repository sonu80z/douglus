<html>
<head>
<title>PHPMailer - Mail() basic test</title>
</head>
<body>

<?php

require_once('../class.phpmailer.php');

$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true);
$mail->Username      = "invoice@techcarexray.com"; // SMTP account username
$mail->Password      = "money95$";        // SMTP account password
$mail->SetFrom('support@techcarexray.com', 'TechcareXray Support Team'); 
//$address = "khb2708798244@hotmail.com";
$address = "designtop1888@gmail.com";
$mail->AddAddress($address, "John Doe");
$mail->Subject = "Study Document";
$mail->Body = "This is the HTML message body <b>in bold!</b>".date('Y-m-d h:i:s');
 if(!$mail->Send()){
    echo "Mailer Error: " . $mail->ErrorInfo;
}
else{
    echo "Message has been sent";
}
if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>

</body>
</html>
