<?php
defined( 'ABSPATH' ) || exit;

class Fome_Booking_Settings {

	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_menu' ] );
		add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
	}

	public static function add_menu(): void {
		add_options_page(
			__( 'Fome Booking', 'fome-booking' ),
			__( 'Fome Booking', 'fome-booking' ),
			'manage_options',
			'fome-booking-settings',
			[ __CLASS__, 'render' ]
		);
	}

	public static function register_settings(): void {
		register_setting( 'fome_booking', 'fome_hub_url' );
		register_setting( 'fome_booking', 'fome_site_api_key' );
		register_setting( 'fome_booking', 'fome_site_id' );
		register_setting( 'fome_booking', 'fome_smtp_host' );
		register_setting( 'fome_booking', 'fome_smtp_port' );
		register_setting( 'fome_booking', 'fome_smtp_user' );
		register_setting( 'fome_booking', 'fome_smtp_pass' );
	}

	public static function render(): void {
		?>
		<div class="wrap">
			<h1><?php _e( 'Fome Booking Settings', 'fome-booking' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'fome_booking' ); ?>
				<table class="form-table">
					<tr><th><?php _e( 'Hub URL', 'fome-booking' ); ?></th>
						<td><input class="regular-text" type="url" name="fome_hub_url" value="<?php echo esc_attr( get_option( 'fome_hub_url', '' ) ); ?>" placeholder="https://bookings.fastklean.co.uk">
						<p class="description"><?php _e( 'URL of the central booking hub (no trailing slash)', 'fome-booking' ); ?></p></td></tr>
					<tr><th><?php _e( 'Site API key', 'fome-booking' ); ?></th>
						<td><input class="regular-text" type="password" name="fome_site_api_key" value="<?php echo esc_attr( get_option( 'fome_site_api_key', '' ) ); ?>">
						<p class="description"><?php _e( 'Generated in the hub — Sites → your site', 'fome-booking' ); ?></p></td></tr>
					<tr><th><?php _e( 'Site ID', 'fome-booking' ); ?></th>
						<td><input class="small-text" type="number" name="fome_site_id" value="<?php echo esc_attr( get_option( 'fome_site_id', '' ) ); ?>">
						<p class="description"><?php _e( 'Numeric ID from the hub', 'fome-booking' ); ?></p></td></tr>

					<tr><th colspan="2"><h2 style="margin:0;"><?php _e( 'SMTP (for booking emails)', 'fome-booking' ); ?></h2></th></tr>
					<tr><th><?php _e( 'SMTP host', 'fome-booking' ); ?></th>
						<td><input class="regular-text" type="text" name="fome_smtp_host" value="<?php echo esc_attr( get_option( 'fome_smtp_host', '' ) ); ?>"></td></tr>
					<tr><th><?php _e( 'SMTP port', 'fome-booking' ); ?></th>
						<td><input class="small-text" type="number" name="fome_smtp_port" value="<?php echo esc_attr( get_option( 'fome_smtp_port', '465' ) ); ?>"></td></tr>
					<tr><th><?php _e( 'SMTP username', 'fome-booking' ); ?></th>
						<td><input class="regular-text" type="text" name="fome_smtp_user" value="<?php echo esc_attr( get_option( 'fome_smtp_user', '' ) ); ?>"></td></tr>
					<tr><th><?php _e( 'SMTP password', 'fome-booking' ); ?></th>
						<td><input class="regular-text" type="password" name="fome_smtp_pass" value="<?php echo esc_attr( get_option( 'fome_smtp_pass', '' ) ); ?>">
						<p class="description"><?php _e( 'Stored in WP options. For production prefer storing in wp-config.php as FOME_SMTP_PASS constant.', 'fome-booking' ); ?></p></td></tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
