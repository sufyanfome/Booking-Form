<?php
/**
 * Plugin Name:  Fome Booking
 * Plugin URI:   https://bookings.fastklean.co.uk
 * Description:  Booking form for FastKlean sites. Connects to the central hub for config, pricing and booking logs.
 * Version:      1.0.0
 * Author:       Fome Agency
 * Text Domain:  fome-booking
 * Requires PHP: 8.2
 */

defined( 'ABSPATH' ) || exit;

define( 'FOME_BOOKING_VERSION', '1.0.0' );
define( 'FOME_BOOKING_FILE',    __FILE__ );
define( 'FOME_BOOKING_DIR',     plugin_dir_path( __FILE__ ) );
define( 'FOME_BOOKING_URL',     plugin_dir_url( __FILE__ ) );

require_once FOME_BOOKING_DIR . 'includes/class-config.php';
require_once FOME_BOOKING_DIR . 'includes/class-submission-handler.php';
require_once FOME_BOOKING_DIR . 'includes/class-shortcode.php';
require_once FOME_BOOKING_DIR . 'includes/class-settings.php';
require_once FOME_BOOKING_DIR . 'includes/class-updater.php';

add_action( 'plugins_loaded', function () {
	Fome_Booking_Settings::init();
	Fome_Booking_Shortcode::init();
	Fome_Booking_Updater::init();
} );

// Exclude booking page from caching (SiteGround Speed Optimizer compatibility)
add_action( 'init', function () {
	$booking_page = get_option( 'fome_booking_page_id' );
	if ( $booking_page && is_page( $booking_page ) ) {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) define( 'DONOTCACHEPAGE', true );
		if ( ! defined( 'SG_OPTIMIZER_EXCLUDE' ) ) define( 'SG_OPTIMIZER_EXCLUDE', true );
	}
} );
