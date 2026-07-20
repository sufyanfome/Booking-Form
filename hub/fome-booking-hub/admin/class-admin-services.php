<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_Services {

	public static function init(): void {}

	public static function render(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$services = $wpdb->get_results(
			"SELECT s.*,
			 (SELECT COUNT(*) FROM {$p}fome_site_services ss WHERE ss.service_id = s.id AND ss.enabled = 1) as active_sites
			 FROM {$p}fome_services s
			 ORDER BY s.category, s.sort_order",
			ARRAY_A
		);

		$grouped = [];
		foreach ( $services as $s ) {
			$grouped[ $s['category'] ][] = $s;
		}
		?>
		<div class="wrap">
			<h1><?php _e( 'Services', 'fome-hub' ); ?></h1>
			<p><?php _e( 'Service catalogue. Toggle availability per site on the <a href="' . esc_url( admin_url( 'admin.php?page=fome-sites' ) ) . '">Sites</a> page.', 'fome-hub' ); ?></p>

			<?php foreach ( $grouped as $cat => $svcs ) : ?>
				<h2><?php echo esc_html( ucfirst( $cat ) ); ?></h2>
				<table class="wp-list-table widefat fixed striped">
					<thead><tr>
						<th><?php _e( 'Service name', 'fome-hub' ); ?></th>
						<th><?php _e( 'Key', 'fome-hub' ); ?></th>
						<th><?php _e( 'Payment type', 'fome-hub' ); ?></th>
						<th><?php _e( 'Active on sites', 'fome-hub' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $svcs as $s ) : ?>
						<tr>
							<td><?php echo esc_html( $s['name'] ); ?></td>
							<td><code><?php echo esc_html( $s['service_key'] ); ?></code></td>
							<td><?php echo esc_html( $s['payment_type'] ); ?></td>
							<td><?php echo esc_html( $s['active_sites'] ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
