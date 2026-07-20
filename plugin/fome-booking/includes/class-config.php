<?php
defined( 'ABSPATH' ) || exit;

/**
 * Pulls config from the hub and caches it as a WordPress transient.
 * Falls back to last known config if the hub is unreachable.
 */
class Fome_Booking_Config {

	const CACHE_KEY     = 'fome_site_config';
	const CACHE_FALLBACK= 'fome_site_config_fallback';
	const TTL           = 600; // 10 minutes

	public static function get(): array|false {
		$cached = get_transient( self::CACHE_KEY );
		if ( $cached !== false ) {
			return $cached;
		}
		return self::refresh();
	}

	public static function refresh(): array|false {
		$hub_url = rtrim( get_option( 'fome_hub_url', '' ), '/' );
		$api_key = get_option( 'fome_site_api_key', '' );
		$site_id = (int) get_option( 'fome_site_id', 0 );

		if ( ! $hub_url || ! $api_key || ! $site_id ) {
			return false;
		}

		$response = wp_remote_get(
			"{$hub_url}/wp-json/fome/v1/sites/{$site_id}/config",
			[
				'headers' => [ 'X-Fome-Key' => $api_key ],
				'timeout' => 10,
				'sslverify' => true,
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			// Hub unreachable — serve last known config
			$fallback = get_option( self::CACHE_FALLBACK );
			if ( $fallback ) {
				set_transient( self::CACHE_KEY, $fallback, self::TTL );
				return $fallback;
			}
			return false;
		}

		$config = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $config ) ) {
			return false;
		}

		set_transient( self::CACHE_KEY, $config, self::TTL );
		update_option( self::CACHE_FALLBACK, $config, false );

		return $config;
	}

	/** Force-bust the cache (called by hub webhook on price/service change). */
	public static function bust(): void {
		delete_transient( self::CACHE_KEY );
	}
}

// Cache-bust endpoint: POST /wp-json/fome/v1/cache-bust
add_action( 'rest_api_init', function () {
	register_rest_route( 'fome/v1', '/cache-bust', [
		'methods'             => 'POST',
		'callback'            => function ( \WP_REST_Request $r ) {
			$key = $r->get_header( 'X-Fome-Key' );
			if ( $key !== get_option( 'fome_site_api_key' ) ) {
				return new \WP_Error( 'unauthorized', 'Invalid key', [ 'status' => 401 ] );
			}
			Fome_Booking_Config::bust();
			return new \WP_REST_Response( [ 'ok' => true ], 200 );
		},
		'permission_callback' => '__return_true',
	] );
} );
