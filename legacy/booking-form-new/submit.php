<?php 
$service = $_POST['service'];
$email = $_POST['email'];
$name = $_POST['name'];
$phone = $_POST['phone'];
$postcode = $_POST['postcode'];
$address = $_POST['address'];
$date = transform_date($_POST['date']);

$time = $_POST['time'];
$preferred_calling_hours = $_POST['calling-hours'];
$gift_card = $_POST['gift-card-code'];
$gift_card_amount = $_POST['gift-card-amount'];


/* Commercial */
if(isset($_POST['service-type'])) {
	if($_POST['service-type'] === 'commercial') {
		if(isset($service) && strlen($service) > 0) {
			if($service === 'Commercial Cleaning') {
				$service = 'Cleaning';
			}
			$service = 'Commercial '.$service.' - Free quote';
		} else {
			$service = 'Commercial Cleaning - Free quote';
		}
		
		$email = $_POST['preform-email'];
		$name = $_POST['preform-name'];
		$phone = $_POST['preform-phone'];
		$postcode = $_POST['preform-postcode'];
		$note = $_POST['preform-message'];
	}
}

if($_POST['note']) {
	$note = $_POST['note'];
} else {
	$note = '-';
}

$price = $_POST['price'];
if(isset($_POST['original-price'])) {
	$original_price = $_POST['original-price'];
}

$message = "";
$message .= "<h2>$service</h2>";
$message .= "<b>Name:</b> $name <br />";
$message .= "<b>Phone:</b> $phone <br />";
$message .= "<b>Email:</b> $email <br />";
$message .= "<b>Postcode:</b> $postcode <br />";
$message .= "<b>Preferred calling hours:</b> $preferred_calling_hours <br />";

if(isset($address)) {
	$message .= "<b>Address:</b> $address <br />";
}

$message .= "<b>Additional notes:</b> $note <br />";
$message .= "<b>Date:</b> $date <br />";
if(isset($time)) {
	$message .= "<b>Time:</b> $time <br />";
}

$vat_incl = '';

if(VAT === 'true') {
  $vat_incl = '(VAT incl.)';
}

if(isset($original_price) && strlen($original_price) > 0) {

	$message .= "<b>Price:</b> <del>&pound;$original_price</del> &pound;$price  $vat_incl<br />";
} else {
	$message .= "<b>Price:</b> &pound;$price $vat_incl<br />";
}

if(isset($gift_card) && strlen($gift_card) > 0) {
	$message .= "<b>Gift card code:</b> $gift_card <br />";
	$message .= "<b>Gift card amount used:</b> $gift_card_amount <br />";
}

if(isset($_POST['discount-code']) && strlen($_POST['discount-code']) > 0) {
	$message .= "<b>Discount code:</b> ".$_POST['discount-code']." <br />";
}

if(isset($_POST['parking'])) {
	switch(@$_POST['parking-type']) {
		case 'free-parking':
			$parkingType = 'Free parking';
			break;
		case 'parking-permit':
			$parkingType = 'Parking permit';
			break;
		case 'parking-meter':
			$parkingType = 'Parking meter';
			break;
		case 'invoice':
			$parkingType = 'Will cover parking charges upon invoice';
			break;
		case 'cash':
			$parkingType = 'Will cover charges in cash';
			break;
		default:
			$parkingType = 'Not specified';
	}
	
	$message .=  '<b>Parking provided:</b> '.$parkingType.'<br />';
}
	
if(isset($_POST['congestion-charge'])) {
	$message .= '<b>Congestion Charge Zone:</b> Yes<br />';
}

if(isset($_POST['walking-distance'])) {
	if($_POST['walking-distance'] === '5') {
		$walking_distance = 'Near +&pound;5';
	} else if($_POST['walking-distance'] === '10') {
		$walking_distance = 'Far (same area) +&pound;10';
	} else {
		$walking_distance = 'Far (different area) +&pound;20';
	}
	$message .= "<b>Get keys from different address:</b> $walking_distance<br />";
}

$message .= '<br />';


switch($service) {
	case 'Regular Cleaning':
		$message .= getRegularCleaningFormData();
		break;
	case 'Regular Cleaning Luxury':
		$message .= getRegularCleaningFormData();
		break;
	case 'One Off Cleaning':
		$message .= getOneOffFormData();
		break;
     case 'Antiviral Sanitisation':
		$message .= getAntiviralSanitisationFormData();
		break;   
	case 'Carpet Cleaning':
		$message .= getCarpetCleaningFormData();
		break;
	case 'Window Cleaning':
		$message .= getWindowCleaningFormData();
		break;
	case 'Upholstery Cleaning':
		$message .= getUpholsteryCleaningFormData();
		break;
	case 'Mattress Cleaning':
		$message .= getMattressCleaningFormData();
		break;
	case 'End of Tenancy Cleaning':
		$message .= getEndOfTenancyFormData();
		break;
	case 'After Builders Cleaning':
		$message .= getAfterBuildersFormData();
		break;
	case 'Curtain Cleaning':
		$message .= getCurtainCleaningFormData();
		break;
	case 'Oven Cleaning':
		$message .= getOvenCleaningFormData();
		break;
	case 'Hard Floor Cleaning':
		$message .= getHardFloorCleaningFormData();
		break;
	case 'Gardening':
		$message .= getGardeningFormData();
		break;
	case 'Mobile Car Valeting':
		$message .= getCarValetingFormData();
		break;
	case 'Wooden Floor Services':
		$message .= getWoodenFloorFormData();
		break;
	case 'Rubbish Clearance':
		$message .= getRubbishClearanceFormData();
		break;
	case 'Rubbish Removal':
		$message .= getRubbishRemovalFormData();
		break;
}


