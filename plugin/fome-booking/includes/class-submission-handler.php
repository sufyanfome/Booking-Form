<?php
defined( 'ABSPATH' ) || exit;

require_once dirname( __DIR__, 3 ) . '/shared/email-contract/EmailRenderer.php';

/**
 * Handles the booking form POST submission.
 *
 * Flow:
 *   1. Verify nonce
 *   2. Sanitise & validate inputs
 *   3. Validate postcode against hub
 *   4. Server-side price recalculation from hub config
 *   5. Render email body via EmailRenderer
 *   6. POST booking log to hub
 *   7. Send email via SMTP (wp_mail + PHPMailer overrides)
 *   8. For paid services: request Stripe checkout session from hub → redirect
 *   9. For commercial quotes: redirect to thank-you page
 */
class Fome_Booking_Submission_Handler {

	public static function init(): void {
		add_action( 'wp_ajax_nopriv_fome_submit_booking', [ __CLASS__, 'handle' ] );
		add_action( 'wp_ajax_fome_submit_booking',        [ __CLASS__, 'handle' ] );
	}

	public static function handle(): void {
		// 1. Nonce
		if ( ! check_ajax_referer( 'fome_booking_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
		}

		// 2. Pull config (cached)
		$config = Fome_Booking_Config::get();
		if ( ! $config ) {
			wp_send_json_error( [ 'message' => 'Booking is temporarily unavailable. Please try again shortly.' ], 503 );
		}

		// 3. Sanitise inputs
		$b = self::sanitise( $_POST );

		// 4. Validate required core fields
		$errors = self::validate_core( $b );
		if ( $errors ) {
			wp_send_json_error( [ 'message' => implode( ' ', $errors ) ], 422 );
		}

		// 5. Validate postcode
		if ( ! self::postcode_covered( $b['postcode'], $config ) ) {
			wp_send_json_error( [ 'message' => 'Sorry, we don\'t currently cover your postcode.' ], 422 );
		}

		// 6. Server-side price recalculation
		try {
			$pricing = self::calculate_price( $b, $config );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ], 422 );
		}

		// Merge authoritative price back into $b for email rendering
		$b['price']          = $pricing['price_display'];
		$b['original-price'] = $pricing['original_price_display'];
		$b['vat_enabled']    = $config['settings']['vat_enabled'] ?? false;

		// 7. Validate discount/gift card (read from config)
		if ( ! empty( $b['discount-code'] ) ) {
			$dc = self::find_discount_code( $b['discount-code'], $config );
			if ( ! $dc ) {
				wp_send_json_error( [ 'message' => 'Invalid discount code.' ], 422 );
			}
		}
		if ( ! empty( $b['gift-card-code'] ) ) {
			$gc = self::find_gift_card( $b['gift-card-code'], $config );
			if ( ! $gc || $gc['balance'] <= 0 ) {
				wp_send_json_error( [ 'message' => 'Invalid or empty gift card.' ], 422 );
			}
			$b['gift-card-amount'] = number_format( min( $gc['balance'], $pricing['total_pence'] / 100 ), 2 );
		}

		// 8. Render email body
		$email_body   = render_booking_email( $b );
		$site_name    = $config['site']['name']    ?? get_bloginfo( 'name' );
		$site_url     = $config['site']['url']     ?? home_url();
		$email_footer = render_booking_footer( $site_name, $site_url );
		$full_body    = $email_body . $email_footer;
		$subject      = render_booking_subject( $b['service'] );

		// 9. Log booking to hub
		$booking_id = self::log_to_hub( $b, $full_body, $pricing, $config );

		// 10. Send email
		$recipients = $config['site']['recipient_emails'] ?? [];
		if ( ! empty( $b['email'] ) ) {
			$recipients[] = $b['email']; // customer copy
		}
		self::send_email( $subject, $full_body, $recipients );

		// 11. Determine service type → payment or free quote
		$service_slug = self::service_slug( $b['service'] );
		$is_commercial = self::is_commercial( $b['service'], $config );
		$is_subscription = self::is_subscription( $b['service'] );

