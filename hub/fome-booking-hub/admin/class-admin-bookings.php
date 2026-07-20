<?php
defined( 'ABSPATH' ) || exit;

class Fome_Admin_Bookings {

	public static function init(): void {
		add_action( 'admin_post_fome_export_bookings', [ __CLASS__, 'handle_export' ] );
	}

	public static function render(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$site_id    = (int) ( $_GET['site_id'] ?? 0 );
		$status     = sanitize_key( $_GET['payment_status'] ?? '' );
		$date_from  = sanitize_text_field( $_GET['date_from'] ?? '' );
		$date_to    = sanitize_text_field( $_GET['date_to'] ?? '' );
		$booking_id = (int) ( $_GET['booking_id'] ?? 0 );
		$per_page   = 50;
		$current_p  = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
		$offset     = ( $current_p - 1 ) * $per_page;

		// Single booking view
		if ( $booking_id ) {
			$booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$p}fome_bookings WHERE id = %d", $booking_id ), ARRAY_A );
			if ( $booking ) {
				echo '<div class="wrap"><h1>' . esc_html__( 'Booking', 'fome-hub' ) . ' #' . esc_html( $booking_id ) . '</h1>';
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=fome-bookings' ) ) . '">&larr; ' . esc_html__( 'Back', 'fome-hub' ) . '</a>';
				echo '<h2>' . esc_html__( 'Raw email body', 'fome-hub' ) . '</h2>';
				echo '<div style="border:1px solid #ddd;padding:16px;background:#fff;max-width:800px;">' . wp_kses_post( $booking['raw_email_body'] ) . '</div>';
				echo '</div>';
				return;
			}
		}

		// Build query
		$where  = [ '1=1' ];
		$params = [];
		if ( $site_id ) { $where[] = 'b.site_id = %d'; $params[] = $site_id; }
		if ( $status )  { $where[] = 'b.payment_status = %s'; $params[] = $status; }
		if ( $date_from ){ $where[] = 'b.booking_date >= %s'; $params[] = $date_from; }
		if ( $date_to )  { $where[] = 'b.booking_date <= %s'; $params[] = $date_to; }

		$where_sql = implode( ' AND ', $where );
		$count_sql = "SELECT COUNT(*) FROM {$p}fome_bookings b WHERE {$where_sql}";
		$total     = $params ? (int) $wpdb->get_var( $wpdb->prepare( $count_sql, ...$params ) ) : (int) $wpdb->get_var( $count_sql );

		$query_sql = "SELECT b.*, s.name as site_name FROM {$p}fome_bookings b
					  LEFT JOIN {$p}fome_sites s ON s.id = b.site_id
					  WHERE {$where_sql}
					  ORDER BY b.created_at DESC
					  LIMIT %d OFFSET %d";
		$all_params = array_merge( $params, [ $per_page, $offset ] );
		$bookings = $wpdb->get_results( $wpdb->prepare( $query_sql, ...$all_params ), ARRAY_A );

		$sites = $wpdb->get_results( "SELECT id, name FROM {$p}fome_sites ORDER BY name", ARRAY_A );
		?>
		<div class="wrap">
			<h1><?php _e( 'Bookings log', 'fome-hub' ); ?></h1>

			<form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px;">
				<input type="hidden" name="page" value="fome-bookings">

				<div>
					<label><?php _e( 'Site', 'fome-hub' ); ?><br>
					<select name="site_id">
						<option value=""><?php _e( '— All sites —', 'fome-hub' ); ?></option>
						<?php foreach ( $sites as $s ) : ?>
							<option value="<?php echo esc_attr( $s['id'] ); ?>" <?php selected( $site_id, $s['id'] ); ?>><?php echo esc_html( $s['name'] ); ?></option>
						<?php endforeach; ?>
					</select></label>
				</div>

				<div>
					<label><?php _e( 'Payment status', 'fome-hub' ); ?><br>
					<select name="payment_status">
						<option value=""><?php _e( '— All —', 'fome-hub' ); ?></option>
						<?php foreach ( [ 'pending', 'paid', 'subscription_active', 'failed', 'n/a' ] as $st ) : ?>
							<option value="<?php echo esc_attr( $st ); ?>" <?php selected( $status, $st ); ?>><?php echo esc_html( $st ); ?></option>
						<?php endforeach; ?>
					</select></label>
				</div>

				<div>
					<label><?php _e( 'Date from', 'fome-hub' ); ?><br>
					<input type="date" name="date_from" value="<?php echo esc_attr( $date_from ); ?>"></label>
				</div>
				<div>
					<label><?php _e( 'Date to', 'fome-hub' ); ?><br>
					<input type="date" name="date_to" value="<?php echo esc_attr( $date_to ); ?>"></label>
				</div>