if(isset($_POST['added-carpet-cleaning'])) {
	$carpetCleaningData = getCarpetCleaningFormData($_POST);
	$message .= "<br /><h2>Carpet cleaning: </h2>$carpetCleaningData";
} 

if(isset($_POST['added-upholstery-cleaning'])) {
	$upholsteryCleaningData = getUpholsteryCleaningFormData($_POST);
	$message .= "<br /><h2>Upholstery cleaning: </h2>$upholsteryCleaningData";
} 

if(isset($_POST['added-mattress-cleaning'])) {
	$mattressCleaningData = getMattressCleaningFormData($_POST);
	$message .= "<br /><h2>Mattress cleaning: </h2>$mattressCleaningData";
} 

if(isset($_POST['added-window-cleaning'])) {
	$windowCleaningData = getWindowCleaningFormData($_POST);
	$message .= "<br /><h2>Window cleaning: </h2>$windowCleaningData";
} 

if(isset($_POST['added-oven-cleaning'])) {
	$ovenCleaningData = getOvenCleaningFormData($_POST);
	$message .= "<br /><h2>Oven cleaning: </h2>$ovenCleaningData";
} 

if(isset($_POST['added-curtain-cleaning'])) {
	$curtainCleaningData = getCurtainCleaningFormData($_POST);
	$message .= "<br /><h2>Curtain cleaning: </h2>$curtainCleaningData";
}

function getRegularCleaningFormData() {
	if(!isset($_POST['day']) || count($_POST['day']) === 7) {
		$day = 'Any';
	} else {
		$day = @implode(', ', $_POST['day']);
	}

	$howOften = $_POST['how-often'];
	
	if($howOften === 'frequently') {
		$howOften = $_POST['sessions-per-week'].' sessions per week';
	}
	if(isset($_POST['have-pets'])) {
		$pets = $_POST['pets'];
	} else {
		$pets = 'None';
	}
	
	$result = "";
	
	if(isset($_POST['cleaning-hours']) && (int)$_POST['cleaning-hours'] > 0) {
		$result .= "<b>Cleaning hours:</b> ".$_POST['cleaning-hours']."<br />";
	}
	
	if(isset($_POST['additional-fridge'])) {
		$result .= "<b>Cleaning inside fridge:</b> Yes<br />";
	}
	
	if(isset($_POST['additional-laundry'])) {
		$result .= "<b>Do laundry:</b> Yes<br />";
	}
	
	if(isset($_POST['additional-oven'])) {
		$result .= "<b>Inside oven:</b> Yes<br />";
	}
	
	if(isset($_POST['additional-windows'])) {
		$result .= "<b>Inside windows:</b> Yes<br />";
	}
	
	if(isset($_POST['additional-ironing'])) {
		$result .= "<b>Ironing:</b> Yes<br />";
	}
	
	$result .= "<br /><b>How often:</b> $howOften<br />";

	if(strlen($pets) > 0) {
		$result .= "<b>Pets:</b> $pets<br />";
	}
	
	return $result;
}

function getOneOffFormData() {
	if(isset($_POST['one-off-bedrooms'])) {
		$result .= '<b>Bedrooms: </b>'.$_POST['one-off-bedrooms'].'<br />';
	}
	
	if(isset($_POST['one-off-bathrooms'])) {
		$result .= '<b>Bathrooms: </b>'.$_POST['one-off-bathrooms'].'<br />';
	}
	
	if(isset($_POST['one-off-additional'])) {
		$result .= '<b>Additional rooms: </b>'.$_POST['one-off-additional'].'<br />';
	}

	if(isset($_POST['type-housing'])) {
		$result .= '<b>Housing type: </b>'.$_POST['type-housing'].'<br />';
	}

	if(isset($_POST['one-off-cleaning-hours']) && $_POST['one-off-cleaning-hours'] > 0) {
		$result .= "<b>Cleaning hours:</b> ".$_POST['one-off-cleaning-hours']."<br />";
	}

	if(isset($_POST['one-off-cleaning-hours']) && $_POST['one-off-eco-natural-ingredients-hours'] > 0) {
		$result .= "<b>Eco cleaning with natural ingredients:</b> ".$_POST['one-off-eco-natural-ingredients-hours']." hours<br />";
	}

	if(isset($_POST['one-off-cleaning-hours']) && $_POST['one-off-eco-ionised-water-hours'] > 0) {
		$result .= "<b>Eco cleaning with ionised water:</b> ".$_POST['one-off-eco-ionised-water-hours']." hours<br />";
	}

	if(isset($_POST['one-off-cleaning-hours']) && $_POST['one-off-eco-bravo-products-hours'] > 0) {
		$result .= "<b>Eco cleaning with eco bravo products:</b> ".$_POST['one-off-eco-bravo-products-hours']." hours<br />";
	}

	if(isset($_POST['inside-fridge'])) {
		$result .= '<b>Inside fridge: </b>'.$_POST['inside-fridge'].'<br />';
	}

	if(isset($_POST['inside-freezer'])) {
		$result .= '<b>Inside freezer: </b>'.$_POST['inside-freezer'].'<br />';
	}

	if(isset($_POST['inside-cupboards'])) {
		$result .= '<b>Inside Cupboards: </b>'.$_POST['inside-cupboards'].'<br />';
	}

	if(isset($_POST['wooden-blinds'])) {
		$result .= '<b>Wooden blinds: </b>'.$_POST['wooden-blinds'].'<br />';
	}

	if(isset($_POST['glass-panel'])) {
		$result .= '<b>Glass panels: </b>'.$_POST['glass-panel'].'<br />';
	}
	
	if(isset($_POST['equipment'])) {
		$result .= "<b>Cleaner provides equipment:</b> Yes(+&pound;5/hr)<br />";
	}

	return $result;
}

