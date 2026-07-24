<?php
defined( 'ABSPATH' ) || exit;

/**
 * Auto-updater for the fome-booking plugin.
 *
 * Hooks into WordPress's native plugin update mechanism so that when a new
 * version is uploaded to the hub, site admins see "Update available" in
 * wp-admin → Plugins and can update with a single click — exactly like any
 * plugin from wordpress.org.
 *
 * The hub is queried at most once per 12 hours (cached in a transient).
 * No third-party service involved.
 */
class Fome_Booking_Updater {

	const PLUGIN_SLUG    = 'fome-booking/fome-booking.php';
	const TRANSIENT_KEY  = 'fome_plugin_update_info';
	const CHECK_INTERVAL = 12 * HOUR_IN_SECONDS;

	public static function init(): void {
		add_filter( 'pre_set_site_transient_update_plugins', [ __CLASS__, 'check_for_update' ] );
		add_filter( 'plugins_api',                           [ __CLASS__, 'plugin_info' ], 10, 3 );
		add_filter( 'upgrader_package_options',              [ __CLASS__, 'inject_auth_header' ] );
		add_action( 'upgrader_process_complete',             [ __CLASS__, 'after_update' ], 10, 2 );
	}

	// -------------------------------------------------------------------------
	// 1. Inject update data into WordPress's update transient
	// -------------------------------------------------------------------------

	public static function check_for_update( object $transient ): object {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = self::fetch_release_info();
		if ( ! $release || ! $release['available'] ) {
			return $transient;
		}

		$installed_version = $transient->checked[ self::PLUGIN_SLUG ] ?? FOME_BOOKING_VERSION;

		if ( version_compare( $release['version'], $installed_version, '>' ) ) {
			$transient->response[ self::PLUGIN_SLUG ] = (object) [
				'slug'        => 'fome-booking',
				'plugin'      => self::PLUGIN_SLUG,
				'new_version' => $release['version'],
				'url'         => get_option( 'fome_hub_url', '' ),
				'package'     => $release['download_url'],
				'icons'       => [],
				'banners'     => [],
				'tested'      => get_bloginfo( 'version' ),
				'requires_php'=> '8.2',
			];
		} else {
			// Explicitly mark as no update so WP doesn't show stale data
			unset( $transient->response[ self::PLUGIN_SLUG ] );
			$transient->no_update[ self::PLUGIN_SLUG ] = (object) [
				'slug'        => 'fome-booking',
				'plugin'      => self::PLUGIN_SLUG,
				'new_version' => $installed_version,
				'url'         => '',
				'package'     => '',
			];
		}

		return $transient;
	}

	// -------------------------------------------------------------------------
	// 2. Populate the "View version X.X.X details" modal in wp-admin
	// -------------------------------------------------------------------------

	public static function plugin_info( mixed $result, string $action, object $args ): mixed {
		if ( $action !== 'plugin_information' ) {
			return $result;
		}
		if ( ( $args->slug ?? '' ) !== 'fome-booking' ) {
			return $result;
		}

		$release = self::fetch_release_info();
		if ( ! $release || ! $release['available'] ) {
			return $result;
		}

		return (object) [
			'name'          => 'Fome Booking',
			'slug'          => 'fome-booking',
			'version'       => $release['version'],
			'author'        => 'Fome Agency',
			'requires'      => '6.0',
			'requires_php'  => '8.2',
			'tested'        => get_bloginfo( 'version' ),
			'last_updated'  => $release['uploaded_at'] ?? '',
			'sections'      => [
				'changelog' => nl2br( esc_html( $release['changelog'] ?? 'No changelog provided.' ) ),
			],
			'download_link' => $release['download_url'],
		];
	}

	// -------------------------------------------------------------------------
	// 3. Inject the site API key into the download request so the hub
	//    can authenticate it before serving the zip
	// -------------------------------------------------------------------------

	public static function inject_auth_header( array $options ): array {
		$hub_url = rtrim( get_option( 'fome_hub_url', '' ), '/' );
		if ( ! $hub_url ) {
			return $options;
		}

		$package = $options['package'] ?? '';

		// Only touch download URLs that belong to our hub
		if ( ! str_starts_with( $package, $hub_url ) && ! str_contains( $package, 'fome_plugin_download' ) ) {
			return $options;
		}

		// WordPress's upgrader doesn't natively support custom headers on zip downloads,
		// so we embed the API key as a query-string parameter here rather than a header.
		// The hub verifies it the same way as the header.
		$api_key = get_option( 'fome_site_api_key', '' );
		if ( $api_key ) {
			$options['package'] = add_query_arg( 'fome_api_key', $api_key, $package );
		}

		return $options;
	}

	// -------------------------------------------------------------------------
	// 4. Clear our cached release info after a successful update so the
	//    next check fetches fresh data from the hub
	// -------------------------------------------------------------------------

	public static function after_update( \WP_Upgrader $upgrader, array $hook_extra ): void {
		if ( ( $hook_extra['type'] ?? '' ) === 'plugin'
			&& isset( $hook_extra['plugins'] )
			&& in_array( self::PLUGIN_SLUG, (array) $hook_extra['plugins'], true )
		) {
			delete_transient( self::TRANSIENT_KEY );
			// Also bust the site config cache so the new plugin picks up fresh config
			Fome_Booking_Config::bust();
		}
	}

	// -------------------------------------------------------------------------
	// Internal: fetch and cache release info from the hub
	// -------------------------------------------------------------------------

	private static function fetch_release_info(): ?array {
		$cached = get_transient( self::TRANSIENT_KEY );
		if ( $cached !== false ) {
			return $cached;
		}

		$hub_url = rtrim( get_option( 'fome_hub_url', '' ), '/' );
		$api_key = get_option( 'fome_site_api_key', '' );

		if ( ! $hub_url || ! $api_key ) {
			return null;
		}

		$response = wp_remote_get(
			"{$hub_url}/wp-json/fome/v1/plugin-update",
			[
				'headers'   => [ 'X-Fome-Key' => $api_key ],
				'timeout'   => 10,
				'sslverify' => true,
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			// Cache a null result for 1 hour to avoid hammering the hub on failures
			set_transient( self::TRANSIENT_KEY, [ 'available' => false ], HOUR_IN_SECONDS );
			return [ 'available' => false ];
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $data ) ) {
			return null;
		}

		set_transient( self::TRANSIENT_KEY, $data, self::CHECK_INTERVAL );
		return $data;
	}
}
