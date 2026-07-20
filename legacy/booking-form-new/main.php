<?php
require 'config/constants.php';

$config_files = scandir(__DIR__.'/'.'config');

foreach ($config_files as $filename)
{
    if($filename !== '.' && $filename !== '..') {
   	 include_once __DIR__.'/'.'config/'.$filename;
    }
}

if(isset($_GET['free-quote'])) {
	if(file_exists(__DIR__.'/'.'free-quote.php')) {
		include __DIR__.'/'.'free-quote.php';
	}
} else if(isset($_GET['service'])) {
	$service = strtolower(str_replace(' ', '-', $_GET['service'])).'.php';
	if(file_exists(__DIR__.'/'.'services/'.$service)) {
		include __DIR__.'/'.'services/'.$service;
	}
}

if(isset($_GET['booking-details'])) {
	if(file_exists(__DIR__.'/'.'booking-details.php')) {
		include __DIR__.'/'.'booking-details.php';
	}
}

if(isset($_GET['submit'])) {
    if(isset($_POST['gift-card-code']) && isset($_POST['gift-card-amount'])) {
        deduct_from_gift_card($_POST['gift-card-code'], $_POST['gift-card-amount']);
    }

	if(file_exists(__DIR__.'/'.'submit.php')) {
		include __DIR__.'/'.'submit.php';
	}
}



function get_gift_cards() {
    $gift_cards = json_decode(file_get_contents(GIFT_CARDS_FILE_PATH));
    
    return $gift_cards;
}

function validate_gift_card($code) {
    $gift_cards = get_gift_cards();

    $is_valid = 'false';
    foreach($gift_cards as $card) {
        $card_code = $card->card_number;
        
        if(strtolower($card_code) === strtolower($code)) {
            $is_valid = 'true';
        }
    }
    
    echo $is_valid;
}

function get_gift_card_amount($code) {
    $gift_cards = get_gift_cards();
    
    $amount = 'false';

    foreach($gift_cards as $card) {
        $card_code = $card->card_number;
        
        if(strtolower($card_code) === strtolower($code)) {
            $amount = $card->amount;
        }
    }
    
    echo $amount;
}

function deduct_from_gift_card($code, $amount = 0) {
    $gift_cards = get_gift_cards();

    foreach($gift_cards as $k => $card) {
        $card_code = $card->card_number;

        if(strtolower($card_code) === strtolower($code)) {
            $card_amount = $card->amount;
            
            if($amount <= $card_amount) {
                $amount_after_deduction = floatval($card_amount) - floatval($amount);
            } else {
                $amount_after_deduction = 0;
            }
            
            $gift_cards[$k]->amount = $amount_after_deduction;
            
            echo 'true';
        }
    }
    
    file_put_contents(GIFT_CARDS_FILE_PATH, json_encode($gift_cards));
}

//Gift cards
if(file_exists(GIFT_CARDS_FILE_PATH)) {
    if(isset($_GET['validate-gift-card'])) {
        return validate_gift_card($_GET['validate-gift-card']);
    } else if(isset($_GET['get-gift-card-amount'])) {
        return get_gift_card_amount($_GET['get-gift-card-amount']);
    }
}



