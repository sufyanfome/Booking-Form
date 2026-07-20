<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/smtp.php';


$name = $_REQUEST['preform-name'];
$email = $_REQUEST['preform-email'];
$postcode = $_REQUEST['preform-postcode'];
$phone = $_REQUEST['preform-phone'];
$service = $_REQUEST['service'];
$msg = $_REQUEST['preform-message'];

$message = '<b>Free Quote</b><br />';
$message .= 'Name: '. $name.'<br/>';
$message .= 'Email: '. $email.'<br/>';
$message .= 'Phone: '. $phone.'<br/>';
$message .=  'Postcode: '.$postcode.'<br/>';
$message .= 'Service: '. $service.'.<br/>';

$message .= 'Message: '.$msg.'.<br/>';
$message .= '--';

$subject = 'Free Quote';
$headers = "Content-Type: text/html; charset=UTF-8\r\n";

$footer = "\r\n <br /><br /> This email was sent from the online booking system on FastKlean (https://fastklean.co.uk)";

$mail = new PHPMailer();

//$mail->SMTPDebug = 2;                   // Enable verbose debug output
$mail->isSMTP();                        // Set mailer to use SMTP
$mail->Host       = 'ssl://rsserver3.hostinginterface.eu';    // Specify main SMTP server
$mail->SMTPAuth   = true;               // Enable SMTP authentication
$mail->Username   = 'booking-form@fastklean.co.uk';     // SMTP username
$mail->Password   = 'C$H9%NE&%&';         // SMTP password
$mail->SMTPSecure = 'ssl';              // Enable TLS encryption, 'ssl' also accepted
$mail->Port       =  465;                // TCP port to connect to
$mail->isHTML(true);

$mail->setFrom('booking-form@fastklean.co.uk', 'FastKlean');

$mail->addAddress('webforms@fastklean.co.uk', 'FastKlean');
$mail->addAddress('webforms1@fastklean.co.uk', ' FastKlean');
$mail->addAddress('bookings@fome.agency', ' FastKlean');
//$mail->addAddress('yousef@fome.agency', ' FastKlean');

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $subject;
$mail->Body = $message.$footer;

if($mail->send()) {
	echo 'true';
} else {
	 echo 'Mailer Error: ' . $mail->ErrorInfo;
	echo 'false';
}