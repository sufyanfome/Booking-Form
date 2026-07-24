<?php
defined( 'ABSPATH' ) || exit;

require_once FOME_HUB_DIR . 'admin/class-admin-sites.php';
require_once FOME_HUB_DIR . 'admin/class-admin-prices.php';
require_once FOME_HUB_DIR . 'admin/class-admin-services.php';
require_once FOME_HUB_DIR . 'admin/class-admin-bookings.php';
require_once FOME_HUB_DIR . 'admin/class-admin-stripe.php';
require_once FOME_HUB_DIR . 'admin/class-admin-postcodes.php';
require_once FOME_HUB_DIR . 'admin/class-admin-gift-cards.php';
require_once FOME_HUB_DIR . 'admin/class-admin-plugin-releases.php';

class Fome_Hub_Admin {

	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'register_menus' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( FOME_HUB_FILE ), [ __CLASS__, 'plugin_links' ] );

		Fome_Admin_Sites::init();
		Fome_Admin_Prices::init();
		Fome_Admin_Services::init();
		Fome_Admin_Bookings::init();
		Fome_Admin_Stripe::init();
		Fome_Admin_Postcodes::init();
		Fome_Admin_GiftCards::init();
		Fome_Admin_Plugin_Releases::init();
	}

	public static function register_menus(): void {
		$cap = 'manage_fome_bookings';

		add_menu_page(
			__( 'Fome Bookings', 'fome-hub' ),
			__( 'Fome Bookings', 'fome-hub' ),
			$cap,
			'fome-hub',
			[ __CLASS__, 'render_dashboard' ],
			'dashicons-calendar-alt',
			25
		);

		add_submenu_page( 'fome-hub', __( 'Dashboard', 'fome-hub' ),    __( 'Dashboard', 'fome-hub' ),    $cap, 'fome-hub',             [ __CLASS__, 'render_dashboard' ] );
		add_submenu_page( 'fome-hub', __( 'Bookings', 'fome-hub' ),     __( 'Bookings', 'fome-hub' ),     $cap, 'fome-bookings',        [ 'Fome_Admin_Bookings', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Prices', 'fome-hub' ),       __( 'Prices', 'fome-hub' ),       $cap, 'fome-prices',          [ 'Fome_Admin_Prices', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Services', 'fome-hub' ),     __( 'Services', 'fome-hub' ),     $cap, 'fome-services',        [ 'Fome_Admin_Services', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Sites', 'fome-hub' ),        __( 'Sites', 'fome-hub' ),        $cap, 'fome-sites',           [ 'Fome_Admin_Sites', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Stripe Accounts', 'fome-hub' ), __( 'Stripe Accounts', 'fome-hub' ), $cap, 'fome-stripe',   [ 'Fome_Admin_Stripe', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Postcodes', 'fome-hub' ),    __( 'Postcodes', 'fome-hub' ),    $cap, 'fome-postcodes',       [ 'Fome_Admin_Postcodes', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Gift Cards & Codes', 'fome-hub' ), __( 'Gift Cards & Codes', 'fome-hub' ), $cap, 'fome-gift-cards', [ 'Fome_Admin_GiftCards', 'render' ] );
		add_submenu_page( 'fome-hub', __( 'Plugin Releases', 'fome-hub' ), __( 'Plugin Releases', 'fome-hub' ), $cap, 'fome-plugin-releases', [ 'Fome_Admin_Plugin_Releases', 'render' ] );
	}

	public static function render_dashboard(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$bookings_today = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$p}fome_bookings WHERE DATE(created_at) = CURDATE()"
		);
		$bookings_week  = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$p}fome_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
		);
		$recent         = $wpdb->get_results(
			"SELECT b.*, s.name as site_name FROM {$p}fome_bookings b
			 LEFT JOIN {$p}fome_sites s ON s.id = b.site_id
			 ORDER BY b.created_at DESC LIMIT 10",
			ARRAY_A
		);
		$total_sites    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$p}fome_sites WHERE status = 'active'" );
		?>
		<div class="wrap">
			<h1><?php _e( 'Fome Bookings Dashboard', 'fome-hub' ); ?></h1>
			<div class="fome-dashboard-stats" style="display:flex;gap:20px;margin:20px 0;">
				<div class="postbox" style="padding:20px;text-align:center;flex:1;">
					<h2 style="font-size:36px;margin:0;"><?php echo esc_html( $bookings_today ); ?></h2>
					<p><?php _e( 'Bookings today', 'fome-hub' ); ?></p>
				</div>
				<div class="postbox" style="padding:20px;text-align:center;flex:1;">
					<h2 style="font-size:36px;margin:0;"><?php echo esc_html( $bookings_week ); ?></h2>
					<p><?php _e( 'Bookings this week', 'fome-hub' ); ?></p>
				</div>
				<div class="postbox" style="padding:20px;text-align:center;flex:1;">
					<h2 style="font-size:36px;margin:0;"><?php echo esc_html( $total_sites ); ?></h2>
					<p><?php _e( 'Active sites', 'fome-hub' ); ?></p>
				</div>
			</div>

			<h2><?php _e( 'Recent bookings', 'fome-hub' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead><tr>
					<th><?php _e( 'ID', 'fome-hub' ); ?></th>
					<th><?php _e( 'Site', 'fome-hub' ); ?></th>
					<th><?php _e( 'Service', 'fome-hub' ); ?></th>
					<th><?php _e( 'Customer', 'fome-hub' ); ?></th>
					<th><?php _e( 'Price', 'fome-hub' ); ?></th>
					<th><?php _e( 'Status', 'fome-hub' ); ?></th>
					<th><?php _e( 'Date', 'fome-hub' ); ?></th>
				</tr></thead>
				<tbody>
				<?php foreach ( $recent as $b ) : ?>
					<tr>
						<td><?php echo esc_html( $b['id'] ); ?></td>
						<td><?php echo esc_html( $b['site_name'] ); ?></td>
						<td><?php echo esc_html( $b['service'] ); ?></td>
						<td><?php echo esc_html( $b['customer_name'] . ' / ' . $b['email'] ); ?></td>
						<td>£<?php echo esc_html( number_format( $b['price'], 2 ) ); ?></td>
						<td><span class="fome-status-<?php echo esc_attr( $b['payment_status'] ); ?>"><?php echo esc_html( $b['payment_status'] ); ?></span></td>
						<td><?php echo esc_html( $b['created_at'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public static function enqueue_assets( string $hook ): void {
		if ( strpos( $hook, 'fome' ) === false ) return;
		wp_enqueue_style( 'fome-hub-admin', FOME_HUB_URL . 'assets/css/admin.css', [], FOME_HUB_VERSION );
		wp_enqueue_script( 'fome-hub-admin', FOME_HUB_URL . 'assets/js/admin.js', [ 'jquery', 'wp-color-picker' ], FOME_HUB_VERSION, true );
		wp_enqueue_style( 'wp-color-picker' );
	}

	public static function plugin_links( array $links ): array {
		array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=fome-hub' ) . '">' . __( 'Dashboard', 'fome-hub' ) . '</a>' );
		return $links;
	}
}
