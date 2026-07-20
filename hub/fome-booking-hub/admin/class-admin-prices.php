<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_Prices {

	public static function init(): void {
		add_action( 'admin_post_fome_save_prices', [ __CLASS__, 'handle_save' ] );
	}

	public static function render(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$notice = sanitize_key( $_GET['notice'] ?? '' );
		$rows   = $wpdb->get_results(
			"SELECT * FROM {$p}fome_prices ORDER BY price_group, price_key",
			ARRAY_A
		);

		$grouped = [];
		foreach ( $rows as $r ) {
			$grouped[ $r['price_group'] ][] = $r;
		}

		$group_labels = [
			'minimums'       => __( 'Minimum booking fees', 'fome-hub' ),
			'hourly_rates'   => __( 'Hourly rates', 'fome-hub' ),
			'antiviral'      => __( 'Antiviral Sanitisation', 'fome-hub' ),
			'one_off'        => __( 'One Off Cleaning', 'fome-hub' ),
			'carpet'         => __( 'Carpet Cleaning', 'fome-hub' ),
			'mattress'       => __( 'Mattress Cleaning', 'fome-hub' ),
			'upholstery'     => __( 'Upholstery Cleaning', 'fome-hub' ),
			'windows'        => __( 'Window Cleaning', 'fome-hub' ),
			'curtains'       => __( 'Curtain Cleaning', 'fome-hub' ),
			'oven'           => __( 'Oven Cleaning', 'fome-hub' ),
			'hard_floor'     => __( 'Hard Floor Cleaning', 'fome-hub' ),
			'end_of_tenancy' => __( 'End of Tenancy Cleaning', 'fome-hub' ),
			'rubbish'        => __( 'Rubbish Removal', 'fome-hub' ),
		];
		?>
		<div class="wrap">
			<h1><?php _e( 'Prices', 'fome-hub' ); ?></h1>
			<p><?php _e( 'These prices apply to all sites. Save to push changes immediately.', 'fome-hub' ); ?></p>
			<?php if ( $notice === 'saved' ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php _e( 'Prices saved.', 'fome-hub' ); ?></p></div>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'fome_save_prices' ); ?>
				<input type="hidden" name="action" value="fome_save_prices">

				<?php foreach ( $grouped as $group => $prices ) : ?>
					<h2><?php echo esc_html( $group_labels[ $group ] ?? $group ); ?></h2>
					<table class="wp-list-table widefat fixed" style="max-width:700px;">
						<thead><tr>
							<th><?php _e( 'Item', 'fome-hub' ); ?></th>
							<th style="width:120px;"><?php _e( 'Amount (£)', 'fome-hub' ); ?></th>
						</tr></thead>
						<tbody>
						<?php foreach ( $prices as $row ) : ?>
							<tr>
								<td><?php echo esc_html( $row['label'] ); ?></td>
								<td>
									<input type="number" step="0.01" min="0" style="width:100px;"
										name="prices[<?php echo esc_attr( $row['price_key'] ); ?>]"
										value="<?php echo esc_attr( number_format( (float) $row['amount'], 2, '.', '' ) ); ?>">
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endforeach; ?>

				<?php submit_button( __( 'Save all prices', 'fome-hub' ) ); ?>
			</form>
		</div>
		<?php
	}

	public static function handle_save(): void {
		check_admin_referer( 'fome_save_prices' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p = $wpdb->prefix;

		$prices = (array) ( $_POST['prices'] ?? [] );
		foreach ( $prices as $key => $value ) {
			$key   = sanitize_key( $key );
			$value = (float) $value;
			$wpdb->update(
				"{$p}fome_prices",
				[ 'amount' => $value, 'updated_by' => get_current_user_id() ],
				[ 'price_key' => $key ]
			);
		}

		wp_redirect( admin_url( 'admin.php?page=fome-prices&notice=saved' ) );
		exit;
	}
}
