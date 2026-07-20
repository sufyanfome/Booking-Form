<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_Stripe {

	public static function init(): void {
		add_action( 'admin_post_fome_save_stripe', [ __CLASS__, 'handle_save' ] );
		add_action( 'admin_post_fome_delete_stripe', [ __CLASS__, 'handle_delete' ] );
	}

	public static function render(): void {
		global $wpdb;
		$p      = $wpdb->prefix;
		$action = sanitize_key( $_GET['action'] ?? 'list' );
		$acc_id = (int) ( $_GET['acc_id'] ?? 0 );
		$notice = sanitize_key( $_GET['notice'] ?? '' );

		if ( $action === 'edit' || $action === 'new' ) {
			self::render_form( $acc_id );
			return;
		}

		$accounts = $wpdb->get_results( "SELECT id, label, publishable_key, created_at FROM {$p}fome_stripe_accounts ORDER BY label", ARRAY_A );
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Stripe Accounts', 'fome-hub' ); ?></h1>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fome-stripe&action=new' ) ); ?>" class="page-title-action"><?php _e( 'Add Account', 'fome-hub' ); ?></a>
			<?php if ( $notice === 'saved' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Stripe account saved.', 'fome-hub' ); ?></p></div>
			<?php elseif ( $notice === 'deleted' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Stripe account deleted.', 'fome-hub' ); ?></p></div>
			<?php endif; ?>

			<table class="wp-list-table widefat fixed striped">
				<thead><tr>
					<th><?php _e( 'Label', 'fome-hub' ); ?></th>
					<th><?php _e( 'Publishable key', 'fome-hub' ); ?></th>
					<th><?php _e( 'Webhook endpoint', 'fome-hub' ); ?></th>
					<th><?php _e( 'Actions', 'fome-hub' ); ?></th>
				</tr></thead>
				<tbody>
				<?php foreach ( $accounts as $acc ) : ?>
					<tr>
						<td><?php echo esc_html( $acc['label'] ); ?></td>
						<td><code><?php echo esc_html( substr( $acc['publishable_key'], 0, 20 ) . '…' ); ?></code></td>
						<td><code><?php echo esc_url( get_rest_url( null, 'fome/v1/stripe/webhook/' . $acc['id'] ) ); ?></code></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=fome-stripe&action=edit&acc_id=' . $acc['id'] ) ); ?>"><?php _e( 'Edit', 'fome-hub' ); ?></a>
							&nbsp;|&nbsp;
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=fome_delete_stripe&acc_id=' . $acc['id'] ), 'fome_del_stripe_' . $acc['id'] ) ); ?>" onclick="return confirm('Delete?')" style="color:red;"><?php _e( 'Delete', 'fome-hub' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private static function render_form( int $acc_id ): void {
		global $wpdb;
		$p   = $wpdb->prefix;
		$acc = $acc_id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}fome_stripe_accounts WHERE id = %d", $acc_id ), ARRAY_A ) : null;
		?>
		<div class="wrap">
			<h1><?php echo $acc ? __( 'Edit Stripe Account', 'fome-hub' ) : __( 'Add Stripe Account', 'fome-hub' ); ?></h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'fome_save_stripe' ); ?>
				<input type="hidden" name="action" value="fome_save_stripe">
				<input type="hidden" name="acc_id" value="<?php echo esc_attr( $acc_id ); ?>">
				<table class="form-table">
					<tr><th><?php _e( 'Label', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="text" name="label" value="<?php echo esc_attr( $acc['label'] ?? '' ); ?>" required></td></tr>
					<tr><th><?php _e( 'Publishable key', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="text" name="publishable_key" value="<?php echo esc_attr( $acc['publishable_key'] ?? '' ); ?>" required>
						<p class="description"><?php _e( 'Starts with pk_live_ or pk_test_', 'fome-hub' ); ?></p></td></tr>
					<tr><th><?php _e( 'Secret key', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="password" name="secret_key" placeholder="<?php echo $acc ? __( 'Leave blank to keep current', 'fome-hub' ) : ''; ?>" <?php echo ! $acc ? 'required' : ''; ?>>
						<p class="description"><?php _e( 'Encrypted at rest. Starts with sk_live_ or sk_test_', 'fome-hub' ); ?></p></td></tr>
					<tr><th><?php _e( 'Webhook secret', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="password" name="webhook_secret" placeholder="<?php echo $acc ? __( 'Leave blank to keep current', 'fome-hub' ) : ''; ?>" <?php echo ! $acc ? 'required' : ''; ?>>
						<p class="description"><?php _e( 'Encrypted at rest. Starts with whsec_', 'fome-hub' ); ?>
						<?php if ( $acc ) : ?>
							<br><?php _e( 'Webhook endpoint to register in Stripe dashboard:', 'fome-hub' ); ?>
							<code><?php echo esc_url( get_rest_url( null, 'fome/v1/stripe/webhook/' . $acc_id ) ); ?></code>
						<?php endif; ?></p></td></tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public static function handle_save(): void {
		check_admin_referer( 'fome_save_stripe' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p      = $wpdb->prefix;
		$acc_id = (int) ( $_POST['acc_id'] ?? 0 );

		$data = [
			'label'          => sanitize_text_field( $_POST['label'] ?? '' ),
			'publishable_key'=> sanitize_text_field( $_POST['publishable_key'] ?? '' ),
		];

		$secret = sanitize_text_field( $_POST['secret_key'] ?? '' );
		if ( $secret ) {
			$data['secret_key_enc'] = Fome_Encryption::encrypt( $secret );
		}
		$webhook = sanitize_text_field( $_POST['webhook_secret'] ?? '' );
		if ( $webhook ) {
			$data['webhook_secret_enc'] = Fome_Encryption::encrypt( $webhook );
		}

		if ( $acc_id ) {
			$wpdb->update( "{$p}fome_stripe_accounts", $data, [ 'id' => $acc_id ] );
		} else {
			$wpdb->insert( "{$p}fome_stripe_accounts", $data );
		}

		wp_redirect( admin_url( 'admin.php?page=fome-stripe&notice=saved' ) );
		exit;
	}

	public static function handle_delete(): void {
		$acc_id = (int) ( $_GET['acc_id'] ?? 0 );
		check_admin_referer( 'fome_del_stripe_' . $acc_id );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'fome_stripe_accounts', [ 'id' => $acc_id ] );
		wp_redirect( admin_url( 'admin.php?page=fome-stripe&notice=deleted' ) );
		exit;
	}
}