		if ( $is_commercial ) {
			// Free quote — no payment
			wp_send_json_success( [ 'redirect' => home_url( '/thank-you/' ) ] );
		}

		// 12. Create Stripe checkout session via hub
		$checkout_url = self::create_checkout_session( $booking_id, $b, $pricing, $config );
		if ( ! $checkout_url ) {
			wp_send_json_error( [ 'message' => 'Payment setup failed. Your booking was received — we\'ll be in touch shortly.' ], 500 );
		}

		wp_send_json_success( [ 'redirect' => $checkout_url ] );
	}

	// -------------------------------------------------------------------------
	// Sanitisation
	// -------------------------------------------------------------------------

	private static function sanitise( array $post ): array {
		$text = fn( $k ) => sanitize_text_field( $post[ $k ] ?? '' );
		$num  = fn( $k ) => (int) ( $post[ $k ] ?? 0 );

		$b = [
			'service'        => $text( 'service' ),
			'name'           => $text( 'name' ),
			'phone'          => $text( 'phone' ),
			'email'          => sanitize_email( $post['email'] ?? '' ),
			'postcode'       => strtoupper( sanitize_text_field( $post['postcode'] ?? '' ) ),
			'address'        => sanitize_textarea_field( $post['address'] ?? '' ),
			'calling-hours'  => $text( 'calling-hours' ),
			'date'           => $text( 'date' ),   // expects dd/mm/yyyy from JS
			'time'           => $text( 'time' ),
			'note'           => sanitize_textarea_field( $post['note'] ?? '' ),
			'discount-code'  => strtoupper( $text( 'discount-code' ) ),
			'gift-card-code' => strtoupper( $text( 'gift-card-code' ) ),
		];

		// Service-specific fields — pass through as sanitised text/int
		$extra_keys = [
			// Regular cleaning
			'bedrooms', 'bathrooms', 'frequency', 'hours', 'extra-tasks',
			// One-off / end of tenancy
			'property-type', 'furnished',
			// Carpet / upholstery / mattress
			'carpet-rooms', 'sofa-type', 'mattress-size', 'fabric-or-steam',
			'scotchgard', 'pets', 'stains',
			// Window
			'window-type', 'window-count', 'window-floors',
			// Oven
			'oven-type', 'oven-trays', 'oven-shelves',
			// Curtain
			'curtain-type', 'curtain-count',
			// Rubbish removal
			'rubbish-size',
			// After builders
			'ab-property-type', 'ab-size',
			// Hard/wooden floor
			'floor-size', 'floor-type',
			// Gardening
			'garden-size',
			// Car valeting
			'car-type',
			// Add-ons (JSON arrays passed as comma-separated)
			'carpet-addons', 'upholstery-addons', 'mattress-addons',
			'window-addons', 'oven-addons', 'curtain-addons',
			// Antiviral
			'antiviral-size',
		];

		foreach ( $extra_keys as $k ) {
			if ( isset( $post[ $k ] ) ) {
				$b[ $k ] = is_array( $post[ $k ] )
					? array_map( 'sanitize_text_field', $post[ $k ] )
					: sanitize_text_field( $post[ $k ] );
			}
		}

		return $b;
	}

	// -------------------------------------------------------------------------
	// Validation
	// -------------------------------------------------------------------------

	private static function validate_core( array $b ): array {
		$errors = [];
		if ( ! $b['service'] )       $errors[] = 'Please select a service.';
		if ( ! $b['name'] )          $errors[] = 'Please enter your name.';
		if ( ! $b['phone'] )         $errors[] = 'Please enter your phone number.';
		if ( ! is_email( $b['email'] ) ) $errors[] = 'Please enter a valid email address.';
		if ( ! $b['postcode'] )      $errors[] = 'Please enter your postcode.';
		if ( ! $b['date'] )          $errors[] = 'Please select a date.';
		if ( ! $b['time'] )          $errors[] = 'Please select a time slot.';
		return $errors;
	}

	// -------------------------------------------------------------------------
	// Postcode check
	// -------------------------------------------------------------------------

	private static function postcode_covered( string $postcode, array $config ): bool {
		$outcodes = $config['postcodes'] ?? [];
		if ( empty( $outcodes ) ) return true; // no restriction configured

		// Extract outcode (letters before the space or digits)
		preg_match( '/^([A-Z]{1,2})/', strtoupper( trim( $postcode ) ), $m );
		$outcode = $m[1] ?? '';
		return in_array( $outcode, $outcodes, true );
	}

	// -------------------------------------------------------------------------
	// Server-side price calculation
	// -------------------------------------------------------------------------

	/**
	 * @throws \Exception on unknown service or invalid inputs
	 */
	private static function calculate_price( array $b, array $config ): array {
		$prices   = $config['prices']   ?? [];
		$settings = $config['settings'] ?? [];

		$vat       = (bool) ( $settings['vat_enabled']             ?? false );
		$online_m  = (float) ( $settings['online_discount_multiplier'] ?? 1.0 );

		$net = self::net_price( $b, $prices );

		// Apply online discount
		$discounted = round( $net * $online_m, 2 );

		// Apply discount code if present
		$after_dc = $discounted;
		if ( ! empty( $b['discount-code'] ) ) {
			$dc = self::find_discount_code( $b['discount-code'], $config );
			if ( $dc ) {
				$after_dc = round( $discounted * (float) $dc['multiplier'], 2 );
			}
		}

		// VAT
		$with_vat = $vat ? round( $after_dc * 1.2, 2 ) : $after_dc;

		// Gift card reduction (for display; Stripe handles actual deduction)
		$final = $with_vat;
		if ( ! empty( $b['gift-card-code'] ) ) {
			$gc = self::find_gift_card( $b['gift-card-code'], $config );
			if ( $gc ) {
				$final = max( 0, round( $with_vat - (float) $gc['balance'], 2 ) );
			}
		}

		$total_pence = (int) round( $final * 100 );

		// Display strings matching legacy format
		$vat_suffix       = $vat ? ' (VAT incl.)' : '';
		$original_display = ( $net !== $discounted )
			? '&pound;' . number_format( $net, 2 ) . $vat_suffix
			: '';
		$price_display    = '&pound;' . number_format( $final, 2 ) . $vat_suffix;

		return [
			'net'                    => $net,
			'total'                  => $final,
			'total_pence'            => $total_pence,
			'price_display'          => $price_display,
			'original_price_display' => $original_display,
			'vat_enabled'            => $vat,
		];
	}

	/**
	 * Calculate the pre-discount, pre-VAT net price using hub price table.
	 */
	private static function net_price( array $b, array $prices ): float {
		$p   = fn( string $k ): float => (float) ( $prices[ $k ] ?? 0.0 );
		$svc = $b['service'];

		// --- Regular Cleaning ---
		if ( str_contains( $svc, 'Regular Cleaning' ) ) {
			$beds  = (int) ( $b['bedrooms']  ?? 1 );
			$baths = (int) ( $b['bathrooms'] ?? 1 );
			$freq  = $b['frequency'] ?? 'weekly';
			$luxury = str_contains( $svc, 'Luxury' );

			$key = $luxury
				? "luxury_cleaning_{$freq}_{$beds}bed_{$baths}bath"
				: "regular_cleaning_{$freq}_{$beds}bed_{$baths}bath";

			$net = $p( $key );
			if ( ! $net ) {
				// Fallback: hourly calculation
				$hours = (float) ( $b['hours'] ?? 2 );
				$rate  = $luxury ? $p( 'luxury_hourly_rate' ) : $p( 'regular_hourly_rate' );
				$net   = round( $hours * $rate, 2 );
			}
			return $net;
		}

		// --- One-Off Cleaning ---
		if ( str_starts_with( $svc, 'One-Off' ) ) {
			$beds  = (int) ( $b['bedrooms']  ?? 1 );
			$baths = (int) ( $b['bathrooms'] ?? 1 );
			return $p( "one_off_{$beds}bed_{$baths}bath" );
		}

		// --- End of Tenancy ---
		if ( str_starts_with( $svc, 'End of Tenancy' ) ) {
			$type      = sanitize_key( $b['property-type'] ?? 'flat' );
			$beds      = (int) ( $b['bedrooms'] ?? 1 );
			$furnished = ( $b['furnished'] ?? 'furnished' ) === 'furnished' ? 'furnished' : 'unfurnished';
			return $p( "eot_{$type}_{$beds}bed_{$furnished}" );
		}

		// --- After Builders ---
		if ( str_starts_with( $svc, 'After Builders' ) ) {
			$size = sanitize_key( $b['ab-size'] ?? 'studio' );
			return $p( "after_builders_{$size}" );
		}

		// --- Carpet Cleaning ---
		if ( str_starts_with( $svc, 'Carpet' ) ) {
			$rooms = (int) ( $b['carpet-rooms'] ?? 1 );
			$base  = $p( "carpet_{$rooms}room" );
			$net   = $base;
			if ( ! empty( $b['carpet-addons'] ) ) {
				foreach ( (array) $b['carpet-addons'] as $addon ) {
					$net += $p( 'carpet_addon_' . sanitize_key( $addon ) );
				}
			}
			return $net;
		}

		// --- Upholstery Cleaning ---
		if ( str_starts_with( $svc, 'Upholstery' ) ) {
			$type = sanitize_key( $b['sofa-type'] ?? '2seater' );
			return $p( "upholstery_{$type}" );
		}

		// --- Mattress Cleaning ---
		if ( str_starts_with( $svc, 'Mattress' ) ) {
			$size = sanitize_key( $b['mattress-size'] ?? 'single' );
			return $p( "mattress_{$size}" );
		}

		// --- Window Cleaning ---
		if ( str_starts_with( $svc, 'Window' ) ) {
			$type  = sanitize_key( $b['window-type'] ?? 'residential' );
			$count = (int) ( $b['window-count'] ?? 1 );
			return $p( "window_{$type}_{$count}" );
		}

		// --- Oven Cleaning ---
		if ( str_starts_with( $svc, 'Oven' ) ) {
			$type = sanitize_key( $b['oven-type'] ?? 'single' );
			$net  = $p( "oven_{$type}" );
			$net += (int) ( $b['oven-trays']  ?? 0 ) * $p( 'oven_tray' );
			$net += (int) ( $b['oven-shelves'] ?? 0 ) * $p( 'oven_shelf' );
			return $net;
		}

		// --- Curtain Cleaning ---
		if ( str_starts_with( $svc, 'Curtain' ) ) {
			$type  = sanitize_key( $b['curtain-type'] ?? 'pair' );
			$count = (int) ( $b['curtain-count'] ?? 1 );
			return $p( "curtain_{$type}_{$count}" );
		}

		// --- Rubbish Removal ---
		if ( str_starts_with( $svc, 'Rubbish' ) ) {
			$size = sanitize_key( $b['rubbish-size'] ?? 'small' );
			return $p( "rubbish_{$size}" );
		}

		// --- Hard Floor Cleaning ---
		if ( str_starts_with( $svc, 'Hard Floor' ) ) {
			$size = sanitize_key( $b['floor-size'] ?? 'small' );
			return $p( "hard_floor_{$size}" );
		}

		// --- Wooden Floor Polishing ---
		if ( str_starts_with( $svc, 'Wooden Floor' ) ) {
			$size = sanitize_key( $b['floor-size'] ?? 'small' );
			return $p( "wooden_floor_{$size}" );
		}

		// --- Gardening ---
		if ( str_starts_with( $svc, 'Gardening' ) ) {
			$size = sanitize_key( $b['garden-size'] ?? 'small' );
			return $p( "gardening_{$size}" );
		}

		// --- Car Valeting ---
		if ( str_starts_with( $svc, 'Car Valeting' ) ) {
			$type = sanitize_key( $b['car-type'] ?? 'hatchback' );
			return $p( "car_valeting_{$type}" );
		}

		// --- Antiviral Sanitisation ---
		if ( str_starts_with( $svc, 'Antiviral' ) ) {
			$size = sanitize_key( $b['antiviral-size'] ?? 'studio' );
			return $p( "antiviral_{$size}" );
		}

		// --- Commercial / Electricity & Water (free quote) ---
		return 0.0;
	}

	// -------------------------------------------------------------------------
	// Discount / gift-card helpers
	// -------------------------------------------------------------------------

	private static function find_discount_code( string $code, array $config ): ?array {
		foreach ( $config['discount_codes'] ?? [] as $dc ) {
			if ( strtoupper( $dc['code'] ) === strtoupper( $code ) && $dc['status'] === 'active' ) {
				return $dc;
			}
		}
		return null;
	}

	private static function find_gift_card( string $code, array $config ): ?array {
		foreach ( $config['gift_cards'] ?? [] as $gc ) {
			if ( strtoupper( $gc['code'] ) === strtoupper( $code ) && $gc['status'] === 'active' ) {
				return $gc;
			}
		}
		return null;
	}

	// -------------------------------------------------------------------------
	// Hub: log booking
	// -------------------------------------------------------------------------

	private static function log_to_hub( array $b, string $email_body, array $pricing, array $config ): int {
		$hub_url = rtrim( get_option( 'fome_hub_url', '' ), '/' );
		$api_key = get_option( 'fome_site_api_key', '' );
		$site_id = (int) get_option( 'fome_site_id', 0 );

		if ( ! $hub_url || ! $api_key || ! $site_id ) {
			return 0;
		}

		$payload = [
			'site_id'        => $site_id,
			'service'        => $b['service'],
			'customer_name'  => $b['name'],
			'customer_email' => $b['email'],
			'customer_phone' => $b['phone'],
			'postcode'       => $b['postcode'],
			'booking_date'   => $b['date'],
			'booking_time'   => $b['time'],
			'total_pence'    => $pricing['total_pence'],
			'email_body'     => $email_body,
			'status'         => 'pending_payment',
			'raw_fields'     => $b,
		];

		$response = wp_remote_post(
			"{$hub_url}/wp-json/fome/v1/bookings",
			[
				'headers'     => [
					'X-Fome-Key'   => $api_key,
					'Content-Type' => 'application/json',
				],
				'body'        => wp_json_encode( $payload ),
				'timeout'     => 15,
				'sslverify'   => true,
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 201 ) {
			// Queue for retry via WP-Cron
			self::queue_retry( $payload );
			return 0;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return (int) ( $data['booking_id'] ?? 0 );
	}

	private static function queue_retry( array $payload ): void {
		$queue = get_option( 'fome_booking_retry_queue', [] );
		$queue[] = [
			'payload'    => $payload,
			'attempts'   => 0,
			'next_retry' => time() + 60,
		];
		update_option( 'fome_booking_retry_queue', $queue, false );

		if ( ! wp_next_scheduled( 'fome_retry_booking_log' ) ) {
			wp_schedule_event( time() + 60, 'fome_five_minutes', 'fome_retry_booking_log' );
		}
	}

	// -------------------------------------------------------------------------
	// Email sending
	// -------------------------------------------------------------------------

	private static function send_email( string $subject, string $body, array $recipients ): void {
		if ( empty( $recipients ) ) return;

		// Override PHPMailer SMTP settings with plugin options
		add_action( 'phpmailer_init', [ __CLASS__, 'configure_smtp' ] );

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		foreach ( $recipients as $to ) {
			wp_mail( sanitize_email( $to ), $subject, $body, $headers );
		}

		remove_action( 'phpmailer_init', [ __CLASS__, 'configure_smtp' ] );
	}

	public static function configure_smtp( \PHPMailer\PHPMailer\PHPMailer $mailer ): void {
		$host = get_option( 'fome_smtp_host', '' );
		if ( ! $host ) return;

		$mailer->isSMTP();
		$mailer->Host       = $host;
		$mailer->SMTPAuth   = true;
		$mailer->Username   = get_option( 'fome_smtp_user', '' );
		$mailer->Password   = defined( 'FOME_SMTP_PASS' ) ? FOME_SMTP_PASS : get_option( 'fome_smtp_pass', '' );
		$mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		$mailer->Port       = (int) get_option( 'fome_smtp_port', 465 );
		$mailer->From       = $mailer->Username;
		$mailer->FromName   = get_bloginfo( 'name' );
	}

	// -------------------------------------------------------------------------
	// Stripe checkout session
	// -------------------------------------------------------------------------

	private static function create_checkout_session( int $booking_id, array $b, array $pricing, array $config ): ?string {
		$hub_url = rtrim( get_option( 'fome_hub_url', '' ), '/' );
		$api_key = get_option( 'fome_site_api_key', '' );
		$site_id = (int) get_option( 'fome_site_id', 0 );

		if ( ! $hub_url || ! $api_key ) return null;

		$payload = [
			'site_id'        => $site_id,
			'booking_id'     => $booking_id,
			'service'        => $b['service'],
			'is_subscription'=> self::is_subscription( $b['service'] ),
			'frequency'      => $b['frequency'] ?? null,
			'customer_email' => $b['email'],
			'customer_name'  => $b['name'],
			'success_url'    => home_url( '/booking-confirmed/' ),
			'cancel_url'     => home_url( '/booking/' ),
		];

		$response = wp_remote_post(
			"{$hub_url}/wp-json/fome/v1/stripe/checkout-session",
			[
				'headers'   => [
					'X-Fome-Key'   => $api_key,
					'Content-Type' => 'application/json',
				],
				'body'      => wp_json_encode( $payload ),
				'timeout'   => 20,
				'sslverify' => true,
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return null;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return $data['url'] ?? null;
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	private static function service_slug( string $service ): string {
		return sanitize_key( str_replace( ' ', '-', strtolower( $service ) ) );
	}

	private static function is_commercial( string $service, array $config ): bool {
		foreach ( $config['services'] ?? [] as $svc ) {
			if ( $svc['name'] === $service ) {
				return ( $svc['category'] ?? '' ) === 'commercial';
			}
		}
		return false;
	}

	private static function is_subscription( string $service ): bool {
		return str_contains( $service, 'Regular Cleaning' );
	}
}

// Register the cron interval and retry hook
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['fome_five_minutes'] = [
		'interval' => 300,
		'display'  => __( 'Every 5 minutes', 'fome-booking' ),
	];
	return $schedules;
} );

add_action( 'fome_retry_booking_log', function () {
	$queue = get_option( 'fome_booking_retry_queue', [] );
	if ( empty( $queue ) ) return;

	$hub_url = rtrim( get_option( 'fome_hub_url', '' ), '/' );
	$api_key = get_option( 'fome_site_api_key', '' );
	if ( ! $hub_url || ! $api_key ) return;

	$remaining = [];
	foreach ( $queue as $item ) {
		if ( $item['next_retry'] > time() ) {
			$remaining[] = $item;
			continue;
		}
		if ( $item['attempts'] >= 5 ) {
			continue; // drop after 5 attempts
		}

		$response = wp_remote_post(
			"{$hub_url}/wp-json/fome/v1/bookings",
			[
				'headers'   => [
					'X-Fome-Key'   => $api_key,
					'Content-Type' => 'application/json',
				],
				'body'      => wp_json_encode( $item['payload'] ),
				'timeout'   => 15,
				'sslverify' => true,
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 201 ) {
			$item['attempts']++;
			$item['next_retry'] = time() + ( 60 * pow( 2, $item['attempts'] ) );
			$remaining[] = $item;
		}
		// success → drop from queue
	}

	update_option( 'fome_booking_retry_queue', $remaining, false );

	if ( empty( $remaining ) ) {
		wp_clear_scheduled_hook( 'fome_retry_booking_log' );
	}
} );
