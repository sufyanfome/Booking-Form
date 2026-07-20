<?php
defined( 'ABSPATH' ) || exit;

/**
 * Libsodium-based encryption for Stripe keys and SMTP passwords.
 * The encryption key must be defined in wp-config.php as FOME_ENCRYPTION_KEY (32 bytes, hex-encoded).
 */
class Fome_Encryption {

	private static function key(): string {
		if ( ! defined( 'FOME_ENCRYPTION_KEY' ) ) {
			throw new \RuntimeException( 'FOME_ENCRYPTION_KEY is not defined in wp-config.php' );
		}
		$raw = sodium_hex2bin( FOME_ENCRYPTION_KEY );
		if ( strlen( $raw ) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES ) {
			throw new \RuntimeException( 'FOME_ENCRYPTION_KEY must be ' . SODIUM_CRYPTO_SECRETBOX_KEYBYTES . ' bytes (64 hex chars)' );
		}
		return $raw;
	}

	public static function encrypt( string $plaintext ): string {
		$nonce      = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext = sodium_crypto_secretbox( $plaintext, $nonce, self::key() );
		return base64_encode( $nonce . $ciphertext );
	}

	public static function decrypt( string $encoded ): string {
		$decoded    = base64_decode( $encoded, true );
		$nonce      = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$ciphertext = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$plain      = sodium_crypto_secretbox_open( $ciphertext, $nonce, self::key() );
		if ( $plain === false ) {
			throw new \RuntimeException( 'Decryption failed — key mismatch or corrupted data' );
		}
		return $plain;
	}
}
