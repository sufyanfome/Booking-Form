<?php
defined( 'ABSPATH' ) || exit;

/**
 * Server-side price calculation from hub config.
 * Used by the REST API to return config and by the plugin before Stripe.
 */
class Fome_Price_Calculator {

	private array $prices;
	private array $settings;

	public function __construct( array $prices, array $settings ) {
		$this->prices   = $prices;
		$this->settings = $settings;
	}

	public static function from_db(): self {
		global $wpdb;
		$p = $wpdb->prefix;

		$rows     = $wpdb->get_results( "SELECT price_key, amount FROM {$p}fome_prices", ARRAY_A );
		$prices   = array_column( $rows, 'amount', 'price_key' );
		$rows2    = $wpdb->get_results( "SELECT setting_key, setting_value FROM {$p}fome_settings", ARRAY_A );
		$settings = array_column( $rows2, 'setting_value', 'setting_key' );

		return new self( $prices, $settings );
	}

	public function get( string $key ): float {
		return (float) ( $this->prices[ $key ] ?? 0 );
	}

	public function vat_enabled(): bool {
		return ( $this->settings['vat_enabled'] ?? 'true' ) === 'true';
	}

	public function online_discount_multiplier(): float {
		return (float) ( $this->settings['online_discount'] ?? 0.95 );
	}

	/**
	 * Apply VAT (20%) to a net amount if VAT is enabled.
	 */
	public function with_vat( float $net ): float {
		return $this->vat_enabled() ? round( $net * 1.20, 2 ) : $net;
	}

	/**
	 * Apply the online booking discount to a subtotal.
	 */
	public function apply_online_discount( float $subtotal ): float {
		return round( $subtotal * $this->online_discount_multiplier(), 2 );
	}

	/**
	 * Export prices keyed for the front end.
	 */
	public function export(): array {
		return [
			'prices'               => $this->prices,
			'vat_enabled'          => $this->vat_enabled(),
			'online_discount_mult' => $this->online_discount_multiplier(),
		];
	}
}
