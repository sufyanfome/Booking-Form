<?php
header('Access-Control-Allow-Origin: *');
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('REDACTED_STRIPE_LIVE_KEY'); // original key removed — rotate immediately

header('Content-Type: application/json');


$name = $_GET['name'];
$address = $_GET['address'];
$postcode = $_GET['postcode'];
$ref_number = $_GET['refnumber'];
$price = $_GET['price'];

$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'payment_intent_data'=>['metadata' => ['name' => $name, 'address' => $address, 'postcode' => $postcode, 'ref-number' => $ref_number]],
  'line_items' => [[
    'price_data' => [
      'currency' => 'gbp',
      'unit_amount' => $_GET['price'],
      'product_data' => [
        'name' => 'FastKlean Payment',
        'images' => ["https://www.fastklean.co.uk/wp-content/uploads/2021/04/fastklean-stripe.jpg"],
      ],
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => 'https://www.fastklean.co.uk/stripe-payment-success/',
  'cancel_url' => 'https://www.fastklean.co.uk/stripe-payment-cancel/',
]);

echo json_encode(['id' => $checkout_session->id]);