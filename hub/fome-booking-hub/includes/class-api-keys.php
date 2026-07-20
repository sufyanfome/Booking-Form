<?php
defined( 'ABSPATH' ) || exit;

class Fome_API_Keys {

	public static function generate(): string {
		return 'fk_' . bin2hex( random_bytes( 24 ) );
	}

	public static function hash( string $key ): string {
		return hash( 'sha256', $key );
	}

	/**
	 * Authenticate an incoming request by X-Fome-Key header.
	 * Returns site row or false.
	 */
	public static function authenticate( \WP_REST_Request $request ): array|false {
		global $wpdb;
		$p   = $wpdb->prefix;
		$key = $request->get_header( 'X-Fome-Key' );
		if ( ! $key ) {
			return false;
		}
		$hash = self::hash( $key );
		$site = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$p}fome_sites WHERE api_key_hash = %s AND status = 'active'", $hash ),
			ARRAY_A
		);
		return $site ?: false;
	}
}
