<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_Postcodes {

	public static function init(): void {
		add_action( 'admin_post_fome_save_postcodes', [ __CLASS__, 'handle_save' ] );
	}

	public static function render(): void {
		global $wpdb;
		$p      = $wpdb->prefix;
		$notice = sanitize_key( $_GET['notice'] ?? '' );
		$rows   = $wpdb->get_results( "SELECT id, outcode FROM {$p}fome_postcodes ORDER BY outcode", ARRAY_A );
		$list   = implode( "\n", array_column( $rows, 'outcode' ) );
		?>
		<div class="wrap">
			<h1><?php _e( 'Postcode Coverage', 'fome-hub' ); ?></h1>
			<p><?php _e( 'One outcode per line (e.g. SW, E, N, CR). This shared list is used by all sites to validate postcodes.', 'fome-hub' ); ?></p>
			<?php if ( $notice === 'saved' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Postcodes saved.', 'fome-hub' ); ?></p></div>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'fome_save_postcodes' ); ?>
				<input type="hidden" name="action" value="fome_save_postcodes">
				<textarea name="outcodes" rows="20" style="width:300px;font-family:monospace;"><?php echo esc_textarea( $list ); ?></textarea>
				<br>
				<?php submit_button( __( 'Save postcodes', 'fome-hub' ) ); ?>
			</form>
		</div>
		<?php
	}

	public static function handle_save(): void {
		check_admin_referer( 'fome_save_postcodes' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p = $wpdb->prefix;

		$raw      = sanitize_textarea_field( $_POST['outcodes'] ?? '' );
		$outcodes = array_values( array_unique( array_filter( array_map( fn( $l ) => strtoupper( trim( $l ) ), explode( "\n", $raw ) ) ) ) );

		$wpdb->query( "TRUNCATE TABLE {$p}fome_postcodes" );
		foreach ( $outcodes as $oc ) {
			$wpdb->insert( "{$p}fome_postcodes", [ 'outcode' => $oc ] );
		}

		wp_redirect( admin_url( 'admin.php?page=fome-postcodes&notice=saved' ) );
		exit;
	}
}
