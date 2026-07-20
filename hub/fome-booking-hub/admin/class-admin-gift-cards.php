<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_GiftCards {

	public static function init(): void {
		add_action( 'admin_post_fome_save_gift_card', [ __CLASS__, 'handle_save_gift_card' ] );
		add_action( 'admin_post_fome_save_discount_code', [ __CLASS__, 'handle_save_discount_code' ] );
		add_action( 'admin_post_fome_delete_gift_card', [ __CLASS__, 'handle_delete_gift_card' ] );
		add_action( 'admin_post_fome_delete_discount_code', [ __CLASS__, 'handle_delete_discount_code' ] );
	}

	public static function render(): void {
		global $wpdb;
		$p      = $wpdb->prefix;
		$tab    = sanitize_key( $_GET['tab'] ?? 'gift-cards' );
		$notice = sanitize_key( $_GET['notice'] ?? '' );

		echo '<div class="wrap"><h1>' . esc_html__( 'Gift Cards & Discount Codes', 'fome-hub' ) . '</h1>';

		if ( $notice === 'saved' ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Saved.', 'fome-hub' ) . '</p></div>';
		}

		$tabs = [
			'gift-cards'     => __( 'Gift cards', 'fome-hub' ),
			'discount-codes' => __( 'Discount codes', 'fome-hub' ),
		];
		echo '<nav class="nav-tab-wrapper">';
		foreach ( $tabs as $t => $label ) {
			$url = admin_url( 'admin.php?page=fome-gift-cards&tab=' . $t );
			echo '<a href="' . esc_url( $url ) . '" class="nav-tab' . ( $tab === $t ? ' nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
		}
		echo '</nav>';

		if ( $tab === 'gift-cards' ) {
			self::render_gift_cards();
		} else {
			self::render_discount_codes();
		}

		echo '</div>';
	}

	private static function render_gift_cards(): void {
		global $wpdb;
		$p     = $wpdb->prefix;
		$cards = $wpdb->get_results( "SELECT * FROM {$p}fome_gift_cards ORDER BY created_at DESC", ARRAY_A );
		?>
		<h2 style="margin-top:16px;"><?php _e( 'Gift cards', 'fome-hub' ); ?></h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:20px;">
			<?php wp_nonce_field( 'fome_save_gift_card' ); ?>
			<input type="hidden" name="action" value="fome_save_gift_card">
			<table class="form-table" style="max-width:500px;">
				<tr><th><?php _e( 'Code', 'fome-hub' ); ?></th>
					<td><input type="text" name="code" class="regular-text" required placeholder="GIFT100"></td></tr>
				<tr><th><?php _e( 'Balance (£)', 'fome-hub' ); ?></th>
					<td><input type="number" step="0.01" min="0" name="balance" class="regular-text" required></td></tr>
			</table>
			<?php submit_button( __( 'Add gift card', 'fome-hub' ), 'secondary' ); ?>
		</form>

		<table class="wp-list-table widefat fixed striped">
			<thead><tr>
				<th><?php _e( 'Code', 'fome-hub' ); ?></th>
				<th><?php _e( 'Balance', 'fome-hub' ); ?></th>
				<th><?php _e( 'Status', 'fome-hub' ); ?></th>
				<th><?php _e( 'Created', 'fome-hub' ); ?></th>
				<th><?php _e( 'Actions', 'fome-hub' ); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ( $cards as $c ) : ?>
				<tr>
					<td><code><?php echo esc_html( $c['code'] ); ?></code></td>
					<td>£<?php echo esc_html( number_format( $c['balance'], 2 ) ); ?></td>
					<td><?php echo esc_html( $c['status'] ); ?></td>
					<td><?php echo esc_html( $c['created_at'] ); ?></td>
					<td><a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=fome_delete_gift_card&id=' . $c['id'] ), 'fome_del_gc_' . $c['id'] ) ); ?>" onclick="return confirm('Delete?')" style="color:red;"><?php _e( 'Delete', 'fome-hub' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	private static function render_discount_codes(): void {
		global $wpdb;
		$p     = $wpdb->prefix;
		$codes = $wpdb->get_results( "SELECT * FROM {$p}fome_discount_codes ORDER BY created_at DESC", ARRAY_A );
		?>
		<h2 style="margin-top:16px;"><?php _e( 'Discount codes', 'fome-hub' ); ?></h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:20px;">
			<?php wp_nonce_field( 'fome_save_discount_code' ); ?>
			<input type="hidden" name="action" value="fome_save_discount_code">
			<table class="form-table" style="max-width:500px;">
				<tr><th><?php _e( 'Code', 'fome-hub' ); ?></th>
					<td><input type="text" name="code" class="regular-text" required placeholder="FRIEND10"></td></tr>
				<tr><th><?php _e( 'Discount multiplier', 'fome-hub' ); ?></th>
					<td><input type="number" step="0.0001" min="0" max="1" name="multiplier" class="regular-text" required placeholder="0.9">
					<p class="description"><?php _e( 'e.g. 0.9 = 10% off, 0.8 = 20% off', 'fome-hub' ); ?></p></td></tr>
			</table>
			<?php submit_button( __( 'Add discount code', 'fome-hub' ), 'secondary' ); ?>
		</form>

		<table class="wp-list-table widefat fixed striped">
			<thead><tr>
				<th><?php _e( 'Code', 'fome-hub' ); ?></th>
				<th><?php _e( 'Multiplier', 'fome-hub' ); ?></th>
				<th><?php _e( 'Discount', 'fome-hub' ); ?></th>
				<th><?php _e( 'Status', 'fome-hub' ); ?></th>
				<th><?php _e( 'Actions', 'fome-hub' ); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ( $codes as $c ) : ?>
				<tr>
					<td><code><?php echo esc_html( $c['code'] ); ?></code></td>
					<td><?php echo esc_html( $c['multiplier'] ); ?></td>
					<td><?php echo esc_html( round( ( 1 - $c['multiplier'] ) * 100 ) . '%' ); ?></td>
					<td><?php echo esc_html( $c['status'] ); ?></td>
					<td><a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=fome_delete_discount_code&id=' . $c['id'] ), 'fome_del_dc_' . $c['id'] ) ); ?>" onclick="return confirm('Delete?')" style="color:red;"><?php _e( 'Delete', 'fome-hub' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	public static function handle_save_gift_card(): void {
		check_admin_referer( 'fome_save_gift_card' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'fome_gift_cards', [
			'code'    => strtoupper( sanitize_text_field( $_POST['code'] ?? '' ) ),
			'balance' => (float) ( $_POST['balance'] ?? 0 ),
			'status'  => 'active',
		] );
		wp_redirect( admin_url( 'admin.php?page=fome-gift-cards&tab=gift-cards&notice=saved' ) );
		exit;
	}

	public static function handle_save_discount_code(): void {
		check_admin_referer( 'fome_save_discount_code' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'fome_discount_codes', [
			'code'       => strtoupper( sanitize_text_field( $_POST['code'] ?? '' ) ),
			'multiplier' => max( 0, min( 1, (float) ( $_POST['multiplier'] ?? 1 ) ) ),
			'status'     => 'active',
		] );
		wp_redirect( admin_url( 'admin.php?page=fome-gift-cards&tab=discount-codes&notice=saved' ) );
		exit;
	}

	public static function handle_delete_gift_card(): void {
		$id = (int) ( $_GET['id'] ?? 0 );
		check_admin_referer( 'fome_del_gc_' . $id );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'fome_gift_cards', [ 'id' => $id ] );
		wp_redirect( admin_url( 'admin.php?page=fome-gift-cards&tab=gift-cards&notice=saved' ) );
		exit;
	}

	public static function handle_delete_discount_code(): void {
		$id = (int) ( $_GET['id'] ?? 0 );
		check_admin_referer( 'fome_del_dc_' . $id );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'fome_discount_codes', [ 'id' => $id ] );
		wp_redirect( admin_url( 'admin.php?page=fome-gift-cards&tab=discount-codes&notice=saved' ) );
		exit;
	}
}
