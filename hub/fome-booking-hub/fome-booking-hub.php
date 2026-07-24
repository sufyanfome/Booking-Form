<?php
/**
 * Plugin Name:  Fome Booking Hub
 * Plugin URI:   https://bookings.fastklean.co.uk
 * Description:  Central management hub for the FastKlean multi-site booking platform.
 *               Controls prices, services, email recipients, themes, Stripe accounts and the booking log.
 * Version:      1.0.0
 * Author:       Fome Agency
 * Text Domain:  fome-hub
 * Requires PHP: 8.2
 */

defined( 'ABSPATH' ) || exit;

define( 'FOME_HUB_VERSION', '1.0.0' );
define( 'FOME_HUB_FILE',    __FILE__ );
define( 'FOME_HUB_DIR',     plugin_dir_path( __FILE__ ) );
define( 'FOME_HUB_URL',     plugin_dir_url( __FILE__ ) );

require_once FOME_HUB_DIR . 'includes/class-db-installer.php';
require_once FOME_HUB_DIR . 'includes/class-api-keys.php';
require_once FOME_HUB_DIR . 'includes/class-encryption.php';
require_once FOME_HUB_DIR . 'includes/class-price-calculator.php';
require_once FOME_HUB_DIR . 'api/class-rest-api.php';
require_once FOME_HUB_DIR . 'admin/class-admin.php';

register_activation_hook( __FILE__, [ 'Fome_DB_Installer', 'install' ] );

add_action( 'plugins_loaded', function () {
	Fome_REST_API::init();
	Fome_Hub_Admin::init();
} );

// Serve plugin zip downloads — must run early, before WordPress sends output
add_action( 'init', [ 'Fome_Admin_Plugin_Releases', 'handle_download_request' ], 1 );
