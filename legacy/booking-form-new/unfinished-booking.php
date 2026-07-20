<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/smtp.php';

$text = implode (',', $_REQUEST);

$client_email = $_GET['email'];
$client_name = $_GET['name'];

if($client_email === 'yousef@fome.agency') {
		
	if(isset($client_email) && strlen($client_email) > 0 &&  isset($client_name) && strlen($client_name)) {
		$mail = new PHPMailer;

		$mail->setFrom('info@fastklean.co.uk', 'FastKlean');
		$mail->addAddress($client_email, $client_name);     // Add a recipient
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = 'Need any help?';

		$body = file_get_contents('unfinished-booking-email.html');
		
		$mail->Body = $body;
		$mail->send();
	}
}
