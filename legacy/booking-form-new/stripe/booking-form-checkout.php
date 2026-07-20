<?php
header('Access-Control-Allow-Origin: *');

require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('REDACTED_STRIPE_LIVE_KEY'); // original key removed — rotate immediately

header('Content-Type: application/json');

$name = $_GET['name'];
$address = $_GET['address'];
$postcode = $_GET['postcode'];
$price = $_GET['price'];
$service = $_GET['service'];
$mode = 'payment';

if($service === 'Regular Cleaning' || $service === 'Regular Cleaning Luxury') {
    $recurring_interval = 'week';
    $recurring_interval_count = 1;

    $how_often = $_GET['howoften'];
    $sessions = $_GET['sessions'];

    if(isset($how_often)) {
        if($how_often === 'fortnightly') {
            $recurring_interval_count = 2;
        }
    }
}



if($service === 'Regular Cleaning' || $service === 'Regular Cleaning Luxury') {
  $checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
      'price_data' => [
        'recurring' => ['interval' =>  $recurring_interval, 'interval_count' => $recurring_interval_count],
        'currency' => 'gbp',
        'unit_amount' => $price,
        'product_data' => [
          'name' => 'FastKlean Payment',
          'images' => ["https://www.fastklean.co.uk/wp-content/uploads/2021/04/fastklean-stripe.jpg"],
        ],
      ],
      'quantity' => 1,
    ]],
    'mode' => 'subscription',
    'success_url' => 'https://www.fastklean.co.uk/stripe-payment-success/',
    'cancel_url' => 'https://www.fastklean.co.uk/stripe-payment-cancel/',
  ]);
} else {
    $checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
      'price_data' => [
        'currency' => 'gbp',
        'unit_amount' => $price,
        'product_data' => [
          'name' => 'FastKlean Payment',
          'images' => ["https://www.fastklean.co.uk/wp-content/uploads/2021/04/fastklean-stripe.jpg"],
        ],
      ],
      'quantity' => 1,
    ]],
    'mode' => $mode,
    'success_url' => 'https://www.fastklean.co.uk/stripe-payment-success/',
    'cancel_url' => 'https://www.fastklean.co.uk/stripe-payment-cancel/',
  ]);
}

echo json_encode(['id' => $checkout_session->id]);