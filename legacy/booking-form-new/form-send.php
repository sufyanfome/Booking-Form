<?php
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/smtp.php';


$current_time = date('d-m-Y_h-i-s', time());
file_put_contents('orders/'.$current_time, $message);

$subject = $service. ' Booking';
$footer = "\r\n <br /><br /> This email was sent from the online booking system on FastKlean (https://fastklean.co.uk)";

$mail = new PHPMailer();

//$mail->SMTPDebug = 2;                   // Enable verbose debug output
$mail->isSMTP();                        // Set mailer to use SMTP
$mail->Host       = 'ssl://ssdrs5.hostinginterface.eu';  
$mail->SMTPAuth   = true;               // Enable SMTP authentication
$mail->Username   = 'booking-form@fastklean.co.uk';     // SMTP username
$mail->Password   = 'C$H9%NE&%&';         // SMTP password
$mail->SMTPSecure = 'ssl';              // Enable TLS encryption, 'ssl' also accepted
$mail->Port       =  465;                // TCP port to connect to
$mail->isHTML(true);

$mail->setFrom('booking-form@fastklean.co.uk', 'FastKlean');
$mail->addAddress('webforms@fastklean.co.uk', 'FastKlean');
$mail->addAddress('webforms1@fastklean.co.uk', 'FastKlean');
$mail->addAddress('bookings@emails.fome.agency', 'FastKlean');
//$mail->addAddress('yousef@fome.agency', 'FastKlean');
$mail->Subject = $subject;
$mail->Body = $message.$footer;

foreach($_FILES as $f) {
	$mail->AddAttachment( $f['tmp_name'] , $f['name'] );
}

$howOften = $_POST['how-often'];
	
if($howOften === 'frequently') {
	$howOften = $_POST['sessions-per-week'].' sessions per week';
}

if(!$mail->send()) {
  echo 'An error has occurred.';
} else {
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
<script src="https://js.stripe.com/v3/"></script>

    <script>
     var stripe = Stripe("pk_live_51IbRksFhXlpPq0lEXxBECj1jLGmY3IlcvRg9RehbQyGqsXjz9nMwAZsIl0oN5NgslEREJFDYiRveg1x2OPJE1oCJ00LlPBKE4C");
     var name = '<?=$name?>';
	 var address = '<?=$address?>';
	 var postcode = '<?=$postcode?>';
     var price = '<?=$price?>';
	 var service = '<?=$service?>';
	 var howOften = '<?=$_POST['how-often']?>';
	 var sessionsPerWeek = '<?=$_POST['sessions-per-week']?>';

	 price = Math.ceil(price * 100);
	 
      fetch("https://www.fastklean.co.uk/wp-content/themes/rebound/booking-form-new/stripe/booking-form-checkout.php?price="+price+'&name='+name+'&address='+address+'&postcode='+postcode+'&service='+service+'&howoften='+howOften+'&sessions='+sessionsPerWeek, {
        method: "POST"
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (session) {
          return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(function (result) {
          // If redirectToCheckout fails due to a browser or network
          // error, you should display the localized error message to your
          // customer using error.message.
          if (result.error) {
            alert(result.error.message);
          }
        })
    </script>
<?php
}
?>