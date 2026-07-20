<?php
defined( 'ABSPATH' ) || exit;

class Fome_REST_API {

	const NS = 'fome/v1';

	public static function init(): void {
		add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
	}

	public static function register_routes(): void {
		// GET /fome/v1/sites/{site_id}/config
		register_rest_route( self::NS, '/sites/(?P<site_id>\d+)/config', [
			'methods'             => 'GET',
			'callback'            => [ __CLASS__, 'get_site_config' ],
			'permission_callback' => [ __CLASS__, 'auth' ],
		] );

		// POST /fome/v1/bookings
		register_rest_route( self::NS, '/bookings', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'create_booking' ],
			'permission_callback' => [ __CLASS__, 'auth' ],
		] );

		// POST /fome/v1/stripe/checkout-session
		register_rest_route( self::NS, '/stripe/checkout-session', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'create_stripe_session' ],
			'permission_callback' => [ __CLASS__, 'auth' ],
		] );

		// POST /fome/v1/stripe/webhook/{account_id}
		register_rest_route( self::NS, '/stripe/webhook/(?P<account_id>\d+)', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'stripe_webhook' ],
			'permission_callback' => '__return_true', // Stripe signs requests
		] );

		// POST /fome/v1/validate-postcode
		register_rest_route( self::NS, '/validate-postcode', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'validate_postcode' ],
			'permission_callback' => [ __CLASS__, 'auth' ],
		] );

		// POST /fome/v1/validate-discount-code
		register_rest_route( self::NS, '/validate-discount-code', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'validate_discount_code' ],
			'permission_callback' => [ __CLASS__, 'auth' ],
		] );

		// POST /fome/v1/validate-gift-card
		register_rest_route( self::NS, '/validate-gift-card', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'validate_gift_card' ],
			'permission_callback' => [ __CLASS__, 'auth' ],
		] );

		// POST /fome/v1/sites/{site_id}/test-email
		register_rest_route( self::NS, '/sites/(?P<site_id>\d+)/test-email', [
			'methods'             => 'POST',
			'callback'            => [ __CLASS__, 'send_test_email' ],
			'permission_callback' => [ __CLASS__, 'hub_admin_only' ],
		] );
	}

	public static function auth( \WP_REST_Request $request ): bool|\WP_Error {
		$site = Fome_API_Keys::authenticate( $request );
		if ( ! $site ) {
			return new \WP_Error( 'unauthorized', 'Invalid or missing API key', [ 'status' => 401 ] );
		}
		$request->set_param( '_authenticated_site', $site );
		return true;
	}

	public static function hub_admin_only(): bool {
		return current_user_can( 'manage_options' );
	}

	// ----------------------------------------------------------------
	// GET /fome/v1/sites/{site_id}/config
	// ----------------------------------------------------------------
	public static function get_site_config( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		global $wpdb;
		$p       = $wpdb->prefix;
		$site    = $request->get_param( '_authenticated_site' );
		$site_id = (int) $site['id'];

		// Services enabled for this site
		$services = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT s.service_key, s.name, s.category, s.sort_order, s.form_schema, s.payment_type
				 FROM {$p}fome_services s
				 JOIN {$p}fome_site_services ss ON ss.service_id = s.id
				 WHERE ss.site_id = %d AND ss.enabled = 1
				 ORDER BY s.category, s.sort_order",
				$site_id
			),
			ARRAY_A
		);

		$calculator = Fome_Price_Calculator::from_db();
		$settings   = $wpdb->get_results( "SELECT setting_key, setting_value FROM {$p}fome_settings", ARRAY_A );

		// Postcode outcodes
		$postcodes = $wpdb->get_col( "SELECT outcode FROM {$p}fome_postcodes" );

		// Discount codes (just codes and multipliers — no balances)
		$discount_codes = $wpdb->get_results(
			"SELECT code, multiplier FROM {$p}fome_discount_codes WHERE status = 'active'",
			ARRAY_A
		);

		$response = [
			'site'           => [
				'id'               => $site_id,
				'name'             => $site['name'],
				'domain'           => $site['domain'],
				'brand_colour'     => $site['brand_colour'],
				'from_email'       => $site['from_email'],
				'from_name'        => $site['from_name'],
				'footer_site_name' => $site['footer_site_name'],
				'footer_site_url'  => $site['footer_site_url'],
				'recipient_emails' => json_decode( $site['recipient_emails'], true ) ?? [],
				'stripe_success_url'=> $site['stripe_success_url'],
				'stripe_cancel_url' => $site['stripe_cancel_url'],
			],
			'services'       => $services,
			'pricing'        => $calculator->export(),
			'settings'       => array_column( $settings, 'setting_value', 'setting_key' ),
			'postcodes'      => $postcodes,
			'discount_codes' => $discount_codes,
		];

		return new \WP_REST_Response( $response, 200 );
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/bookings
	// ----------------------------------------------------------------
	public static function create_booking( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		global $wpdb;
		$p    = $wpdb->prefix;
		$site = $request->get_param( '_authenticated_site' );
		$data = $request->get_json_params();

		$wpdb->insert( "{$p}fome_bookings", [
			'site_id'          => (int) $site['id'],
			'service'          => sanitize_text_field( $data['service'] ?? '' ),
			'customer_name'    => sanitize_text_field( $data['name'] ?? '' ),
			'email'            => sanitize_email( $data['email'] ?? '' ),
			'phone'            => sanitize_text_field( $data['phone'] ?? '' ),
			'postcode'         => sanitize_text_field( $data['postcode'] ?? '' ),
			'address'          => sanitize_text_field( $data['address'] ?? '' ),
			'booking_date'     => sanitize_text_field( $data['date'] ?? '' ),
			'booking_time'     => sanitize_text_field( $data['time'] ?? '' ),
			'price'            => (float) ( $data['price'] ?? 0 ),
			'original_price'   => isset( $data['original_price'] ) ? (float) $data['original_price'] : null,
			'discount_code'    => sanitize_text_field( $data['discount_code'] ?? '' ),
			'gift_card'        => sanitize_text_field( $data['gift_card'] ?? '' ),
			'payment_status'   => sanitize_key( $data['payment_status'] ?? 'pending' ),
			'stripe_session_id'=> sanitize_text_field( $data['stripe_session_id'] ?? '' ),
			'raw_email_body'   => wp_kses_post( $data['raw_email_body'] ?? '' ),
		] );

		if ( ! $wpdb->insert_id ) {
			return new \WP_Error( 'db_error', 'Could not save booking', [ 'status' => 500 ] );
		}

		return new \WP_REST_Response( [ 'id' => $wpdb->insert_id ], 201 );
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/stripe/checkout-session
	// ----------------------------------------------------------------
	public static function create_stripe_session( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		global $wpdb;
		$p    = $wpdb->prefix;
		$site = $request->get_param( '_authenticated_site' );
		$data = $request->get_json_params();

		$booking_id = (int) ( $data['booking_id'] ?? 0 );
		if ( ! $booking_id ) {
			return new \WP_Error( 'invalid', 'booking_id required', [ 'status' => 400 ] );
		}

		// Load the authoritative price from the booking log (never trust client)
		$booking = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$p}fome_bookings WHERE id = %d AND site_id = %d", $booking_id, $site['id'] ),
			ARRAY_A
		);
		if ( ! $booking ) {
			return new \WP_Error( 'not_found', 'Booking not found', [ 'status' => 404 ] );
		}

		// Load site's Stripe account
		$stripe_acc = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$p}fome_stripe_accounts WHERE id = %d", $site['stripe_account_id'] ?? 0 ),
			ARRAY_A
		);
		if ( ! $stripe_acc ) {
			return new \WP_Error( 'no_stripe', 'No Stripe account configured for this site', [ 'status' => 500 ] );
		}

		try {
			$secret_key = Fome_Encryption::decrypt( $stripe_acc['secret_key_enc'] );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'encryption', 'Stripe key decryption failed', [ 'status' => 500 ] );
		}

		// Amount in pence (Stripe uses smallest unit)
		$amount_pence = (int) round( $booking['price'] * 100 );
		$service      = $booking['service'];
		$is_subscription = in_array( $service, [ 'Regular Cleaning', 'Regular Cleaning Luxury' ], true )
		                   && isset( $data['how_often'] )
		                   && in_array( $data['how_often'], [ 'every-week', 'every-2-weeks', 'frequently' ], true );

		// Call Stripe API
		$stripe_response = self::call_stripe_checkout(
			$secret_key,
			$amount_pence,
			$service,
			$booking_id,
			$site['stripe_success_url'],
			$site['stripe_cancel_url'],
			$is_subscription,
			$data['how_often'] ?? 'one-off'
		);

		if ( is_wp_error( $stripe_response ) ) {
			return $stripe_response;
		}

		// Update booking with session ID
		$wpdb->update(
			"{$p}fome_bookings",
			[ 'stripe_session_id' => $stripe_response['id'] ],
			[ 'id' => $booking_id ]
		);

		return new \WP_REST_Response( [ 'session_id' => $stripe_response['id'] ], 200 );
	}

	private static function call_stripe_checkout(
		string $secret_key,
		int $amount_pence,
		string $service,
		int $booking_id,
		string $success_url,
		string $cancel_url,
		bool $is_subscription,
		string $how_often
	): array|\WP_Error {

		if ( $is_subscription ) {
			$interval       = $how_often === 'every-2-weeks' ? 'every-2-weeks' : 'weekly';
			$stripe_interval = $interval === 'every-2-weeks' ? [ 'interval' => 'week', 'interval_count' => 2 ] : [ 'interval' => 'week', 'interval_count' => 1 ];
			$params = [
				'payment_method_types' => [ 'card' ],
				'mode'                 => 'subscription',
				'line_items'           => [ [
					'price_data' => [
						'currency'    => 'gbp',
						'unit_amount' => $amount_pence,
						'recurring'   => $stripe_interval,
						'product_data'=> [ 'name' => $service ],
					],
					'quantity' => 1,
				] ],
				'success_url'          => add_query_arg( [ 'booking_id' => $booking_id, 'session_id' => '{CHECKOUT_SESSION_ID}' ], $success_url ),
				'cancel_url'           => $cancel_url,
				'metadata'             => [ 'booking_id' => $booking_id ],
			];
		} else {
			$params = [
				'payment_method_types' => [ 'card' ],
				'mode'                 => 'payment',
				'line_items'           => [ [
					'price_data' => [
						'currency'    => 'gbp',
						'unit_amount' => $amount_pence,
						'product_data'=> [ 'name' => $service ],
					],
					'quantity' => 1,
				] ],
				'success_url'          => add_query_arg( [ 'booking_id' => $booking_id, 'session_id' => '{CHECKOUT_SESSION_ID}' ], $success_url ),
				'cancel_url'           => $cancel_url,
				'metadata'             => [ 'booking_id' => $booking_id ],
			];
		}

		$response = wp_remote_post( 'https://api.stripe.com/v1/checkout/sessions', [
			'headers' => [
				'Authorization' => 'Bearer ' . $secret_key,
				'Content-Type'  => 'application/x-www-form-urlencoded',
			],
			'body'    => self::stripe_flatten( $params ),
			'timeout' => 15,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'stripe_error', $body['error']['message'], [ 'status' => 502 ] );
		}
		return $body;
	}

	private static function stripe_flatten( array $params, string $prefix = '' ): array {
		$flat = [];
		foreach ( $params as $k => $v ) {
			$key = $prefix ? "{$prefix}[{$k}]" : $k;
			if ( is_array( $v ) ) {
				$flat = array_merge( $flat, self::stripe_flatten( $v, $key ) );
			} else {
				$flat[ $key ] = $v;
			}
		}
		return $flat;
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/stripe/webhook/{account_id}
	// ----------------------------------------------------------------
	public static function stripe_webhook( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		global $wpdb;
		$p          = $wpdb->prefix;
		$account_id = (int) $request->get_param( 'account_id' );

		$stripe_acc = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$p}fome_stripe_accounts WHERE id = %d", $account_id ),
			ARRAY_A
		);
		if ( ! $stripe_acc ) {
			return new \WP_Error( 'not_found', 'Stripe account not found', [ 'status' => 404 ] );
		}

		try {
			$webhook_secret = Fome_Encryption::decrypt( $stripe_acc['webhook_secret_enc'] );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'encryption', 'Webhook secret decryption failed', [ 'status' => 500 ] );
		}

		$payload   = $request->get_body();
		$sig       = $request->get_header( 'Stripe-Signature' );
		$tolerance = 300;

		// Verify Stripe signature
		if ( ! self::verify_stripe_signature( $payload, $sig, $webhook_secret, $tolerance ) ) {
			return new \WP_Error( 'invalid_signature', 'Webhook signature verification failed', [ 'status' => 400 ] );
		}

		$event = json_decode( $payload, true );
		$type  = $event['type'] ?? '';

		$session = $event['data']['object'] ?? [];
		$session_id = $session['id'] ?? '';
		$booking_id = (int) ( $session['metadata']['booking_id'] ?? 0 );

		if ( ! $booking_id ) {
			return new \WP_REST_Response( [ 'ok' => true ], 200 );
		}

		$new_status = match ( $type ) {
			'checkout.session.completed' => ( $session['mode'] ?? '' ) === 'subscription' ? 'subscription_active' : 'paid',
			'invoice.paid'               => 'paid',
			'payment_intent.payment_failed', 'checkout.session.expired' => 'failed',
			default                       => null,
		};

		if ( $new_status ) {
			$wpdb->update(
				"{$p}fome_bookings",
				[ 'payment_status' => $new_status, 'stripe_session_id' => $session_id ],
				[ 'id' => $booking_id ]
			);
		}

		return new \WP_REST_Response( [ 'ok' => true ], 200 );
	}

	private static function verify_stripe_signature( string $payload, string $sig_header, string $secret, int $tolerance ): bool {
		$parts     = explode( ',', $sig_header );
		$timestamp = null;
		$v1        = null;
		foreach ( $parts as $part ) {
			[ $k, $v ] = array_pad( explode( '=', $part, 2 ), 2, '' );
			if ( $k === 't' ) $timestamp = (int) $v;
			if ( $k === 'v1' ) $v1 = $v;
		}
		if ( ! $timestamp || ! $v1 ) return false;
		if ( abs( time() - $timestamp ) > $tolerance ) return false;
		$signed_payload = $timestamp . '.' . $payload;
		$expected       = hash_hmac( 'sha256', $signed_payload, $secret );
		return hash_equals( $expected, $v1 );
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/validate-postcode
	// ----------------------------------------------------------------
	public static function validate_postcode( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$p        = $wpdb->prefix;
		$postcode = strtoupper( trim( $request->get_param( 'postcode' ) ?? '' ) );

		if ( ! preg_match( '/^([A-Z]{1,2}\d[A-Z\d]?)\s*(\d[A-Z]{2})$/i', $postcode, $m ) ) {
			return new \WP_REST_Response( [ 'valid' => false, 'covered' => false ], 200 );
		}

		$outcode  = preg_replace( '/\d[A-Z]{2}$/', '', $postcode );
		$letters  = preg_replace( '/\d.*$/', '', $outcode );
		$covered  = (bool) $wpdb->get_var(
			$wpdb->prepare( "SELECT id FROM {$p}fome_postcodes WHERE outcode = %s", $letters )
		);

		return new \WP_REST_Response( [ 'valid' => true, 'covered' => $covered ], 200 );
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/validate-discount-code
	// ----------------------------------------------------------------
	public static function validate_discount_code( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$p    = $wpdb->prefix;
		$code = strtoupper( trim( $request->get_param( 'code' ) ?? '' ) );

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT multiplier FROM {$p}fome_discount_codes WHERE code = %s AND status = 'active'", $code ),
			ARRAY_A
		);

		if ( $row ) {
			return new \WP_REST_Response( [ 'valid' => true, 'multiplier' => (float) $row['multiplier'] ], 200 );
		}
		return new \WP_REST_Response( [ 'valid' => false ], 200 );
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/validate-gift-card
	// ----------------------------------------------------------------
	public static function validate_gift_card( \WP_REST_Request $request ): \WP_REST_Response {
		global $wpdb;
		$p    = $wpdb->prefix;
		$code = strtoupper( trim( $request->get_param( 'code' ) ?? '' ) );

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT balance FROM {$p}fome_gift_cards WHERE code = %s AND status = 'active' AND balance > 0", $code ),
			ARRAY_A
		);

		if ( $row ) {
			return new \WP_REST_Response( [ 'valid' => true, 'balance' => (float) $row['balance'] ], 200 );
		}
		return new \WP_REST_Response( [ 'valid' => false ], 200 );
	}

	// ----------------------------------------------------------------
	// POST /fome/v1/sites/{site_id}/test-email
	// ----------------------------------------------------------------
	public static function send_test_email( \WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		global $wpdb;
		$p       = $wpdb->prefix;
		$site_id = (int) $request->get_param( 'site_id' );

		$site = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$p}fome_sites WHERE id = %d", $site_id ),
			ARRAY_A
		);
		if ( ! $site ) {
			return new \WP_Error( 'not_found', 'Site not found', [ 'status' => 404 ] );
		}

		require_once plugin_dir_path( __DIR__ ) . '/../shared/email-contract/EmailRenderer.php';

		$sample = [
			'service'       => 'Regular Cleaning',
			'name'          => 'Test User',
			'phone'         => '07700 900000',
			'email'         => 'test@example.com',
			'postcode'      => 'SW1A 1AA',
			'calling-hours' => '9am-5pm',
			'address'       => '10 Test Street, London',
			'note'          => '-',
			'date'          => date( 'd/m/Y', strtotime( '+1 day' ) ),
			'price'         => '48.00',
			'vat_enabled'   => true,
			'how-often'     => 'every-week',
			'cleaning-hours'=> '2',
			'have-pets'     => null,
		];

		$body    = render_booking_email( $sample );
		$footer  = render_booking_footer( $site['footer_site_name'], $site['footer_site_url'] );
		$subject = render_booking_subject( 'Regular Cleaning' );

		$recipients = json_decode( $site['recipient_emails'], true ) ?? [];
		if ( empty( $recipients ) ) {
			return new \WP_Error( 'no_recipients', 'No recipient emails configured for this site', [ 'status' => 400 ] );
		}

		add_filter( 'wp_mail_content_type', fn() => 'text/html' );
		$sent = wp_mail( $recipients, '[TEST] ' . $subject, $body . $footer, [
			'From: ' . $site['from_name'] . ' <' . $site['from_email'] . '>',
		] );
		remove_all_filters( 'wp_mail_content_type' );

		if ( $sent ) {
			return new \WP_REST_Response( [ 'sent' => true ], 200 );
		}
		return new \WP_Error( 'mail_failed', 'Test email failed to send', [ 'status' => 500 ] );
	}
}