function getAntiviralSanitisationFormData() {
    $result = '';

    if(@(int)$_POST['antiviral-sanitisation-area'] > 0) {
		$result .= '<b>Area:</b> '.$_POST['antiviral-sanitisation-area'].' sq .m.<br />';
	}

    if(@(int)$_POST['antiviral-sanitisation-computers'] > 0) {
		$result .= '<b>Computers:</b> '.$_POST['antiviral-sanitisation-computers'].'<br />';
	}

    return $result;
}
function getCarpetCleaningFormData() {
	
	$result = '';
	
	if(@(int)$_POST['carpet-steam-stairs'] > 0) {
		$result .= '<b>Stair carpets (steam):</b> '.$_POST['carpet-steam-stairs'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-lounge'] > 0) {
		$result .= '<b>Lounge carpets (steam):</b> '.$_POST['carpet-steam-lounge'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-dining-room'] > 0) {
		$result .= '<b>Dining room carpets (steam):</b> '.$_POST['carpet-steam-dining-room'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-kitchen'] > 0) {
		$result .= '<b>Kitchen carpets (steam):</b> '.$_POST['carpet-steam-kitchen'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-bedroom'] > 0) {
		$result .= '<b>Single bedroom carpets (steam):</b> '.$_POST['carpet-steam-bedroom'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-double-bedroom'] > 0) {
		$result .= '<b>Double bedroom carpets (steam):</b> '.$_POST['carpet-steam-double-bedroom'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-hallway'] > 0) {
		$result .= '<b>Hallway carpets (steam):</b> '.$_POST['carpet-steam-hallway'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-landing'] > 0) {
		$result .= '<b>Landing carpets (steam):</b> '.$_POST['carpet-steam-landing'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-bathroom'] > 0) {
		$result .= '<b>Bathroom carpets (steam):</b> '.$_POST['carpet-steam-bathroom'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-medium-rugs'] > 0) {
		$result .= '<b>Medium rugs (steam):</b> '.$_POST['carpet-steam-medium-rugs'].'<br />';
	}
	if(@(int)$_POST['carpet-steam-large-rugs'] > 0) {
		$result .= '<b>Large rugs (steam):</b> '.$_POST['carpet-steam-large-rugs'].'<br />';
	}
	
	if(@(int)$_POST['carpet-dry-stairs'] > 0) {
		$result .= '<b>Stair carpets (dry cleaning):</b> '.$_POST['carpet-dry-stairs'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-lounge'] > 0) {
		$result .= '<b>Lounge carpets (dry cleaning):</b> '.$_POST['carpet-dry-lounge'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-dining-room'] > 0) {
		$result .= '<b>Dining room carpets (dry cleaning):</b> '.$_POST['carpet-dry-dining-room'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-kitchen'] > 0) {
		$result .= '<b>Kitchen carpets (dry cleaning):</b> '.$_POST['carpet-dry-kitchen'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-bedroom'] > 0) {
		$result .= '<b>Single bedroom carpets (dry cleaning):</b> '.$_POST['carpet-dry-bedroom'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-double-bedroom'] > 0) {
		$result .= '<b>Double bedroom carpets (dry cleaning):</b> '.$_POST['carpet-dry-double-bedroom'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-hallway'] > 0) {
		$result .= '<b>Hallway carpets (dry cleaning):</b> '.$_POST['carpet-dry-hallway'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-landing'] > 0) {
		$result .= '<b>Landing carpets (dry cleaning):</b> '.$_POST['carpet-dry-landing'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-bathroom'] > 0) {
		$result .= '<b>Bathroom carpets (dry cleaning):</b> '.$_POST['carpet-dry-bathroom'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-medium-rugs'] > 0) {
		$result .= '<b>Medium rugs (dry cleaning):</b> '.$_POST['carpet-dry-medium-rugs'].'<br />';
	}
	if(@(int)$_POST['carpet-dry-large-rugs'] > 0) {
		$result .= '<b>Large rugs (dry cleaning):</b> '.$_POST['carpet-dry-large-rugs'].'<br />';
	}
	
	if(isset($_POST['carpet-scotchgard'])) {
		$result .= '<br /><b>Use Scotchgard:</b> Yes<br />';
	} else {
		$result .= '<br /><b>Use Scotchgard:</b> No<br />';
	}
	
	return $result;
}


function getMattressCleaningFormData() {
	
	$result = '';
	
	if(@(int)$_POST['mattress-steam-babycot'] > 0) {
		$result .= '<b>Babycots (steam):</b> '.$_POST['mattress-steam-babycot'].'<br />';
	}
	if(@(int)$_POST['mattress-steam-single'] > 0) {
		$result .= '<b>Single mattresses (steam):</b> '.$_POST['mattress-steam-single'].'<br />';
	}
	if(@(int)$_POST['mattress-steam-double'] > 0) {
		$result .= '<b>Double mattresses (steam):</b> '.$_POST['mattress-steam-double'].'<br />';
	}
	if(@(int)$_POST['mattress-steam-queen-size'] > 0) {
		$result .= '<b>Queen size mattresses (steam):</b> '.$_POST['mattress-steam-queen-size'].'<br />';
	}
	if(@(int)$_POST['mattress-steam-king-size'] > 0) {
		$result .= '<b>King size mattresses (steam):</b> '.$_POST['mattress-steam-king-size'].'<br />';
	}
	
	if(@(int)$_POST['mattress-dry-babycot'] > 0) {
		$result .= '<b>Babycots (dry cleaning):</b> '.$_POST['mattress-dry-babycot'].'<br />';
	}
	if(@(int)$_POST['mattress-dry-single'] > 0) {
		$result .= '<b>Single dryes (dry cleaning):</b> '.$_POST['mattress-dry-single'].'<br />';
	}
	if(@(int)$_POST['mattress-dry-double'] > 0) {
		$result .= '<b>Double dryes (dry cleaning):</b> '.$_POST['mattress-dry-double'].'<br />';
	}
	if(@(int)$_POST['mattress-dry-queen-size'] > 0) {
		$result .= '<b>Queen size dryes (dry cleaning):</b> '.$_POST['mattress-dry-queen-size'].'<br />';
	}
	if(@(int)$_POST['mattress-dry-king-size'] > 0) {
		$result .= '<b>King size dryes (dry cleaning):</b> '.$_POST['mattress-dry-king-size'].'<br />';
	}
	
	return $result;
}


function getUpholsteryCleaningFormData() {
	
	$result = '';

	if(@(int)$_POST['upholstery-steam-two-seater'] > 0) {
		$result .= '<b>2 seaters - fabric (steam):</b> '.$_POST['upholstery-steam-two-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-three-seater'] > 0) {
		$result .= '<b>3 seaters - fabric (steam):</b> '.$_POST['upholstery-steam-three-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-five-seater'] > 0) {
		$result .= '<b>5 seaters - fabric (steam):</b> '.$_POST['upholstery-steam-five-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-armchair'] > 0) {
		$result .= '<b>Armchairs - fabric (steam):</b> '.$_POST['upholstery-steam-armchair'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-headboard'] > 0) {
		$result .= '<b>Headboard (steam):</b> '.$_POST['upholstery-steam-headboard'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-dining-chair'] > 0) {
		$result .= '<b>Dining chairs (steam):</b> '.$_POST['upholstery-steam-dining-chair'].'<br />';
	}
	if(@(int)$_POST['upholstery-l-shaped-two-seater'] > 0) {
		$result .= '<b>L-shaped two seater (steam):</b> '.$_POST['upholstery-l-shaped-two-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-l-shaped-three-seater'] > 0) {
		$result .= '<b>L-shaped three seater (steam):</b> '.$_POST['upholstery-l-shaped-three-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-l-shaped-four-seater'] > 0) {
		$result .= '<b>L-shaped four seater (steam):</b> '.$_POST['upholstery-l-shaped-four-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-l-shaped-five-seater'] > 0) {
		$result .= '<b>L-shaped five seater (steam):</b> '.$_POST['upholstery-l-shaped-five-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-two-seater-leather'] > 0) {
		$result .= '<b>2 seaters - leather (steam):</b> '.$_POST['upholstery-steam-two-seater-leather'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-three-seater-leather'] > 0) {
		$result .= '<b>3 seaters - leather (steam):</b> '.$_POST['upholstery-steam-three-seater-leather'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-five-seater-leather'] > 0) {
		$result .= '<b>5 seaters - leather (steam):</b> '.$_POST['upholstery-steam-five-seater-leather'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-armchair-leather'] > 0) {
		$result .= '<b>Armchairs - leather (steam):</b> '.$_POST['upholstery-steam-armchair-leather'].'<br />';
	}
	if(@(int)$_POST['upholstery-steam-headboard-leather'] > 0) {
		$result .= '<b>Headboard - leather (steam):</b> '.$_POST['upholstery-steam-headboard-leather'].'<br />';
	}
	if(@(int)$_POST['upholstery-dry-two-seater'] > 0) {
		$result .= '<b>2 seaters (dry cleaning):</b> '.$_POST['upholstery-dry-two-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-dry-three-seater'] > 0) {
		$result .= '<b>3 seaters (dry cleaning):</b> '.$_POST['upholstery-dry-three-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-dry-five-seater'] > 0) {
		$result .= '<b>5 seaters (dry cleaning):</b> '.$_POST['upholstery-dry-five-seater'].'<br />';
	}
	if(@(int)$_POST['upholstery-dry-armchair'] > 0) {
		$result .= '<b>Armchairs (dry cleaning):</b> '.$_POST['upholstery-dry-armchair'].'<br />';
	}
	if(@(int)$_POST['upholstery-dry-dining-chair'] > 0) {
		$result .= '<b>Dining chair (dry cleaning):</b> '.$_POST['upholstery-dry-dining-chair'].'<br />';
	}
	
	if(isset($_POST['upholstery-scotchgard'])) {
		$result .= '<br /><b>Use Scotchgard:</b> Yes<br />';
	} else {
		$result .= '<br /><b>Use Scotchgard:</b> No<br />';
	}
	return $result;
}



function getWindowCleaningFormData() {
	
	$result = '';
	
	if(@(int)$_POST['window-small'] > 0) {
		$result .= '<b>Small windows:</b> '.$_POST['window-small'].'<br />';
	}
	if(@(int)$_POST['window-medium'] > 0) {
		$result .= '<b>Medium windows:</b> '.$_POST['window-medium'].'<br />';
	}
	if(@(int)$_POST['window-large'] > 0) {
		$result .= '<b>Large windows:</b> '.$_POST['window-large'].'<br />';
	}
	if(@(int)$_POST['window-bay'] > 0) {
		$result .= '<b>Bay windows:</b> '.$_POST['window-bay'].'<br />';
	}
	if(@(int)$_POST['window-french-door'] > 0) {
		$result .= '<b>French doors:</b> '.$_POST['window-french-door'].'<br />';
	}
	
	if(isset($_POST['window-internal-cleaning'])) {
		$result .= '<br /><b>Internal cleaning:</b> Yes<br />';
	} else {
		$result .= '<br /><b>Internal cleaning:</b> No<br />';
	}
	
	if(isset($_POST['electricity'])) {
		$outletLocation = '';
		if($_POST['outlet-location'] === 'inside') {
			$outletLocation = 'inside';
		} else if ($_POST['outlet-location'] === 'outside') {
			$outletLocation = 'outside';
		}
		$result .= '<b>Can provide electricity:</b> Yes';
		if($outletLocation) {
			$result .= " ($outletLocation outlet)";
		}
	} else {
		$result .= '<b>Can provide electricity:</b> No';
	}
	
	$result .= '<br />';
	
	if(isset($_POST['water'])) {
		$tapLocation = '';
		if($_POST['tap-location'] === 'inside') {
			$tapLocation = 'inside';
		} else if ($_POST['tap-location'] === 'outside') {
			$tapLocation = 'outside';
		}
		$result .= '<b>Can provide water:</b> Yes';
		if($tapLocation) {
			$result .= " ($tapLocation tap)";
		}
	} else {
		$result .= '<b>Can provide water:</b> No';
	}
	$result .= '<br />';

	return $result;
}


function getCurtainCleaningFormData() {
	
	$result = '';
	
	if(@(int)$_POST['curtain-steam-short'] > 0) {
		$result .= '<b>Short curtains (steam):</b> '.$_POST['curtain-steam-short'].'<br />';
	}
	if(@(int)$_POST['curtain-steam-full-length'] > 0) {
		$result .= '<b>Full length curtains (steam):</b> '.$_POST['curtain-steam-full-length'].'<br />';
	}
	
	if(@(int)$_POST['curtain-dry-short'] > 0) {
		$result .= '<b>Short curtains (dry cleaning):</b> '.$_POST['curtain-dry-short'].'<br />';
	}
	if(@(int)$_POST['curtain-dry-full-length'] > 0) {
		$result .= '<b>Full length curtains (dry cleaning):</b> '.$_POST['curtain-dry-full-length'].'<br />';
	}
	
	return $result;
}


function getOvenCleaningFormData() {
	
	$result = '';

	if(@(int)$_POST['oven-gas-hobs'] > 0) {
		$result .= '<b>Gas hobs:</b> '.$_POST['oven-gas-hobs'].'<br />';
	}
	if(@(int)$_POST['oven-gas-hobs-double'] > 0) {
		$result .= '<b>Gas hobs (double):</b> '.$_POST['oven-gas-hobs-double'].'<br />';
	}
	if(@(int)$_POST['oven-ceramic-hobs'] > 0) {
		$result .= '<b>Ceramic hobs:</b> '.$_POST['oven-ceramic-hobs'].'<br />';
	}
	if(@(int)$_POST['oven-microwave'] > 0) {
		$result .= '<b>Microwave ovens:</b> '.$_POST['oven-microwave'].'<br />';
	}
	if(@(int)$_POST['oven-single'] > 0) {
		$result .= '<b>Single ovens:</b> '.$_POST['oven-single'].'<br />';
	}
	if(@(int)$_POST['oven-double'] > 0) {
		$result .= '<b>Double ovens:</b> '.$_POST['oven-double'].'<br />';
	}
	if(@(int)$_POST['oven-extractor'] > 0) {
		$result .= '<b>Extractors:</b> '.$_POST['oven-extractor'].'<br />';
	}
	if(@(int)$_POST['oven-extractor-double'] > 0) {
		$result .= '<b>Extractors (double):</b> '.$_POST['oven-extractor-double'].'<br />';
	}
	
	if(@(int)$_POST['oven-range'] > 0) {
		$result .= '<b>Range ovens:</b> '.$_POST['oven-range'].'<br />';
	}
	if(@(int)$_POST['oven-range-hood'] > 0) {
		$result .= '<b>Range hoods or hobs:</b> '.$_POST['oven-range-hood'].'<br />';
	}
	if(@(int)$_POST['oven-range-gas-hob'] > 0) {
		$result .= '<b>Range gas hobs:</b> '.$_POST['oven-range-gas-hob'].'<br />';
	}
	if(@(int)$_POST['oven-range-electric-hob'] > 0) {
		$result .= '<b>Range electric hobs:</b> '.$_POST['oven-range-electric-hob'].'<br />';
	}
	if(@(int)$_POST['oven-range-ceramic-hob'] > 0) {
		$result .= '<b>Range ceramic hobs:</b> '.$_POST['oven-range-ceramic-hob'].'<br />';
	}
	if(@(int)$_POST['oven-range-extractor'] > 0) {
		$result .= '<b>Range extractors:</b> '.$_POST['oven-range-extractor'].'<br />';
	}
	if(@(int)$_POST['oven-range-wide'] > 0) {
		$result .= '<b>Single wide ovens:</b> '.$_POST['oven-range-wide'].'<br />';
	}
	if(@(int)$_POST['oven-range-half-size'] > 0) {
		$result .= '<b>Range 1/2 size ovens:</b> '.$_POST['oven-range-half-size'].'<br />';
	}
	if(@(int)$_POST['oven-range-90'] > 0) {
		$result .= '<b>Master range - 90cm:</b> '.$_POST['oven-range-90'].'<br />';
	}
	if(@(int)$_POST['oven-range-100'] > 0) {
		$result .= '<b>Master range - 100cm:</b> '.$_POST['oven-range-100'].'<br />';
	}
	if(@(int)$_POST['oven-warming-drawer'] > 0) {
		$result .= '<b>Warming drawer doors:</b> '.$_POST['oven-warming-drawer'].'<br />';
	}
	
	if(@(int)$_POST['oven-two-size'] > 0) {
		$result .= '<b>Double oven size:</b> '.$_POST['oven-two-size'].'<br />';
	}
	if(@(int)$_POST['oven-four-size'] > 0) {
		$result .= '<b>Four oven size:</b> '.$_POST['oven-four-size'].'<br />';
	}
	if(@(int)$_POST['oven-side-module'] > 0) {
		$result .= '<b>Side modules:</b> '.$_POST['oven-side-module'].'<br />';
	}
	
	if(@(int)$_POST['oven-small-bbq'] > 0) {
		$result .= '<b>Small barbecues:</b> '.$_POST['oven-small-bbq'].'<br />';
	}
	if(@(int)$_POST['oven-large-bbq'] > 0) {
		$result .= '<b>Large barbecues:</b> '.$_POST['oven-large-bbq'].'<br />';
	}

	if(@(int)$_POST['oven-combo-deal-1'] > 0) {
		$result .= '<b>Single Oven (2 racks) + Hob + Extractor (combo deal):</b> '.$_POST['oven-combo-deal-1'].'<br />';
	}

	if(@(int)$_POST['oven-combo-deal-2'] > 0) {
		$result .= '<b>Double Oven (3 racks) + Hob + Extractor (combo deal):</b> '.$_POST['oven-combo-deal-2'].'<br />';
	}

	if(@(int)$_POST['oven-combo-deal-3'] > 0) {
		$result .= '<b>Master Range - 90cm + hob and extractor (combo deal):</b> '.$_POST['oven-combo-deal-3'].'<br />';
	}

	if(@(int)$_POST['oven-combo-deal-4'] > 0) {
		$result .= '<b>Master Range - 100cm + hob and extractor (combo deal):</b> '.$_POST['oven-combo-deal-4'].'<br />';
	}
	
	return $result;
}


function getRubbishRemovalFormData() {
	$result = '';
	
	if(@(int)$_POST['rubbish-cubic-yards'] > 0) {
		$result .= '<b>Rubbish cubic yards:</b> '.$_POST['rubbish-cubic-yards'].'<br />';
	}
	if(@(int)$_POST['rubbish-weight'] > 0) {
		$result .= '<b>Rubbish weight:</b> '.$_POST['rubbish-weight'].'<br />';
	}
	
	return $result;
}


function getAfterBuildersFormData(){
	$result = '';

	if($_POST['after-builders-cleaning-hours']) {
		$result .= '<b>Cleaning hours: </b>'.$_POST['after-builders-cleaning-hours'].'<br />';	
	}
	
	if($_POST['after-building-equipment-cleaning-hours']) {
		$result .= '<b>Cleaning + Equipment: </b>'.$_POST['after-building-equipment-cleaning-hours'].' hours<br />';	
	}

	if(isset($_POST['inside-fridge'])) {
		$result .= '<b>Inside fridge: </b>'.$_POST['inside-fridge'].'<br />';
	}

	if(isset($_POST['inside-freezer'])) {
		$result .= '<b>Inside freezer: </b>'.$_POST['inside-freezer'].'<br />';
	}

	if(isset($_POST['inside-cupboards'])) {
		$result .= '<b>Inside Cupboards: </b>'.$_POST['inside-cupboards'].'<br />';
	}

	if(isset($_POST['wooden-blinds'])) {
		$result .= '<b>Wooden blinds: </b>'.$_POST['wooden-blinds'].'<br />';
	}

	if(isset($_POST['glass-panel'])) {
		$result .= '<b>Glass panels: </b>'.$_POST['glass-panel'].'<br />';
	}

	return $result;
}


function getEndOfTenancyFormData(){
	$result = '';

	if(isset($_POST['property-type'])) {
		$result .= '<b>Property type: </b>'.$_POST['property-type'].'<br />';
	}
	
	if(isset($_POST['end-of-tenancy-bedrooms'])) {
		$result .= '<b>Bedrooms: </b>'.$_POST['end-of-tenancy-bedrooms'].'<br />';
	}
	
	if(isset($_POST['end-of-tenancy-bathrooms'])) {
		$result .= '<b>Bathrooms: </b>'.$_POST['end-of-tenancy-bathrooms'].'<br />';
	}
	
	if(isset($_POST['end-of-tenancy-additional'])) {
		$result .= '<b>Additional rooms: </b>'.$_POST['end-of-tenancy-additional'].'<br />';
	}
	
	
	if(isset($_POST['collect-keys'])) {
		if($_POST['keys-distance'] === 'near') {
			$result .= '<b>Collect keys from a near address (+10&pound;)</b><br />';
		} else if($_POST['keys-distance'] === 'far') {
			$result .= '<b>Collect keys from a far address (+20&pound;)</b><br />';
		} else {
			$result .= '<b>Collect keys from a different address</b><br />';
		}
	}
	
	if($_POST['inventory-check-date']) {
		$result .= '<b>Inventory check: </b>'.$_POST['inventory-check-date'];
		if($_POST['inventory-check-time']) {
			$result .= ' in the '.strtolower($_POST['inventory-check-time']);
		}
		$result .= '<br />';
	}
	
	return $result;
}


function getHardFloorCleaningFormData(){ 
	$result = '';
	
	$floorType = '<b>Floor type: </b>';
	$floorEdges = '<b>Floor surface: </b>';
	$floorSize = '<b>Floor size: </b>';
	
	if(isset($_POST['floor-type'])) {
		switch($_POST['floor-type']) {
			case 'wooden':
				$floorType .= 'Wooden';
				break;
			case 'porous':
				$floorType .= 'Porous';
				break;
			case 'semi-porous':
				$floorType .= 'Semi-porous';
				break;
			case 'non-porous':
				$floorType .= 'Non-porous';
				break;
			default:
				$floorType .= '';
		}
	}
	
	if(isset($_POST['floor-edges'])) {
		switch($_POST['floor-edges']) {
			case 'flat':
				$floorEdges .= 'Flat surface';
				break;
			case 'build-up':
				$floorEdges .= 'Build up';
				break;
			case 'polish':
				$floorEdges .= 'Non-removable polish/seal';
				break;
			case 'loose':
				$floorEdges .= 'Loose';
				break;
			case 'sealed':
				$floorEdges .= 'Sealed';
				break;
			case 'curled':
				$floorEdges .= 'Curled';
				break;
			default:
				$floorEdges .= '';
		}
	}
	
	if(isset($_POST['floor-size'])) {
		$floorSize .= $_POST['floor-size'].' sq m';
	}
	
	$result .= $floorSize.'<br />';
	$result .= $floorType.'<br />';
	$result .= $floorEdges.'<br />';
	
	return $result;
}


function getWoodenFloorFormData() {
	
	$result = '';

	if(strlen($_POST['floor-service-1']) > 0) {
		$result .= '<b>Sanding only: </b> '.$_POST['floor-service-1'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-2']) > 0) {
		$result .= '<b>Sanding and 3 coats of clear lacquer: </b> '.$_POST['floor-service-2'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-3']) > 0) {
		$result .= '<b>Extra coat of lacquer, hardwax, oil or stain: </b> '.$_POST['floor-service-3'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-4']) > 0) {
		$result .= '<b>Upgrade to HP Commercial lacquer: </b> '.$_POST['floor-service-4'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-5']) > 0) {
		$result .= '<b>Sanding and 2 coats of hardwax oil: </b> '.$_POST['floor-service-5'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-6']) > 0) {
		$result .= '<b>Sanding and 3 coats of wood floor oil: </b> '.$_POST['floor-service-6'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-7']) > 0) {
		$result .= '<b>Staining/colouring: </b> '.$_POST['floor-service-7'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-8']) > 0) {
		$result .= '<b>Lime washing/liming/whitening: </b> '.$_POST['floor-service-8'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-9']) > 0) {
		$result .= '<b>Gap filling sawdust+resin (up to 3mm): </b> '.$_POST['floor-service-9'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-10']) > 0) {
		$result .= '<b>Gap filling reclaimed wooden slivers (wider than 3mm): </b> '.$_POST['floor-service-10'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-11']) > 0) {
		$result .= '<b>Gap filling flexible gap master (wider than 3mm, up to 9mm): </b> '.$_POST['floor-service-11'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-12']) > 0) {
		$result .= '<b>Reclaimed pine floorboards changed: </b> '.$_POST['floor-service-12'].' m<br />';
	}
	
	if(strlen($_POST['floor-service-13']) > 0) {
		$result .= '<b>Stairs: sand and seal: </b> '.$_POST['floor-service-13'].' steps<br />';
	}
	
	if(strlen($_POST['floor-service-14']) > 0) {
		$result .= '<b>Carpet removal: </b> '.$_POST['floor-service-14'].' rooms<br />';
	}
	
	if(strlen($_POST['floor-service-15']) > 0) {
		$result .= '<b>Solid wood flooring installation: </b> '.$_POST['floor-service-15'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-16']) > 0) {
		$result .= '<b>Parquet/strip floor installation: </b> '.$_POST['floor-service-16'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-17']) > 0) {
		$result .= '<b>Skirting fitting (does not include supplying the skirting): </b> '.$_POST['floor-service-17'].' m<br />';
	}
	
	if(strlen($_POST['floor-service-18']) > 0) {
		$result .= '<b>Engineered wood floor installation: </b> '.$_POST['floor-service-18'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-19']) > 0) {
		$result .= '<b>Engineered wood door trims: </b> '.$_POST['floor-service-19'].' doorways<br />';
	}
	
	if(strlen($_POST['floor-service-20']) > 0) {
		$result .= '<b>Skirting fitting (does not include supply): </b> '.$_POST['floor-service-20'].' m<br />';
	}
	
	if(strlen($_POST['floor-service-21']) > 0) {
		$result .= '<b>Staircase fitting per step: </b> '.$_POST['floor-service-21'].' steps<br />';
	}
	
	if(strlen($_POST['floor-service-22']) > 0) {
		$result .= '<b>Laminate floor installation: </b> '.$_POST['floor-service-22'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-23']) > 0) {
		$result .= '<b>Laminate wood door trims: </b> '.$_POST['floor-service-23'].' doorways<br />';
	}
	
	if(strlen($_POST['floor-service-24']) > 0) {
		$result .= '<b>Beading fitting (does not include supply): </b> '.$_POST['floor-service-24'].' m<br />';
	}
	
	if(strlen($_POST['floor-service-25']) > 0) {
		$result .= '<b>Old laminate removal: </b> '.$_POST['floor-service-25'].' sq m<br />';
	}
	
	if(strlen($_POST['floor-service-26']) > 0) {
		$result .= '<b>Wooden kitchen countertop sanding/polishing/oiling: </b> '.$_POST['floor-service-26'].' m<br />';
	}
	
	if(strlen($_POST['floor-service-27']) > 0) {
		$result .= '<b>Additional labour (restoration, repairs, furniture movement, etc.): </b> '.$_POST['floor-service-27'].' m<br />';
	}
	return $result;
}


function getGardeningFormData() {
	
	$result = '';
	
	$result .= '<b>Gardening hours: </b> '.$_POST['gardening-hours'].'<br />';
	
	if(@(int)$_POST['small-bags'] > 0) {
		$result .= '<b>Rubbish removal - small bags:</b> '.$_POST['small-bags'].'<br />';
	}
	if(@(int)$_POST['jumbo-bags'] > 0) {
		$result .= '<b>Rubbish removal -  jumbo bags:</b> '.$_POST['jumbo-bags'].'<br />';
	}
	
	if(isset($_POST['electricity'])) {
		$outletLocation = '';
		if($_POST['outlet-location'] === 'inside') {
			$outletLocation = 'inside';
		} else if ($_POST['outlet-location'] === 'outside') {
			$outletLocation = 'outside';
		}
		$result .= '<b>Can provide electricity:</b> Yes';
		if($outletLocation) {
			$result .= " ($outletLocation outlet)";
		}
	} else {
		$result .= '<b>Can provide electricity:</b> No';
	}
	
	$result .= '<br />';
	
	if(isset($_POST['water'])) {
		$tapLocation = '';
		if($_POST['tap-location'] === 'inside') {
			$tapLocation = 'inside';
		} else if ($_POST['tap-location'] === 'outside') {
			$tapLocation = 'outside';
		}
		$result .= '<b>Can provide water:</b> Yes';
		if($tapLocation) {
			$result .= " ($tapLocation tap)";
		}
	} else {
		$result .= '<b>Can provide water:</b> No';
	}
	$result .= '<br />';
	
	return $result;
}

function getCarValetingFormData() {
	$result = '';
	
	$counter = 1;
	
	while(isset($_POST['vehicle-type-'.$counter])) {
			
		switch($_POST['vehicle-type-'.$counter]) {
			case 'city':
				$vehicleType = 'City/sports/convertible car';
				break;
			case 'space-cruiser':
				$vehicleType = 'Space cruiser';
				break;
			case '4x4':
				$vehicleType = '4x4';
				break;
			case 'van':
				$vehicleType = 'Van, truck';
				break;
			default:
				$vehicleType = '';
		}
		
		switch($_POST['service-type-'.$counter]) {
			case 'internal':
				$serviceType = 'Internal';
				break;
			case 'external':
				$serviceType = 'External';
				break;
			case 'mini':
				$serviceType = 'Mini';
				break;
			case 'midi':
				$serviceType = 'Midi';
				break;
			case 'full':
				$serviceType = 'Full';
				break;
			default:
				$serviceType = '';
		}
	
		switch($_POST['interior-material-'.$counter]) {
			case 'fabric':
				$interior = 'Fabric';
				break;
			case 'leather':
				$interior = 'Leather';
				break;
			default:
				$interior = '';
		}
		
		$vehicleAmount = $_POST['car-valeting-vehicle-amount-'.$counter];
		
		$result .= "<br />";
		
		$result .= "<b>Vehicle type:</b> $vehicleType<br />";
		$result .= "<b>Service type:</b> $serviceType<br />";
		$result .= "<b>Interior material:</b> $interior<br />";
		$result .= "<b>Vehicle amount:</b> $vehicleAmount<br />";
		
		$counter++;
	}
	
	$result .= '<br />';
	
	if(isset($_POST['electricity'])) {
		$outletLocation = '';
		if($_POST['outlet-location'] === 'inside') {
			$outletLocation = 'inside';
		} else if ($_POST['outlet-location'] === 'outside') {
			$outletLocation = 'outside';
		}
		$result .= '<b>Can provide electricity:</b> Yes';
		if($outletLocation) {
			$result .= " ($outletLocation outlet)";
		}
	} else {
		$result .= '<b>Can provide electricity:</b> No';
	}
	
	$result .= '<br />';
	
	if(isset($_POST['water'])) {
		$tapLocation = '';
		if($_POST['tap-location'] === 'inside') {
			$tapLocation = 'inside';
		} else if ($_POST['tap-location'] === 'outside') {
			$tapLocation = 'outside';
		}
		$result .= '<b>Can provide water:</b> Yes';
		if($tapLocation) {
			$result .= " ($tapLocation tap)";
		}
	} else {
		$result .= '<b>Can provide water:</b> No';
	}
	$result .= '<br />';
	
	return $result;
}


function transform_date($date) {
    $parts = explode('/', $date);
    $day = $parts[1];
    $month = $parts[0];
    $year = $parts[2];
    
    return $day . '/' . $month . '/' . $year;
}

require 'form-send.php';