<?php
defined( 'ABSPATH' ) || exit;

class Fome_Booking_Shortcode {

	public static function init(): void {
		add_shortcode( 'fome_booking_form', [ __CLASS__, 'render' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		Fome_Booking_Submission_Handler::init();
	}

	public static function enqueue_assets(): void {
		// Only load on pages that use the shortcode or block
		if ( ! self::page_has_form() ) return;

		$ver = FOME_BOOKING_VERSION;
		wp_enqueue_style(
			'fome-booking-form',
			FOME_BOOKING_URL . 'assets/css/form.css',
			[],
			$ver
		);
		wp_enqueue_script(
			'fome-booking-form',
			FOME_BOOKING_URL . 'assets/js/form.js',
			[],
			$ver,
			true
		);

		// Config from hub (brand color, services list, etc.)
		$config = Fome_Booking_Config::get();
		$brand_color = $config['site']['brand_color'] ?? '#4578b4';
		$services    = array_map(
			fn( $s ) => [ 'name' => $s['name'], 'category' => $s['category'] ?? 'domestic' ],
			$config['services'] ?? []
		);

		wp_localize_script( 'fome-booking-form', 'fomeBooking', [
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'fome_booking_nonce' ),
			'brandColor'    => $brand_color,
			'services'      => $services,
			'prices'        => $config['prices'] ?? [],
			'settings'      => [
				'vat_enabled'               => $config['settings']['vat_enabled']               ?? false,
				'online_discount_multiplier' => $config['settings']['online_discount_multiplier'] ?? 1.0,
			],
			'vatRate'       => 1.2,
			'discountCodes' => array_values( array_map(
				fn( $dc ) => [ 'code' => $dc['code'], 'multiplier' => $dc['multiplier'], 'status' => $dc['status'] ],
				$config['discount_codes'] ?? []
			) ),
			'giftCards'     => array_values( array_map(
				fn( $gc ) => [ 'code' => $gc['code'], 'balance' => $gc['balance'], 'status' => $gc['status'] ],
				$config['gift_cards'] ?? []
			) ),
		] );
	}

	public static function render( $atts = [] ): string {
		$config = Fome_Booking_Config::get();
		if ( ! $config ) {
			return '<p class="fome-notice">' . esc_html__( 'Booking is temporarily unavailable. Please try again shortly.', 'fome-booking' ) . '</p>';
		}

		$brand_color = $config['site']['brand_color'] ?? '#4578b4';
		$services    = $config['services'] ?? [];

		ob_start();
		include FOME_BOOKING_DIR . 'templates/form.php';
		return ob_get_clean();
	}

	private static function page_has_form(): bool {
		global $post;
		if ( ! $post ) return false;
		if ( has_shortcode( $post->post_content, 'fome_booking_form' ) ) return true;
		// Gutenberg block check
		if ( has_block( 'fome/booking-form', $post ) ) return true;
		return false;
	}
}
