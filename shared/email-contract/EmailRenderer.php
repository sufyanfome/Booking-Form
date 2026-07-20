<?php
/**
 * Email Contract Renderer
 *
 * CRITICAL: The output of render_booking_email() must remain byte-for-byte identical
 * to the legacy submit.php output. See CLAUDE.md for the full contract.
 * Any change to this file must be validated against golden files in tests/.
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'FOME_EMAIL_CONTRACT_STANDALONE' ) ) {
	exit;
}

/**
 * Render the booking email body (excluding envelope footer).
 *
 * @param array $b Booking data array — keys mirror POST field names from legacy submit.php.
 * @return string HTML email body (no footer).
 */
function render_booking_email( array $b ): string {

	$service  = $b['service'] ?? '';
	$name     = $b['name'] ?? '';
	$phone    = $b['phone'] ?? '';
	$email    = $b['email'] ?? '';
	$postcode = $b['postcode'] ?? '';
	$calling_hours = $b['calling-hours'] ?? '';
	$address  = $b['address'] ?? '';
	$note     = ( isset( $b['note'] ) && strlen( $b['note'] ) ) ? $b['note'] : '-';
	$date     = $b['date'] ?? '';   // already in dd/mm/yyyy
	$time     = $b['time'] ?? '';
	$price    = $b['price'] ?? '';
	$original_price = $b['original-price'] ?? '';
	$gift_card        = $b['gift-card-code'] ?? '';
	$gift_card_amount = $b['gift-card-amount'] ?? '';
	$discount_code    = $b['discount-code'] ?? '';
	$vat_incl = ( isset( $b['vat_enabled'] ) && $b['vat_enabled'] ) ? '(VAT incl.)' : '';

	$message = '';
	$message .= "<h2>{$service}</h2>";
	$message .= "<b>Name:</b> {$name} <br />";
	$message .= "<b>Phone:</b> {$phone} <br />";
	$message .= "<b>Email:</b> {$email} <br />";
	$message .= "<b>Postcode:</b> {$postcode} <br />";
	$message .= "<b>Preferred calling hours:</b> {$calling_hours} <br />";

	if ( isset( $b['address'] ) && strlen( $address ) ) {
		$message .= "<b>Address:</b> {$address} <br />";
	}

	$message .= "<b>Additional notes:</b> {$note} <br />";
	$message .= "<b>Date:</b> {$date} <br />";

	if ( isset( $b['time'] ) && strlen( $time ) ) {
		$message .= "<b>Time:</b> {$time} <br />";
	}

	if ( isset( $b['original-price'] ) && strlen( $original_price ) > 0 ) {
		$message .= "<b>Price:</b> <del>&pound;{$original_price}</del> &pound;{$price}  {$vat_incl}<br />";
	} else {
		$message .= "<b>Price:</b> &pound;{$price} {$vat_incl}<br />";
	}

	if ( strlen( $gift_card ) > 0 ) {
		$message .= "<b>Gift card code:</b> {$gift_card} <br />";
	}

	if ( strlen( $gift_card_amount ) > 0 ) {
		$message .= "<b>Gift card amount used:</b> {$gift_card_amount} <br />";
	}

	if ( strlen( $discount_code ) > 0 ) {
		$message .= "<b>Discount code:</b> {$discount_code} <br />";
	}

	if ( isset( $b['parking'] ) ) {
		switch ( $b['parking'] ) {
			case 'free':
				$parkingType = 'Free parking';
				break;
			case 'permit':
				$parkingType = 'Parking permit';
				break;
			case 'meter':
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
		$message .= '<b>Parking provided:</b> ' . $parkingType . '<br />';
	}

	if ( isset( $b['congestion-charge'] ) ) {
		$message .= '<b>Congestion Charge Zone:</b> Yes<br />';
	}

	if ( isset( $b['walking-distance'] ) ) {
		if ( $b['walking-distance'] === '5' ) {
			$walking_distance = 'Near +&pound;5';
		} elseif ( $b['walking-distance'] === '10' ) {
			$walking_distance = 'Far (same area) +&pound;10';
		} else {
			$walking_distance = 'Far (different area) +&pound;20';
		}
		$message .= "<b>Get keys from different address:</b> {$walking_distance}<br />";
	}

	$message .= '<br />';

	// Service-specific block
	switch ( $service ) {
		case 'Regular Cleaning':
		case 'Regular Cleaning Luxury':
			$message .= _render_regular_cleaning( $b );
			break;
		case 'One Off Cleaning':
			$message .= _render_one_off( $b );
			break;
		case 'Antiviral Sanitisation':
			$message .= _render_antiviral( $b );
			break;
		case 'Carpet Cleaning':
			$message .= _render_carpet_cleaning( $b );
			break;
		case 'Window Cleaning':
			$message .= _render_window_cleaning( $b );
			break;
		case 'Upholstery Cleaning':
			$message .= _render_upholstery( $b );
			break;
		case 'Mattress Cleaning':
			$message .= _render_mattress( $b );
			break;
		case 'End of Tenancy Cleaning':
			$message .= _render_end_of_tenancy( $b );
			break;
		case 'After Builders Cleaning':
			$message .= _render_after_builders( $b );
			break;
		case 'Curtain Cleaning':
			$message .= _render_curtain( $b );
			break;
		case 'Oven Cleaning':
			$message .= _render_oven( $b );
			break;
		case 'Hard Floor Cleaning':
			$message .= _render_hard_floor( $b );
			break;
		case 'Gardening':
			$message .= _render_gardening( $b );
			break;
		case 'Mobile Car Valeting':
			$message .= _render_car_valeting( $b );
			break;
		case 'Wooden Floor Services':
			$message .= _render_wooden_floor( $b );
			break;
		case 'Rubbish Removal':
			$message .= _render_rubbish_removal( $b );
			break;
	}

	// Add-on service blocks (appended after main service)
	if ( isset( $b['added-carpet-cleaning'] ) ) {
		$message .= '<br /><h2>Carpet cleaning: </h2>' . _render_carpet_cleaning( $b );
	}
	if ( isset( $b['added-upholstery-cleaning'] ) ) {
		$message .= '<br /><h2>Upholstery cleaning: </h2>' . _render_upholstery( $b );
	}
	if ( isset( $b['added-mattress-cleaning'] ) ) {
		$message .= '<br /><h2>Mattress cleaning: </h2>' . _render_mattress( $b );
	}
	if ( isset( $b['added-window-cleaning'] ) ) {
		$message .= '<br /><h2>Window cleaning: </h2>' . _render_window_cleaning( $b );
	}
	if ( isset( $b['added-oven-cleaning'] ) ) {
		$message .= '<br /><h2>Oven cleaning: </h2>' . _render_oven( $b );
	}
	if ( isset( $b['added-curtain-cleaning'] ) ) {
		$message .= '<br /><h2>Curtain cleaning: </h2>' . _render_curtain( $b );
	}

	return $message;
}

/**
 * Render the email footer (appended to body before sending).
 *
 * @param string $site_name  e.g. "FastKlean"
 * @param string $site_url   e.g. "https://fastklean.co.uk"
 */
function render_booking_footer( string $site_name, string $site_url ): string {
	return "\r\n <br /><br /> This email was sent from the online booking system on {$site_name} ({$site_url})";
}

/**
 * Build the email subject line.
 *
 * @param string $service e.g. "Regular Cleaning"
 */
function render_booking_subject( string $service ): string {
	return $service . ' Booking';
}

/**
 * Transform date from yyyy-mm-dd (HTML date input) to dd/mm/yyyy.
 */
function transform_booking_date( string $date ): string {
	if ( strpos( $date, '-' ) !== false ) {
		$parts = explode( '-', $date );
		return $parts[2] . '/' . $parts[1] . '/' . $parts[0];
	}
	// Legacy mm/dd/yyyy format
	$parts = explode( '/', $date );
	if ( count( $parts ) === 3 ) {
		return $parts[1] . '/' . $parts[0] . '/' . $parts[2];
	}
	return $date;
}

// ============================================================
// Private per-service renderers — match submit.php exactly
// ============================================================

function _render_regular_cleaning( array $b ): string {
	$how_often = $b['how-often'] ?? '';
	if ( $how_often === 'frequently' ) {
		$how_often = ( $b['sessions-per-week'] ?? '' ) . ' sessions per week';
	}
	if ( isset( $b['have-pets'] ) ) {
		$pets = $b['pets'] ?? '';
	} else {
		$pets = 'None';
	}

	$result = '';
	if ( isset( $b['cleaning-hours'] ) && (int) $b['cleaning-hours'] > 0 ) {
		$result .= '<b>Cleaning hours:</b> ' . $b['cleaning-hours'] . '<br />';
	}
	if ( isset( $b['additional-fridge'] ) ) {
		$result .= '<b>Cleaning inside fridge:</b> Yes<br />';
	}
	if ( isset( $b['additional-laundry'] ) ) {
		$result .= '<b>Do laundry:</b> Yes<br />';
	}
	if ( isset( $b['additional-oven'] ) ) {
		$result .= '<b>Inside oven:</b> Yes<br />';
	}
	if ( isset( $b['additional-windows'] ) ) {
		$result .= '<b>Inside windows:</b> Yes<br />';
	}
	if ( isset( $b['additional-ironing'] ) ) {
		$result .= '<b>Ironing:</b> Yes<br />';
	}
	$result .= "<br /><b>How often:</b> {$how_often}<br />";
	if ( strlen( $pets ) > 0 ) {
		$result .= "<b>Pets:</b> {$pets}<br />";
	}
	return $result;
}

function _render_one_off( array $b ): string {
	$result = '';
	if ( isset( $b['one-off-bedrooms'] ) ) {
		$result .= '<b>Bedrooms: </b>' . $b['one-off-bedrooms'] . '<br />';
	}
	if ( isset( $b['one-off-bathrooms'] ) ) {
		$result .= '<b>Bathrooms: </b>' . $b['one-off-bathrooms'] . '<br />';
	}
	if ( isset( $b['one-off-additional'] ) ) {
		$result .= '<b>Additional rooms: </b>' . $b['one-off-additional'] . '<br />';
	}
	if ( isset( $b['type-housing'] ) ) {
		$result .= '<b>Housing type: </b>' . $b['type-housing'] . '<br />';
	}
	if ( isset( $b['one-off-cleaning-hours'] ) && $b['one-off-cleaning-hours'] > 0 ) {
		$result .= '<b>Cleaning hours:</b> ' . $b['one-off-cleaning-hours'] . '<br />';
	}
	if ( isset( $b['one-off-cleaning-hours'] ) && isset( $b['one-off-eco-natural-ingredients-hours'] ) && $b['one-off-eco-natural-ingredients-hours'] > 0 ) {
		$result .= '<b>Eco cleaning with natural ingredients:</b> ' . $b['one-off-eco-natural-ingredients-hours'] . ' hours<br />';
	}
	if ( isset( $b['one-off-cleaning-hours'] ) && isset( $b['one-off-eco-ionised-water-hours'] ) && $b['one-off-eco-ionised-water-hours'] > 0 ) {
		$result .= '<b>Eco cleaning with ionised water:</b> ' . $b['one-off-eco-ionised-water-hours'] . ' hours<br />';
	}
	if ( isset( $b['one-off-cleaning-hours'] ) && isset( $b['one-off-eco-bravo-products-hours'] ) && $b['one-off-eco-bravo-products-hours'] > 0 ) {
		$result .= '<b>Eco cleaning with eco bravo products:</b> ' . $b['one-off-eco-bravo-products-hours'] . ' hours<br />';
	}
	if ( isset( $b['inside-fridge'] ) ) {
		$result .= '<b>Inside fridge: </b>' . $b['inside-fridge'] . '<br />';
	}
	if ( isset( $b['inside-freezer'] ) ) {
		$result .= '<b>Inside freezer: </b>' . $b['inside-freezer'] . '<br />';
	}
	if ( isset( $b['inside-cupboards'] ) ) {
		$result .= '<b>Inside Cupboards: </b>' . $b['inside-cupboards'] . '<br />';
	}
	if ( isset( $b['wooden-blinds'] ) ) {
		$result .= '<b>Wooden blinds: </b>' . $b['wooden-blinds'] . '<br />';
	}
	if ( isset( $b['glass-panel'] ) ) {
		$result .= '<b>Glass panels: </b>' . $b['glass-panel'] . '<br />';
	}
	if ( isset( $b['equipment'] ) ) {
		$result .= '<b>Cleaner provides equipment:</b> Yes(+&pound;5/hr)<br />';
	}
	return $result;
}

function _render_antiviral( array $b ): string {
	$result = '';
	if ( isset( $b['antiviral-sanitisation-area'] ) && (int) $b['antiviral-sanitisation-area'] > 0 ) {
		$result .= '<b>Area:</b> ' . $b['antiviral-sanitisation-area'] . ' sq .m.<br />';
	}
	if ( isset( $b['antiviral-sanitisation-computers'] ) && (int) $b['antiviral-sanitisation-computers'] > 0 ) {
		$result .= '<b>Computers:</b> ' . $b['antiviral-sanitisation-computers'] . '<br />';
	}
	return $result;
}

function _render_carpet_cleaning( array $b ): string {
	$result = '';
	$steam_fields = [
		'carpet-steam-stairs'        => 'Stair carpets (steam)',
		'carpet-steam-lounge'        => 'Lounge carpets (steam)',
		'carpet-steam-dining-room'   => 'Dining room carpets (steam)',
		'carpet-steam-kitchen'       => 'Kitchen carpets (steam)',
		'carpet-steam-bedroom'       => 'Single bedroom carpets (steam)',
		'carpet-steam-double-bedroom'=> 'Double bedroom carpets (steam)',
		'carpet-steam-hallway'       => 'Hallway carpets (steam)',
		'carpet-steam-landing'       => 'Landing carpets (steam)',
		'carpet-steam-bathroom'      => 'Bathroom carpets (steam)',
		'carpet-steam-medium-rugs'   => 'Medium rugs (steam)',
		'carpet-steam-large-rugs'    => 'Large rugs (steam)',
	];
	$dry_fields = [
		'carpet-dry-stairs'         => 'Stair carpets (dry cleaning)',
		'carpet-dry-lounge'         => 'Lounge carpets (dry cleaning)',
		'carpet-dry-dining-room'    => 'Dining room carpets (dry cleaning)',
		'carpet-dry-kitchen'        => 'Kitchen carpets (dry cleaning)',
		'carpet-dry-bedroom'        => 'Single bedroom carpets (dry cleaning)',
		'carpet-dry-double-bedroom' => 'Double bedroom carpets (dry cleaning)',
		'carpet-dry-hallway'        => 'Hallway carpets (dry cleaning)',
		'carpet-dry-landing'        => 'Landing carpets (dry cleaning)',
		'carpet-dry-bathroom'       => 'Bathroom carpets (dry cleaning)',
		'carpet-dry-medium-rugs'    => 'Medium rugs (dry cleaning)',
		'carpet-dry-large-rugs'     => 'Large rugs (dry cleaning)',
	];
	foreach ( array_merge( $steam_fields, $dry_fields ) as $key => $label ) {
		if ( isset( $b[ $key ] ) && (int) $b[ $key ] > 0 ) {
			$result .= "<b>{$label}:</b> " . $b[ $key ] . '<br />';
		}
	}
	if ( isset( $b['carpet-scotchgard'] ) ) {
		$result .= '<br /><b>Use Scotchgard:</b> Yes<br />';
	} else {
		$result .= '<br /><b>Use Scotchgard:</b> No<br />';
	}
	return $result;
}

function _render_mattress( array $b ): string {
	$result = '';
	$fields = [
		'mattress-steam-babycot'    => 'Babycots (steam)',
		'mattress-steam-single'     => 'Single mattresses (steam)',
		'mattress-steam-double'     => 'Double mattresses (steam)',
		'mattress-steam-queen-size' => 'Queen size mattresses (steam)',
		'mattress-steam-king-size'  => 'King size mattresses (steam)',
		'mattress-dry-babycot'      => 'Babycots (dry cleaning)',
		'mattress-dry-single'       => 'Single dryes (dry cleaning)',
		'mattress-dry-double'       => 'Double dryes (dry cleaning)',
		'mattress-dry-queen-size'   => 'Queen size dryes (dry cleaning)',
		'mattress-dry-king-size'    => 'King size dryes (dry cleaning)',
	];
	foreach ( $fields as $key => $label ) {
		if ( isset( $b[ $key ] ) && (int) $b[ $key ] > 0 ) {
			$result .= "<b>{$label}:</b> " . $b[ $key ] . '<br />';
		}
	}
	return $result;
}

function _render_upholstery( array $b ): string {
	$result = '';
	$fields = [
		'upholstery-steam-two-seater'          => '2 seaters - fabric (steam)',
		'upholstery-steam-three-seater'        => '3 seaters - fabric (steam)',
		'upholstery-steam-five-seater'         => '5 seaters - fabric (steam)',
		'upholstery-steam-armchair'            => 'Armchairs - fabric (steam)',
		'upholstery-steam-headboard'           => 'Headboard (steam)',
		'upholstery-steam-dining-chair'        => 'Dining chairs (steam)',
		'upholstery-l-shaped-two-seater'       => 'L-shaped two seater (steam)',
		'upholstery-l-shaped-three-seater'     => 'L-shaped three seater (steam)',
		'upholstery-l-shaped-four-seater'      => 'L-shaped four seater (steam)',
		'upholstery-l-shaped-five-seater'      => 'L-shaped five seater (steam)',
		'upholstery-steam-two-seater-leather'  => '2 seaters - leather (steam)',
		'upholstery-steam-three-seater-leather'=> '3 seaters - leather (steam)',
		'upholstery-steam-five-seater-leather' => '5 seaters - leather (steam)',
		'upholstery-steam-armchair-leather'    => 'Armchairs - leather (steam)',
		'upholstery-steam-headboard-leather'   => 'Headboard - leather (steam)',
		'upholstery-dry-two-seater'            => '2 seaters (dry cleaning)',
		'upholstery-dry-three-seater'          => '3 seaters (dry cleaning)',
		'upholstery-dry-five-seater'           => '5 seaters (dry cleaning)',
		'upholstery-dry-armchair'              => 'Armchairs (dry cleaning)',
		'upholstery-dry-dining-chair'          => 'Dining chair (dry cleaning)',
	];
	foreach ( $fields as $key => $label ) {
		if ( isset( $b[ $key ] ) && (int) $b[ $key ] > 0 ) {
			$result .= "<b>{$label}:</b> " . $b[ $key ] . '<br />';
		}
	}
	if ( isset( $b['upholstery-scotchgard'] ) ) {
		$result .= '<br /><b>Use Scotchgard:</b> Yes<br />';
	} else {
		$result .= '<br /><b>Use Scotchgard:</b> No<br />';
	}
	return $result;
}

function _render_window_cleaning( array $b ): string {
	$result = '';
	$fields = [
		'window-small'       => 'Small windows',
		'window-medium'      => 'Medium windows',
		'window-large'       => 'Large windows',
		'window-bay'         => 'Bay windows',
		'window-french-door' => 'French doors',
	];
	foreach ( $fields as $key => $label ) {
		if ( isset( $b[ $key ] ) && (int) $b[ $key ] > 0 ) {
			$result .= "<b>{$label}:</b> " . $b[ $key ] . '<br />';
		}
	}
	if ( isset( $b['window-internal-cleaning'] ) ) {
		$result .= '<br /><b>Internal cleaning:</b> Yes<br />';
	} else {
		$result .= '<br /><b>Internal cleaning:</b> No<br />';
	}
	$result .= _render_electricity_water( $b );
	return $result;
}

function _render_curtain( array $b ): string {
	$result = '';
	$fields = [
		'curtain-steam-short'       => 'Short curtains (steam)',
		'curtain-steam-full-length' => 'Full length curtains (steam)',
		'curtain-dry-short'         => 'Short curtains (dry cleaning)',
		'curtain-dry-full-length'   => 'Full length curtains (dry cleaning)',
	];
	foreach ( $fields as $key => $label ) {
		if ( isset( $b[ $key ] ) && (int) $b[ $key ] > 0 ) {
			$result .= "<b>{$label}:</b> " . $b[ $key ] . '<br />';
		}
	}
	return $result;
}

function _render_oven( array $b ): string {
	$result = '';
	$fields = [
		'oven-gas-hobs'          => 'Gas hobs',
		'oven-gas-hobs-double'   => 'Gas hobs (double)',
		'oven-ceramic-hobs'      => 'Ceramic hobs',
		'oven-microwave'         => 'Microwave ovens',
		'oven-single'            => 'Single ovens',
		'oven-double'            => 'Double ovens',
		'oven-extractor'         => 'Extractors',
		'oven-extractor-double'  => 'Extractors (double)',
		'oven-range'             => 'Range ovens',
		'oven-range-hood'        => 'Range hoods or hobs',
		'oven-range-gas-hob'     => 'Range gas hobs',
		'oven-range-electric-hob'=> 'Range electric hobs',
		'oven-range-ceramic-hob' => 'Range ceramic hobs',
		'oven-range-extractor'   => 'Range extractors',
		'oven-range-wide'        => 'Single wide ovens',
		'oven-range-half-size'   => 'Range 1/2 size ovens',
		'oven-range-90'          => 'Master range - 90cm',
		'oven-range-100'         => 'Master range - 100cm',
		'oven-warming-drawer'    => 'Warming drawer doors',
		'oven-two-size'          => 'Double oven size',
		'oven-four-size'         => 'Four oven size',
		'oven-side-module'       => 'Side modules',
		'oven-small-bbq'         => 'Small barbecues',
		'oven-large-bbq'         => 'Large barbecues',
		'oven-combo-deal-1'      => 'Single Oven (2 racks) + Hob + Extractor (combo deal)',
		'oven-combo-deal-2'      => 'Double Oven (3 racks) + Hob + Extractor (combo deal)',
		'oven-combo-deal-3'      => 'Master Range - 90cm + hob and extractor (combo deal)',
		'oven-combo-deal-4'      => 'Master Range - 100cm + hob and extractor (combo deal)',
	];
	foreach ( $fields as $key => $label ) {
		if ( isset( $b[ $key ] ) && (int) $b[ $key ] > 0 ) {
			$result .= "<b>{$label}:</b> " . $b[ $key ] . '<br />';
		}
	}
	return $result;
}

function _render_rubbish_removal( array $b ): string {
	$result = '';
	if ( isset( $b['rubbish-cubic-yards'] ) && (int) $b['rubbish-cubic-yards'] > 0 ) {
		$result .= '<b>Rubbish cubic yards:</b> ' . $b['rubbish-cubic-yards'] . '<br />';
	}
	if ( isset( $b['rubbish-weight'] ) && (int) $b['rubbish-weight'] > 0 ) {
		$result .= '<b>Rubbish weight:</b> ' . $b['rubbish-weight'] . '<br />';
	}
	return $result;
}

function _render_after_builders( array $b ): string {
	$result = '';
	if ( isset( $b['after-builders-cleaning-hours'] ) && $b['after-builders-cleaning-hours'] ) {
		$result .= '<b>Cleaning hours: </b>' . $b['after-builders-cleaning-hours'] . '<br />';
	}
	if ( isset( $b['after-building-equipment-cleaning-hours'] ) && $b['after-building-equipment-cleaning-hours'] ) {
		$result .= '<b>Cleaning + Equipment: </b>' . $b['after-building-equipment-cleaning-hours'] . ' hours<br />';
	}
	if ( isset( $b['inside-fridge'] ) ) {
		$result .= '<b>Inside fridge: </b>' . $b['inside-fridge'] . '<br />';
	}
	if ( isset( $b['inside-freezer'] ) ) {
		$result .= '<b>Inside freezer: </b>' . $b['inside-freezer'] . '<br />';
	}
	if ( isset( $b['inside-cupboards'] ) ) {
		$result .= '<b>Inside Cupboards: </b>' . $b['inside-cupboards'] . '<br />';
	}
	if ( isset( $b['wooden-blinds'] ) ) {
		$result .= '<b>Wooden blinds: </b>' . $b['wooden-blinds'] . '<br />';
	}
	if ( isset( $b['glass-panel'] ) ) {
		$result .= '<b>Glass panels: </b>' . $b['glass-panel'] . '<br />';
	}
	return $result;
}

function _render_end_of_tenancy( array $b ): string {
	$result = '';
	if ( isset( $b['property-type'] ) ) {
		$result .= '<b>Property type: </b>' . $b['property-type'] . '<br />';
	}
	if ( isset( $b['end-of-tenancy-bedrooms'] ) ) {
		$result .= '<b>Bedrooms: </b>' . $b['end-of-tenancy-bedrooms'] . '<br />';
	}
	if ( isset( $b['end-of-tenancy-bathrooms'] ) ) {
		$result .= '<b>Bathrooms: </b>' . $b['end-of-tenancy-bathrooms'] . '<br />';
	}
	if ( isset( $b['end-of-tenancy-additional'] ) ) {
		$result .= '<b>Additional rooms: </b>' . $b['end-of-tenancy-additional'] . '<br />';
	}
	if ( isset( $b['collect-keys'] ) ) {
		if ( isset( $b['keys-distance'] ) ) {
			if ( $b['keys-distance'] === 'near' ) {
				$result .= '<b>Collect keys from a near address (+10&pound;)</b><br />';
			} elseif ( $b['keys-distance'] === 'far' ) {
				$result .= '<b>Collect keys from a far address (+20&pound;)</b><br />';
			} else {
				$result .= '<b>Collect keys from a different address</b><br />';
			}
		}
	}
	if ( isset( $b['inventory-check-date'] ) && $b['inventory-check-date'] ) {
		$result .= '<b>Inventory check: </b>' . $b['inventory-check-date'];
		if ( isset( $b['inventory-check-time'] ) && $b['inventory-check-time'] ) {
			$result .= ' in the ' . strtolower( $b['inventory-check-time'] );
		}
		$result .= '<br />';
	}
	return $result;
}

function _render_hard_floor( array $b ): string {
	$floor_type  = '<b>Floor type: </b>';
	$floor_edges = '<b>Floor surface: </b>';
	$floor_size  = '<b>Floor size: </b>';

	if ( isset( $b['floor-type'] ) ) {
		$map = [ 'wooden' => 'Wooden', 'porous' => 'Porous', 'semi-porous' => 'Semi-porous', 'non-porous' => 'Non-porous' ];
		$floor_type .= $map[ $b['floor-type'] ] ?? '';
	}
	if ( isset( $b['floor-edges'] ) ) {
		$map = [ 'flat' => 'Flat surface', 'build-up' => 'Build up', 'polish' => 'Non-removable polish/seal', 'loose' => 'Loose', 'sealed' => 'Sealed', 'curled' => 'Curled' ];
		$floor_edges .= $map[ $b['floor-edges'] ] ?? '';
	}
	if ( isset( $b['floor-size'] ) ) {
		$floor_size .= $b['floor-size'] . ' sq m';
	}

	return $floor_size . '<br />' . $floor_type . '<br />' . $floor_edges . '<br />';
}

function _render_wooden_floor( array $b ): string {
	$result = '';
	$services = [
		'floor-service-1'  => [ 'label' => 'Sanding only', 'unit' => 'sq m' ],
		'floor-service-2'  => [ 'label' => 'Sanding and 3 coats of clear lacquer', 'unit' => 'sq m' ],
		'floor-service-3'  => [ 'label' => 'Extra coat of lacquer, hardwax, oil or stain', 'unit' => 'sq m' ],
		'floor-service-4'  => [ 'label' => 'Upgrade to HP Commercial lacquer', 'unit' => 'sq m' ],
		'floor-service-5'  => [ 'label' => 'Sanding and 2 coats of hardwax oil', 'unit' => 'sq m' ],
		'floor-service-6'  => [ 'label' => 'Sanding and 3 coats of wood floor oil', 'unit' => 'sq m' ],
		'floor-service-7'  => [ 'label' => 'Staining/colouring', 'unit' => 'sq m' ],
		'floor-service-8'  => [ 'label' => 'Lime washing/liming/whitening', 'unit' => 'sq m' ],
		'floor-service-9'  => [ 'label' => 'Gap filling sawdust+resin (up to 3mm)', 'unit' => 'sq m' ],
		'floor-service-10' => [ 'label' => 'Gap filling reclaimed wooden slivers (wider than 3mm)', 'unit' => 'sq m' ],
		'floor-service-11' => [ 'label' => 'Gap filling flexible gap master (wider than 3mm, up to 9mm)', 'unit' => 'sq m' ],
		'floor-service-12' => [ 'label' => 'Reclaimed pine floorboards changed', 'unit' => 'm' ],
		'floor-service-13' => [ 'label' => 'Stairs: sand and seal', 'unit' => 'steps' ],
		'floor-service-14' => [ 'label' => 'Carpet removal', 'unit' => 'rooms' ],
		'floor-service-15' => [ 'label' => 'Solid wood flooring installation', 'unit' => 'sq m' ],
		'floor-service-16' => [ 'label' => 'Parquet/strip floor installation', 'unit' => 'sq m' ],
		'floor-service-17' => [ 'label' => 'Skirting fitting (does not include supplying the skirting)', 'unit' => 'm' ],
		'floor-service-18' => [ 'label' => 'Engineered wood floor installation', 'unit' => 'sq m' ],
		'floor-service-19' => [ 'label' => 'Engineered wood door trims', 'unit' => 'doorways' ],
		'floor-service-20' => [ 'label' => 'Skirting fitting (does not include supply)', 'unit' => 'm' ],
		'floor-service-21' => [ 'label' => 'Staircase fitting per step', 'unit' => 'steps' ],
		'floor-service-22' => [ 'label' => 'Laminate floor installation', 'unit' => 'sq m' ],
		'floor-service-23' => [ 'label' => 'Laminate wood door trims', 'unit' => 'doorways' ],
		'floor-service-24' => [ 'label' => 'Beading fitting (does not include supply)', 'unit' => 'm' ],
		'floor-service-25' => [ 'label' => 'Old laminate removal', 'unit' => 'sq m' ],
		'floor-service-26' => [ 'label' => 'Wooden kitchen countertop sanding/polishing/oiling', 'unit' => 'm' ],
		'floor-service-27' => [ 'label' => 'Additional labour (restoration, repairs, furniture movement, etc.)', 'unit' => 'm' ],
	];
	foreach ( $services as $key => $info ) {
		if ( isset( $b[ $key ] ) && strlen( $b[ $key ] ) > 0 ) {
			$result .= '<b>' . $info['label'] . ': </b> ' . $b[ $key ] . ' ' . $info['unit'] . '<br />';
		}
	}
	return $result;
}

function _render_gardening( array $b ): string {
	$result = '';
	$result .= '<b>Gardening hours: </b> ' . ( $b['gardening-hours'] ?? '' ) . '<br />';
	if ( isset( $b['small-bags'] ) && (int) $b['small-bags'] > 0 ) {
		$result .= '<b>Rubbish removal - small bags:</b> ' . $b['small-bags'] . '<br />';
	}
	if ( isset( $b['jumbo-bags'] ) && (int) $b['jumbo-bags'] > 0 ) {
		$result .= '<b>Rubbish removal -  jumbo bags:</b> ' . $b['jumbo-bags'] . '<br />';
	}
	$result .= _render_electricity_water( $b );
	return $result;
}

function _render_car_valeting( array $b ): string {
	$result  = '';
	$counter = 1;
	while ( isset( $b[ 'vehicle-type-' . $counter ] ) ) {
		$vt_map = [ 'city' => 'City/sports/convertible car', 'space-cruiser' => 'Space cruiser', '4x4' => '4x4', 'van' => 'Van, truck' ];
		$st_map = [ 'internal' => 'Internal', 'external' => 'External', 'mini' => 'Mini', 'midi' => 'Midi', 'full' => 'Full' ];
		$im_map = [ 'fabric' => 'Fabric', 'leather' => 'Leather' ];
		$vehicle_type  = $vt_map[ $b[ 'vehicle-type-' . $counter ] ] ?? '';
		$service_type  = $st_map[ $b[ 'service-type-' . $counter ] ] ?? '';
		$interior      = $im_map[ $b[ 'interior-material-' . $counter ] ] ?? '';
		$vehicle_amount= $b[ 'car-valeting-vehicle-amount-' . $counter ] ?? '';
		$result .= '<br />';
		$result .= "<b>Vehicle type:</b> {$vehicle_type}<br />";
		$result .= "<b>Service type:</b> {$service_type}<br />";
		$result .= "<b>Interior material:</b> {$interior}<br />";
		$result .= "<b>Vehicle amount:</b> {$vehicle_amount}<br />";
		$counter++;
	}
	$result .= '<br />';
	$result .= _render_electricity_water( $b );
	return $result;
}

function _render_electricity_water( array $b ): string {
	$result = '';
	if ( isset( $b['electricity'] ) ) {
		$outlet = '';
		if ( isset( $b['outlet-location'] ) ) {
			if ( $b['outlet-location'] === 'inside' ) {
				$outlet = 'inside';
			} elseif ( $b['outlet-location'] === 'outside' ) {
				$outlet = 'outside';
			}
		}
		$result .= '<b>Can provide electricity:</b> Yes';
		if ( $outlet ) {
			$result .= " ({$outlet} outlet)";
		}
	} else {
		$result .= '<b>Can provide electricity:</b> No';
	}
	$result .= '<br />';
	if ( isset( $b['water'] ) ) {
		$tap = '';
		if ( isset( $b['tap-location'] ) ) {
			if ( $b['tap-location'] === 'inside' ) {
				$tap = 'inside';
			} elseif ( $b['tap-location'] === 'outside' ) {
				$tap = 'outside';
			}
		}
		$result .= '<b>Can provide water:</b> Yes';
		if ( $tap ) {
			$result .= " ({$tap} tap)";
		}
	} else {
		$result .= '<b>Can provide water:</b> No';
	}
	$result .= '<br />';
	return $result;
}
