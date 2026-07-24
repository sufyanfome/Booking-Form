<?php
defined( 'ABSPATH' ) || exit;

/**
 * Plugin Releases admin page.
 *
 * Lets you upload a new fome-booking.zip, stores it on the hub server,
 * and records version + changelog in the DB so connected sites can
 * detect and pull the update through WordPress's native update mechanism.
 */
class Fome_Admin_Plugin_Releases {

	const OPTION_CURRENT = 'fome_plugin_release_current';
	const UPLOAD_DIR     = 'fome-plugin-releases';

	public static function init(): void {
		add_action( 'admin_post_fome_upload_release', [ __CLASS__, 'handle_upload' ] );
		add_action( 'admin_post_fome_delete_release', [ __CLASS__, 'handle_delete' ] );
	}

	public static function render(): void {
		$current = get_option( self::OPTION_CURRENT, [] );
		$notice  = sanitize_key( $_GET['notice'] ?? '' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Plugin Releases', 'fome-hub' ); ?></h1>
			<p><?php _e( 'Upload a new <code>fome-booking.zip</code> here. All connected sites will see "Update available" in their wp-admin within minutes and can update with one click.', 'fome-hub' ); ?></p>

			<?php if ( $notice === 'uploaded' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Release uploaded. Sites will detect the update on their next check (or immediately via WP-CLI).', 'fome-hub' ); ?></p></div>
			<?php elseif ( $notice === 'deleted' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Release deleted.', 'fome-hub' ); ?></p></div>
			<?php elseif ( $notice === 'error' ) : ?>
				<div class="notice notice-error"><p><?php echo esc_html( sanitize_text_field( $_GET['msg'] ?? 'Upload failed.' ) ); ?></p></div>
			<?php endif; ?>

			<!-- Current release -->
			<?php if ( ! empty( $current ) ) : ?>
			<div class="postbox" style="max-width:700px;padding:20px;margin-bottom:24px;">
				<h2 style="margin-top:0;"><?php _e( 'Current release', 'fome-hub' ); ?></h2>
				<table class="form-table" style="max-width:600px;">
					<tr>
						<th><?php _e( 'Version', 'fome-hub' ); ?></th>
						<td><strong><?php echo esc_html( $current['version'] ); ?></strong></td>
					</tr>
					<tr>
						<th><?php _e( 'Uploaded', 'fome-hub' ); ?></th>
						<td><?php echo esc_html( $current['uploaded_at'] ); ?></td>
					</tr>
					<tr>
						<th><?php _e( 'Download URL', 'fome-hub' ); ?></th>
						<td>
							<code id="fome-dl-url"><?php echo esc_html( $current['download_url'] ); ?></code>
							<button type="button" class="button button-small fome-copy-btn" data-copy="fome-dl-url" style="margin-left:8px;">
								<?php _e( 'Copy', 'fome-hub' ); ?>
							</button>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Changelog', 'fome-hub' ); ?></th>
						<td><pre style="white-space:pre-wrap;font-size:13px;background:#f6f7f7;padding:10px;border:1px solid #ccc;"><?php echo esc_html( $current['changelog'] ?? '' ); ?></pre></td>
					</tr>
				</table>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px;">
					<?php wp_nonce_field( 'fome_delete_release' ); ?>
					<input type="hidden" name="action" value="fome_delete_release">
					<button type="submit" class="button button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Remove this release? Sites will no longer see an update.', 'fome-hub' ); ?>')">
						<?php _e( 'Remove release', 'fome-hub' ); ?>
					</button>
				</form>
			</div>
			<?php endif; ?>

			<!-- Upload form -->
			<div class="postbox" style="max-width:700px;padding:20px;">
				<h2 style="margin-top:0;"><?php echo empty( $current ) ? esc_html__( 'Upload first release', 'fome-hub' ) : esc_html__( 'Upload new release', 'fome-hub' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
					<?php wp_nonce_field( 'fome_upload_release' ); ?>
					<input type="hidden" name="action" value="fome_upload_release">
					<table class="form-table" style="max-width:600px;">
						<tr>
							<th><label for="fome-release-version"><?php _e( 'Version', 'fome-hub' ); ?></label></th>
							<td>
								<input type="text" id="fome-release-version" name="version" class="regular-text" required
								       placeholder="1.1.0" pattern="^\d+\.\d+\.\d+$">
								<p class="description"><?php _e( 'Must match the version in fome-booking.php (e.g. 1.1.0)', 'fome-hub' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="fome-release-zip"><?php _e( 'Plugin zip', 'fome-hub' ); ?></label></th>
							<td>
								<input type="file" id="fome-release-zip" name="plugin_zip" accept=".zip" required>
								<p class="description"><?php _e( 'Upload fome-booking.zip (the plugin folder zipped)', 'fome-hub' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="fome-release-changelog"><?php _e( 'Changelog', 'fome-hub' ); ?></label></th>
							<td>
								<textarea id="fome-release-changelog" name="changelog" rows="5" class="large-text"
								          placeholder="= 1.1.0 =&#10;* Fixed price calculation for End of Tenancy&#10;* Added support for Wooden Floor Polishing service"></textarea>
								<p class="description"><?php _e( 'Shown to site admins in wp-admin when update is available.', 'fome-hub' ); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Upload release', 'fome-hub' ), 'primary' ); ?>
				</form>
			</div>

			<!-- Instructions -->
			<div class="postbox" style="max-width:700px;padding:20px;margin-top:24px;">
				<h2 style="margin-top:0;"><?php _e( 'Release process', 'fome-hub' ); ?></h2>
				<ol>
					<li><?php _e( 'Bump the version in <code>plugin/fome-booking/fome-booking.php</code> (e.g. 1.0.0 → 1.1.0)', 'fome-hub' ); ?></li>
					<li><?php _e( 'Zip the <code>fome-booking/</code> folder: <code>cd plugin && zip -r fome-booking.zip fome-booking/</code>', 'fome-hub' ); ?></li>
					<li><?php _e( 'Upload the zip here with the matching version number and a changelog.', 'fome-hub' ); ?></li>
					<li><?php _e( 'All connected sites will see "Update available" in <strong>Plugins</strong> within 12 hours, or immediately after running <code>wp plugin update-check</code>.', 'fome-hub' ); ?></li>
					<li><?php _e( 'Site admins click Update — done. Or you can enable auto-updates per site.', 'fome-hub' ); ?></li>
				</ol>
			</div>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Handlers
	// -------------------------------------------------------------------------

	public static function handle_upload(): void {
		check_admin_referer( 'fome_upload_release' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		$version   = sanitize_text_field( $_POST['version'] ?? '' );
		$changelog = sanitize_textarea_field( $_POST['changelog'] ?? '' );

		if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $version ) ) {
			self::redirect_error( 'Invalid version format. Use semver e.g. 1.2.0' );
		}

		if ( empty( $_FILES['plugin_zip']['tmp_name'] ) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK ) {
			self::redirect_error( 'Upload failed. Please try again.' );
		}

		// Validate it's actually a zip
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime  = finfo_file( $finfo, $_FILES['plugin_zip']['tmp_name'] );
		finfo_close( $finfo );
		if ( ! in_array( $mime, [ 'application/zip', 'application/x-zip-compressed', 'application/octet-stream' ], true ) ) {
			self::redirect_error( 'File must be a .zip archive.' );
		}

		// Store in wp-content/uploads/fome-plugin-releases/
		$upload_dir = wp_upload_dir();
		$dest_dir   = trailingslashit( $upload_dir['basedir'] ) . self::UPLOAD_DIR;
		wp_mkdir_p( $dest_dir );

		// Write an .htaccess so the zip is only downloadable via our signed URL
		$htaccess = $dest_dir . '/.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			file_put_contents( $htaccess, "Order deny,allow\nDeny from all\n" );
		}

		$filename = 'fome-booking-' . $version . '.zip';
		$dest     = $dest_dir . '/' . $filename;

		if ( ! move_uploaded_file( $_FILES['plugin_zip']['tmp_name'], $dest ) ) {
			self::redirect_error( 'Could not save the uploaded file.' );
		}

		// Generate a signed download token (HMAC of version+filename, valid forever — rotated per release)
		$token        = hash_hmac( 'sha256', $version . $filename, wp_salt( 'auth' ) );
		$download_url = add_query_arg( [
			'fome_plugin_download' => '1',
			'version'              => $version,
			'token'                => $token,
		], home_url( '/' ) );

		update_option( self::OPTION_CURRENT, [
			'version'      => $version,
			'filename'     => $filename,
			'changelog'    => $changelog,
			'download_url' => $download_url,
			'token'        => $token,
			'uploaded_at'  => current_time( 'mysql' ),
		], false );

		wp_redirect( admin_url( 'admin.php?page=fome-plugin-releases&notice=uploaded' ) );
		exit;
	}

	public static function handle_delete(): void {
		check_admin_referer( 'fome_delete_release' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		$current = get_option( self::OPTION_CURRENT, [] );
		if ( ! empty( $current['filename'] ) ) {
			$upload_dir = wp_upload_dir();
			$file       = trailingslashit( $upload_dir['basedir'] ) . self::UPLOAD_DIR . '/' . $current['filename'];
			if ( file_exists( $file ) ) @unlink( $file );
		}

		delete_option( self::OPTION_CURRENT );
		wp_redirect( admin_url( 'admin.php?page=fome-plugin-releases&notice=deleted' ) );
		exit;
	}

	private static function redirect_error( string $msg ): never {
		wp_redirect( admin_url( 'admin.php?page=fome-plugin-releases&notice=error&msg=' . urlencode( $msg ) ) );
		exit;
	}

	// -------------------------------------------------------------------------
	// Secure download handler (hooked from main plugin file)
	// -------------------------------------------------------------------------

	public static function handle_download_request(): void {
		if ( empty( $_GET['fome_plugin_download'] ) ) return;

		$version  = sanitize_text_field( $_GET['version']      ?? '' );
		$token    = sanitize_text_field( $_GET['token']        ?? '' );
		// Accept key from header (REST context) or query string (WP upgrader context)
		$api_key  = $_SERVER['HTTP_X_FOME_KEY'] ?? sanitize_text_field( $_GET['fome_api_key'] ?? '' );

		// Must authenticate with a valid site API key
		if ( ! Fome_API_Keys::authenticate_raw( $api_key ) ) {
			status_header( 401 );
			exit( 'Unauthorized' );
		}

		$current = get_option( self::OPTION_CURRENT, [] );
		if ( empty( $current ) || $current['version'] !== $version ) {
			status_header( 404 );
			exit( 'Release not found' );
		}

		// Verify HMAC token
		$expected = hash_hmac( 'sha256', $version . $current['filename'], wp_salt( 'auth' ) );
		if ( ! hash_equals( $expected, $token ) ) {
			status_header( 403 );
			exit( 'Invalid token' );
		}

		$upload_dir = wp_upload_dir();
		$file       = trailingslashit( $upload_dir['basedir'] ) . self::UPLOAD_DIR . '/' . $current['filename'];
		if ( ! file_exists( $file ) ) {
			status_header( 404 );
			exit( 'File not found' );
		}

		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="fome-booking.zip"' );
		header( 'Content-Length: ' . filesize( $file ) );
		header( 'Cache-Control: no-store' );
		readfile( $file );
		exit;
	}

	// -------------------------------------------------------------------------
	// Public API: release info for the REST endpoint
	// -------------------------------------------------------------------------

	public static function get_current_release(): array {
		return get_option( self::OPTION_CURRENT, [] );
	}
}
