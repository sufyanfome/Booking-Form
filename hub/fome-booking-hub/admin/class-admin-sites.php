<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_Sites {

	public static function init(): void {
		add_action( 'admin_post_fome_save_site', [ __CLASS__, 'handle_save' ] );
		add_action( 'admin_post_fome_delete_site', [ __CLASS__, 'handle_delete' ] );
		add_action( 'admin_post_fome_regenerate_key', [ __CLASS__, 'handle_regenerate_key' ] );
	}

	public static function render(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$action    = sanitize_key( $_GET['action'] ?? 'list' );
		$site_id   = (int) ( $_GET['site_id'] ?? 0 );
		$notice    = sanitize_text_field( $_GET['notice'] ?? '' );
		$new_key   = sanitize_text_field( $_GET['new_key'] ?? '' );

		if ( $action === 'edit' || $action === 'new' ) {
			self::render_form( $site_id );
			return;
		}

		// List view
		$sites = $wpdb->get_results( "SELECT * FROM {$p}fome_sites ORDER BY name", ARRAY_A );
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Sites', 'fome-hub' ); ?></h1>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fome-sites&action=new' ) ); ?>" class="page-title-action"><?php _e( 'Add New Site', 'fome-hub' ); ?></a>

			<?php if ( $notice === 'saved' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Site saved.', 'fome-hub' ); ?></p></div>
			<?php elseif ( $notice === 'deleted' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Site deleted.', 'fome-hub' ); ?></p></div>
			<?php endif; ?>

			<?php if ( $new_key ) : ?>
				<div class="notice notice-warning"><p><strong><?php _e( 'New API key generated (shown once only):', 'fome-hub' ); ?></strong><br><code><?php echo esc_html( $new_key ); ?></code></p></div>
			<?php endif; ?>

			<table class="wp-list-table widefat fixed striped">
				<thead><tr>
					<th><?php _e( 'Name', 'fome-hub' ); ?></th>
					<th><?php _e( 'Domain', 'fome-hub' ); ?></th>
					<th><?php _e( 'Brand colour', 'fome-hub' ); ?></th>
					<th><?php _e( 'Status', 'fome-hub' ); ?></th>
					<th><?php _e( 'Actions', 'fome-hub' ); ?></th>
				</tr></thead>
				<tbody>
				<?php foreach ( $sites as $site ) : ?>
					<tr>
						<td><?php echo esc_html( $site['name'] ); ?></td>
						<td><a href="https://<?php echo esc_attr( $site['domain'] ); ?>" target="_blank"><?php echo esc_html( $site['domain'] ); ?></a></td>
						<td>
							<span style="display:inline-block;width:20px;height:20px;border-radius:3px;background:<?php echo esc_attr( $site['brand_colour'] ); ?>;vertical-align:middle;margin-right:6px;border:1px solid #ccc;"></span>
							<?php echo esc_html( $site['brand_colour'] ); ?>
						</td>
						<td><?php echo esc_html( $site['status'] ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=fome-sites&action=edit&site_id=' . $site['id'] ) ); ?>"><?php _e( 'Edit', 'fome-hub' ); ?></a>
							&nbsp;|&nbsp;
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=fome_regenerate_key&site_id=' . $site['id'] ), 'fome_regen_' . $site['id'] ) ); ?>"><?php _e( 'Regenerate API key', 'fome-hub' ); ?></a>
							&nbsp;|&nbsp;
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=fome-sites&action=edit&site_id=' . $site['id'] . '&send_test=1' ) ); ?>"><?php _e( 'Send test email', 'fome-hub' ); ?></a>
							&nbsp;|&nbsp;
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=fome_delete_site&site_id=' . $site['id'] ), 'fome_delete_' . $site['id'] ) ); ?>" onclick="return confirm('Delete this site?')" style="color:red;"><?php _e( 'Delete', 'fome-hub' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private static function render_form( int $site_id ): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$site = $site_id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}fome_sites WHERE id = %d", $site_id ), ARRAY_A ) : null;

		$stripe_accounts = $wpdb->get_results( "SELECT id, label FROM {$p}fome_stripe_accounts ORDER BY label", ARRAY_A );

		// All services, with enabled state for this site
		$services = $wpdb->get_results(
			$site_id
				? $wpdb->prepare(
					"SELECT s.*, COALESCE(ss.enabled,0) as enabled
					 FROM {$p}fome_services s
					 LEFT JOIN {$p}fome_site_services ss ON ss.service_id = s.id AND ss.site_id = %d
					 ORDER BY s.category, s.sort_order",
					$site_id
				)
				: "SELECT *, 1 as enabled FROM {$p}fome_services ORDER BY category, sort_order",
			ARRAY_A
		);

		$recipients = $site ? implode( "\n", json_decode( $site['recipient_emails'], true ) ?? [] ) : '';

		$is_new  = ! $site;
		$gen_key = $is_new ? Fome_API_Keys::generate() : '';
		$key_hash = $gen_key ? Fome_API_Keys::hash( $gen_key ) : '';
		?>
		<div class="wrap">
			<h1><?php echo $is_new ? __( 'Add New Site', 'fome-hub' ) : __( 'Edit Site', 'fome-hub' ); ?></h1>

			<?php if ( $is_new && $gen_key ) : ?>
				<div class="notice notice-warning">
					<p><strong><?php _e( 'New API key (save before leaving — shown once only):', 'fome-hub' ); ?></strong><br>
					<code><?php echo esc_html( $gen_key ); ?></code></p>
				</div>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'fome_save_site' ); ?>
				<input type="hidden" name="action"   value="fome_save_site">
				<input type="hidden" name="site_id"  value="<?php echo esc_attr( $site_id ); ?>">
				<?php if ( $is_new ) : ?>
					<input type="hidden" name="_api_key"      value="<?php echo esc_attr( $gen_key ); ?>">
					<input type="hidden" name="_api_key_hash" value="<?php echo esc_attr( $key_hash ); ?>">
				<?php endif; ?>

				<table class="form-table">
					<tr><th><?php _e( 'Site name', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="text" name="name" value="<?php echo esc_attr( $site['name'] ?? '' ); ?>" required></td></tr>
					<tr><th><?php _e( 'Domain', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="text" name="domain" placeholder="fastklean.co.uk" value="<?php echo esc_attr( $site['domain'] ?? '' ); ?>" required>
						<p class="description"><?php _e( 'Without https://', 'fome-hub' ); ?></p></td></tr>

					<tr><th><?php _e( 'Brand colour', 'fome-hub' ); ?></th>
						<td>
							<input type="text" name="brand_colour" class="fome-color-picker" value="<?php echo esc_attr( $site['brand_colour'] ?? '#4578b4' ); ?>">
							<p class="description"><?php _e( 'e.g. #4578b4 (FastKlean) or #719430 (FK Domestics)', 'fome-hub' ); ?></p>
						</td></tr>

					<tr><th><?php _e( 'From email', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="email" name="from_email" value="<?php echo esc_attr( $site['from_email'] ?? '' ); ?>" required></td></tr>
					<tr><th><?php _e( 'From name', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="text" name="from_name" value="<?php echo esc_attr( $site['from_name'] ?? '' ); ?>" required></td></tr>
					<tr><th><?php _e( 'Footer site name', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="text" name="footer_site_name" value="<?php echo esc_attr( $site['footer_site_name'] ?? '' ); ?>" required>
						<p class="description"><?php _e( 'Used in: "This email was sent from the online booking system on <strong>FastKlean</strong>"', 'fome-hub' ); ?></p></td></tr>
					<tr><th><?php _e( 'Footer site URL', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="url" name="footer_site_url" placeholder="https://fastklean.co.uk" value="<?php echo esc_attr( $site['footer_site_url'] ?? '' ); ?>" required></td></tr>

					<tr><th><?php _e( 'Recipient emails', 'fome-hub' ); ?></th>
						<td>
							<textarea name="recipient_emails" rows="4" class="large-text"><?php echo esc_textarea( $recipients ); ?></textarea>
							<p class="description">
								<?php _e( 'One email address per line.', 'fome-hub' ); ?>
								<strong style="color:#b32d2e;"><?php _e( '⚠ Warning: changing recipient emails may break the forwarding chain into the parser mailbox. Coordinate with the operations team before making changes.', 'fome-hub' ); ?></strong>
							</p>
						</td></tr>

					<tr><th><?php _e( 'Stripe account', 'fome-hub' ); ?></th>
						<td>
							<select name="stripe_account_id">
								<option value=""><?php _e( '— None —', 'fome-hub' ); ?></option>
								<?php foreach ( $stripe_accounts as $acc ) : ?>
									<option value="<?php echo esc_attr( $acc['id'] ); ?>" <?php selected( $site['stripe_account_id'] ?? '', $acc['id'] ); ?>>
										<?php echo esc_html( $acc['label'] ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td></tr>

					<tr><th><?php _e( 'Stripe success URL', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="url" name="stripe_success_url" value="<?php echo esc_attr( $site['stripe_success_url'] ?? '' ); ?>"></td></tr>
					<tr><th><?php _e( 'Stripe cancel URL', 'fome-hub' ); ?></th>
						<td><input class="regular-text" type="url" name="stripe_cancel_url" value="<?php echo esc_attr( $site['stripe_cancel_url'] ?? '' ); ?>"></td></tr>

					<tr><th><?php _e( 'Status', 'fome-hub' ); ?></th>
						<td>
							<select name="status">
								<option value="active" <?php selected( $site['status'] ?? 'active', 'active' ); ?>><?php _e( 'Active', 'fome-hub' ); ?></option>
								<option value="inactive" <?php selected( $site['status'] ?? '', 'inactive' ); ?>><?php _e( 'Inactive', 'fome-hub' ); ?></option>
							</select>
						</td></tr>
				</table>

				<h2><?php _e( 'Enabled services', 'fome-hub' ); ?></h2>
				<p><?php _e( 'Choose which services this site offers:', 'fome-hub' ); ?></p>

				<?php
				$grouped = [];
				foreach ( $services as $s ) {
					$grouped[ $s['category'] ][] = $s;
				}
				foreach ( $grouped as $cat => $svcs ) :
				?>
					<h3><?php echo esc_html( ucfirst( $cat ) ); ?></h3>
					<div style="columns:3;margin-bottom:16px;">
					<?php foreach ( $svcs as $s ) : ?>
						<label style="display:block;margin-bottom:6px;">
							<input type="checkbox" name="services[]" value="<?php echo esc_attr( $s['id'] ); ?>" <?php checked( $s['enabled'] ?? 0, 1 ); ?>>
							<?php echo esc_html( $s['name'] ); ?>
						</label>
					<?php endforeach; ?>
					</div>
				<?php endforeach; ?>

				<?php if ( $is_new ) : ?>
				<div class="notice notice-info" style="padding:12px;margin:20px 0;">
					<h3><?php _e( 'Go-live checklist', 'fome-hub' ); ?></h3>
					<ol>
						<li><?php _e( 'Install the <code>fome-booking</code> plugin on the target WordPress site', 'fome-hub' ); ?></li>
						<li><?php _e( 'Go to Settings → Fome Booking and paste the API key shown above', 'fome-hub' ); ?></li>
						<li><?php _e( 'Set up forwarding rule in your mail provider so emails from <strong>From Email</strong> above are delivered to the parser mailbox', 'fome-hub' ); ?></li>
						<li><?php _e( 'Click "Send test booking email" and confirm the parser picks it up correctly', 'fome-hub' ); ?></li>
						<li><?php _e( 'Set up real cron on the site: <code>wp cron event run --due-now</code> every 5 minutes via the hosting control panel', 'fome-hub' ); ?></li>
					</ol>
				</div>
				<?php endif; ?>

				<?php submit_button( $is_new ? __( 'Create site', 'fome-hub' ) : __( 'Save changes', 'fome-hub' ) ); ?>
			</form>
		</div>
		<?php
	}

	public static function handle_save(): void {
		check_admin_referer( 'fome_save_site' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p = $wpdb->prefix;

		$site_id        = (int) ( $_POST['site_id'] ?? 0 );
		$recipients_raw = sanitize_textarea_field( $_POST['recipient_emails'] ?? '' );
		$recipients     = array_values( array_filter( array_map( 'trim', explode( "\n", $recipients_raw ) ) ) );

		$stripe_account_id = $_POST['stripe_account_id'] !== '' ? (int) $_POST['stripe_account_id'] : null;

		$data = [
			'name'              => sanitize_text_field( $_POST['name'] ?? '' ),
			'domain'            => sanitize_text_field( $_POST['domain'] ?? '' ),
			'brand_colour'      => sanitize_hex_color( $_POST['brand_colour'] ?? '#4578b4' ) ?: '#4578b4',
			'from_email'        => sanitize_email( $_POST['from_email'] ?? '' ),
			'from_name'         => sanitize_text_field( $_POST['from_name'] ?? '' ),
			'footer_site_name'  => sanitize_text_field( $_POST['footer_site_name'] ?? '' ),
			'footer_site_url'   => esc_url_raw( $_POST['footer_site_url'] ?? '' ),
			'recipient_emails'  => wp_json_encode( $recipients ),
			'stripe_success_url'=> esc_url_raw( $_POST['stripe_success_url'] ?? '' ),
			'stripe_cancel_url' => esc_url_raw( $_POST['stripe_cancel_url'] ?? '' ),
			'status'            => in_array( $_POST['status'] ?? '', [ 'active', 'inactive' ], true ) ? $_POST['status'] : 'active',
		];

		// Handle nullable FK separately to avoid wpdb NULL-as-string issue
		if ( $stripe_account_id !== null ) {
			$data['stripe_account_id'] = $stripe_account_id;
		}

		$enabled_service_ids = array_map( 'intval', (array) ( $_POST['services'] ?? [] ) );

		$wpdb->show_errors();

		if ( $site_id ) {
			$result = $wpdb->update( "{$p}fome_sites", $data, [ 'id' => $site_id ] );
		} else {
			$data['api_key_hash'] = sanitize_text_field( $_POST['_api_key_hash'] ?? '' );
			$result  = $wpdb->insert( "{$p}fome_sites", $data );
			$site_id = (int) $wpdb->insert_id;
		}

		if ( $result === false || ( ! $site_id && empty( $_POST['site_id'] ) ) ) {
			$err = $wpdb->last_error ?: 'Unknown database error.';
			wp_die(
				'<h1>Site could not be saved</h1><p>' . esc_html( $err ) . '</p>' .
				'<p><a href="' . esc_url( admin_url( 'admin.php?page=fome-sites' ) ) . '">&larr; Back to Sites</a></p>'
			);
		}

		// Sync site_services
		if ( $site_id ) {
			$all_services = $wpdb->get_col( "SELECT id FROM {$p}fome_services" );
			foreach ( $all_services as $sid ) {
				$enabled = in_array( (int) $sid, $enabled_service_ids, true ) ? 1 : 0;
				$wpdb->replace( "{$p}fome_site_services", [ 'site_id' => $site_id, 'service_id' => (int) $sid, 'enabled' => $enabled ] );
			}
		}

		$new_key  = sanitize_text_field( $_POST['_api_key'] ?? '' );
		$redirect = admin_url( 'admin.php?page=fome-sites&notice=saved' );
		if ( $new_key ) {
			$redirect = add_query_arg( 'new_key', urlencode( $new_key ), $redirect );
		}
		wp_redirect( $redirect );
		exit;
	}

	public static function handle_delete(): void {
		$site_id = (int) ( $_GET['site_id'] ?? 0 );
		check_admin_referer( 'fome_delete_' . $site_id );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p = $wpdb->prefix;
		$wpdb->delete( "{$p}fome_site_services", [ 'site_id' => $site_id ] );
		$wpdb->delete( "{$p}fome_sites", [ 'id' => $site_id ] );
		wp_redirect( admin_url( 'admin.php?page=fome-sites&notice=deleted' ) );
		exit;
	}

	public static function handle_regenerate_key(): void {
		$site_id = (int) ( $_GET['site_id'] ?? 0 );
		check_admin_referer( 'fome_regen_' . $site_id );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p       = $wpdb->prefix;
		$new_key = Fome_API_Keys::generate();
		$wpdb->update( "{$p}fome_sites", [ 'api_key_hash' => Fome_API_Keys::hash( $new_key ) ], [ 'id' => $site_id ] );
		wp_redirect( admin_url( 'admin.php?page=fome-sites&notice=saved&new_key=' . urlencode( $new_key ) ) );
		exit;
	}
}