				<?php submit_button( __( 'Filter', 'fome-hub' ), 'secondary', 'submit', false ); ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=fome_export_bookings&site_id=' . $site_id . '&payment_status=' . $status . '&date_from=' . $date_from . '&date_to=' . $date_to ), 'fome_export' ) ); ?>" class="button"><?php _e( 'Export CSV', 'fome-hub' ); ?></a>
			</form>

			<p><?php printf( _n( '%s booking found.', '%s bookings found.', $total, 'fome-hub' ), number_format_i18n( $total ) ); ?></p>

			<table class="wp-list-table widefat fixed striped">
				<thead><tr>
					<th style="width:50px;">ID</th>
					<th><?php _e( 'Site', 'fome-hub' ); ?></th>
					<th><?php _e( 'Service', 'fome-hub' ); ?></th>
					<th><?php _e( 'Customer', 'fome-hub' ); ?></th>
					<th><?php _e( 'Postcode', 'fome-hub' ); ?></th>
					<th><?php _e( 'Date', 'fome-hub' ); ?></th>
					<th><?php _e( 'Price', 'fome-hub' ); ?></th>
					<th><?php _e( 'Status', 'fome-hub' ); ?></th>
					<th><?php _e( 'Created', 'fome-hub' ); ?></th>
				</tr></thead>
				<tbody>
				<?php foreach ( $bookings as $b ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( admin_url( 'admin.php?page=fome-bookings&booking_id=' . $b['id'] ) ); ?>"><?php echo esc_html( $b['id'] ); ?></a></td>
						<td><?php echo esc_html( $b['site_name'] ); ?></td>
						<td><?php echo esc_html( $b['service'] ); ?></td>
						<td><?php echo esc_html( $b['customer_name'] . ' &lt;' . $b['email'] . '&gt;' ); ?></td>
						<td><?php echo esc_html( $b['postcode'] ); ?></td>
						<td><?php echo esc_html( $b['booking_date'] ); ?></td>
						<td>£<?php echo esc_html( number_format( $b['price'], 2 ) ); ?></td>
						<td><?php echo esc_html( $b['payment_status'] ); ?></td>
						<td><?php echo esc_html( $b['created_at'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<?php
			$pages = (int) ceil( $total / $per_page );
			if ( $pages > 1 ) {
				echo '<div class="tablenav"><div class="tablenav-pages">';
				echo paginate_links( [
					'base'      => add_query_arg( 'paged', '%#%' ),
					'format'    => '',
					'current'   => $current_p,
					'total'     => $pages,
				] );
				echo '</div></div>';
			}
			?>
		</div>
		<?php
	}

	public static function handle_export(): void {
		check_admin_referer( 'fome_export' );
		if ( ! current_user_can( 'manage_fome_bookings' ) ) wp_die( 'Forbidden' );

		global $wpdb;
		$p = $wpdb->prefix;

		$site_id   = (int) ( $_GET['site_id'] ?? 0 );
		$status    = sanitize_key( $_GET['payment_status'] ?? '' );
		$date_from = sanitize_text_field( $_GET['date_from'] ?? '' );
		$date_to   = sanitize_text_field( $_GET['date_to'] ?? '' );

		$where  = [ '1=1' ];
		$params = [];
		if ( $site_id ) { $where[] = 'b.site_id = %d'; $params[] = $site_id; }
		if ( $status )  { $where[] = 'b.payment_status = %s'; $params[] = $status; }
		if ( $date_from ){ $where[] = 'b.booking_date >= %s'; $params[] = $date_from; }
		if ( $date_to )  { $where[] = 'b.booking_date <= %s'; $params[] = $date_to; }

		$where_sql = implode( ' AND ', $where );
		$sql = "SELECT b.id, s.name as site_name, b.service, b.customer_name, b.email, b.phone, b.postcode, b.address, b.booking_date, b.booking_time, b.price, b.payment_status, b.created_at
				FROM {$p}fome_bookings b
				LEFT JOIN {$p}fome_sites s ON s.id = b.site_id
				WHERE {$where_sql}
				ORDER BY b.created_at DESC";
		$bookings = $params ? $wpdb->get_results( $wpdb->prepare( $sql, ...$params ), ARRAY_A ) : $wpdb->get_results( $sql, ARRAY_A );

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="fome-bookings-' . date( 'Y-m-d' ) . '.csv"' );

		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, [ 'ID', 'Site', 'Service', 'Customer Name', 'Email', 'Phone', 'Postcode', 'Address', 'Date', 'Time', 'Price', 'Payment Status', 'Created At' ] );
		foreach ( $bookings as $row ) {
			fputcsv( $out, array_values( $row ) );
		}
		fclose( $out );
		exit;
	}
}
