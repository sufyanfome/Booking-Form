<?php
defined( 'ABSPATH' ) || exit;

class Fome_DB_Installer {

	public static function install(): void {
		global $wpdb;
		$charset = $wpdb->get_charset_collate();

		foreach ( self::schema( $charset ) as $sql ) {
			$wpdb->query( $sql );
		}

		self::seed_defaults();
		self::grant_capabilities();
		update_option( 'fome_hub_db_version', FOME_HUB_VERSION );
	}

	public static function grant_capabilities(): void {
		$admin = get_role( 'administrator' );
		if ( $admin && ! $admin->has_cap( 'manage_fome_bookings' ) ) {
			$admin->add_cap( 'manage_fome_bookings' );
		}
	}

	private static function schema( string $charset ): array {
		global $wpdb;
		$p = $wpdb->prefix;

		return [
			// Sites
			"CREATE TABLE IF NOT EXISTS {$p}fome_sites (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				name            VARCHAR(120)    NOT NULL,
				domain          VARCHAR(255)    NOT NULL UNIQUE,
				api_key_hash    VARCHAR(255)    NOT NULL,
				brand_colour    VARCHAR(20)     NOT NULL DEFAULT '#4578b4',
				from_email      VARCHAR(255)    NOT NULL DEFAULT '',
				from_name       VARCHAR(120)    NOT NULL DEFAULT '',
				footer_site_name VARCHAR(120)   NOT NULL DEFAULT '',
				footer_site_url  VARCHAR(255)   NOT NULL DEFAULT '',
				recipient_emails LONGTEXT       NOT NULL DEFAULT '[]',
				stripe_account_id BIGINT UNSIGNED NULL,
				stripe_success_url VARCHAR(255) NOT NULL DEFAULT '',
				stripe_cancel_url  VARCHAR(255) NOT NULL DEFAULT '',
				status          ENUM('active','inactive') NOT NULL DEFAULT 'active',
				created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset};",

			// Stripe accounts
			"CREATE TABLE IF NOT EXISTS {$p}fome_stripe_accounts (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				label           VARCHAR(120)    NOT NULL,
				publishable_key TEXT            NOT NULL DEFAULT '',
				secret_key_enc  TEXT            NOT NULL DEFAULT '',
				webhook_secret_enc TEXT         NOT NULL DEFAULT '',
				created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset};",

			// Master service catalogue
			"CREATE TABLE IF NOT EXISTS {$p}fome_services (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				service_key     VARCHAR(80)     NOT NULL UNIQUE,
				name            VARCHAR(120)    NOT NULL,
				category        ENUM('domestic','commercial') NOT NULL DEFAULT 'domestic',
				sort_order      INT             NOT NULL DEFAULT 0,
				form_schema     LONGTEXT        NOT NULL DEFAULT '{}',
				payment_type    ENUM('subscription','onetime','freequote') NOT NULL DEFAULT 'onetime',
				PRIMARY KEY (id)
			) {$charset};",

			// Per-site service toggles
			"CREATE TABLE IF NOT EXISTS {$p}fome_site_services (
				site_id         BIGINT UNSIGNED NOT NULL,
				service_id      BIGINT UNSIGNED NOT NULL,
				enabled         TINYINT(1)      NOT NULL DEFAULT 1,
				PRIMARY KEY (site_id, service_id)
			) {$charset};",

			// Prices
			"CREATE TABLE IF NOT EXISTS {$p}fome_prices (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				price_key       VARCHAR(100)    NOT NULL UNIQUE,
				label           VARCHAR(200)    NOT NULL,
				price_group     VARCHAR(80)     NOT NULL DEFAULT '',
				amount          DECIMAL(10,4)   NOT NULL DEFAULT 0,
				updated_by      BIGINT UNSIGNED NULL,
				updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset};",

			// Global settings
			"CREATE TABLE IF NOT EXISTS {$p}fome_settings (
				setting_key     VARCHAR(80)     NOT NULL,
				setting_value   TEXT            NOT NULL DEFAULT '',
				PRIMARY KEY (setting_key)
			) {$charset};",

			// Postcode coverage (shared across all sites)
			"CREATE TABLE IF NOT EXISTS {$p}fome_postcodes (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				outcode         VARCHAR(8)      NOT NULL UNIQUE,
				PRIMARY KEY (id)
			) {$charset};",

			// Gift cards
			"CREATE TABLE IF NOT EXISTS {$p}fome_gift_cards (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				code            VARCHAR(50)     NOT NULL UNIQUE,
				balance         DECIMAL(10,2)   NOT NULL DEFAULT 0,
				status          ENUM('active','used','expired') NOT NULL DEFAULT 'active',
				created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset};",

			// Discount codes
			"CREATE TABLE IF NOT EXISTS {$p}fome_discount_codes (
				id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				code            VARCHAR(50)     NOT NULL UNIQUE,
				multiplier      DECIMAL(5,4)    NOT NULL DEFAULT 1.0000,
				status          ENUM('active','inactive') NOT NULL DEFAULT 'active',
				created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset};",

			// Bookings log
			"CREATE TABLE IF NOT EXISTS {$p}fome_bookings (
				id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				site_id             BIGINT UNSIGNED NOT NULL,
				service             VARCHAR(120)    NOT NULL,
				customer_name       VARCHAR(200)    NOT NULL DEFAULT '',
				email               VARCHAR(255)    NOT NULL DEFAULT '',
				phone               VARCHAR(50)     NOT NULL DEFAULT '',
				postcode            VARCHAR(20)     NOT NULL DEFAULT '',
				address             TEXT            NOT NULL DEFAULT '',
				booking_date        DATE            NULL,
				booking_time        VARCHAR(20)     NOT NULL DEFAULT '',
				price               DECIMAL(10,2)   NOT NULL DEFAULT 0,
				original_price      DECIMAL(10,2)   NULL,
				discount_code       VARCHAR(50)     NOT NULL DEFAULT '',
				gift_card           VARCHAR(50)     NOT NULL DEFAULT '',
				payment_status      ENUM('pending','paid','subscription_active','failed','n/a') NOT NULL DEFAULT 'pending',
				stripe_session_id   VARCHAR(255)    NOT NULL DEFAULT '',
				raw_email_body      LONGTEXT        NOT NULL DEFAULT '',
				created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY idx_site (site_id),
				KEY idx_status (payment_status),
				KEY idx_date (booking_date),
				KEY idx_created (created_at)
			) {$charset};",
		];
	}

	private static function seed_defaults(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		// Settings defaults
		$defaults = [
			'vat_enabled'       => 'true',
			'online_discount'   => '0.95',
			'gift_cards_enabled'=> 'true',
		];
		foreach ( $defaults as $k => $v ) {
			$wpdb->query( $wpdb->prepare(
				"INSERT IGNORE INTO `{$p}fome_settings` (setting_key, setting_value) VALUES (%s, %s)",
				$k, $v
			) );
		}

		// Services catalogue
		self::seed_services();

		// Prices catalogue
		self::seed_prices();

		// London M25 postcodes
		self::seed_postcodes();
	}

	private static function seed_services(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$services = [
			// Domestic
			[ 'regular-cleaning',         'Regular Cleaning',         'domestic', 1,  'subscription' ],
			[ 'regular-cleaning-luxury',   'Regular Cleaning Luxury',  'domestic', 2,  'subscription' ],
			[ 'antiviral-sanitisation',    'Antiviral Sanitisation',   'domestic', 3,  'onetime' ],
			[ 'one-off-cleaning',          'One Off Cleaning',         'domestic', 4,  'onetime' ],
			[ 'carpet-cleaning',           'Carpet Cleaning',          'domestic', 5,  'onetime' ],
			[ 'mattress-cleaning',         'Mattress Cleaning',        'domestic', 6,  'onetime' ],
			[ 'upholstery-cleaning',       'Upholstery Cleaning',      'domestic', 7,  'onetime' ],
			[ 'window-cleaning',           'Window Cleaning',          'domestic', 8,  'onetime' ],
			[ 'curtain-cleaning',          'Curtain Cleaning',         'domestic', 9,  'onetime' ],
			[ 'oven-cleaning',             'Oven Cleaning',            'domestic', 10, 'onetime' ],
			[ 'rubbish-removal',           'Rubbish Removal',          'domestic', 11, 'onetime' ],
			[ 'after-builders-cleaning',   'After Builders Cleaning',  'domestic', 12, 'onetime' ],
			[ 'end-of-tenancy-cleaning',   'End of Tenancy Cleaning',  'domestic', 13, 'onetime' ],
			[ 'hard-floor-cleaning',       'Hard Floor Cleaning',      'domestic', 14, 'onetime' ],
			[ 'wooden-floor-services',     'Wooden Floor Services',    'domestic', 15, 'onetime' ],
			[ 'gardening',                 'Gardening',                'domestic', 16, 'onetime' ],
			[ 'mobile-car-valeting',       'Mobile Car Valeting',      'domestic', 17, 'onetime' ],
			// Commercial
			[ 'commercial-cleaning',       'Commercial Cleaning',      'commercial', 1, 'freequote' ],
			[ 'commercial-antiviral',      'Antiviral Sanitisation',   'commercial', 2, 'freequote' ],
			[ 'commercial-one-off',        'One Off Cleaning',         'commercial', 3, 'freequote' ],
			[ 'commercial-carpet',         'Carpet Cleaning',          'commercial', 4, 'freequote' ],
			[ 'commercial-mattress',       'Mattress Cleaning',        'commercial', 5, 'freequote' ],
			[ 'commercial-upholstery',     'Upholstery Cleaning',      'commercial', 6, 'freequote' ],
			[ 'commercial-window',         'Window Cleaning',          'commercial', 7, 'freequote' ],
			[ 'commercial-curtain',        'Curtain Cleaning',         'commercial', 8, 'freequote' ],
			[ 'commercial-oven',           'Oven Cleaning',            'commercial', 9, 'freequote' ],
			[ 'commercial-rubbish',        'Rubbish Removal',          'commercial', 10, 'freequote' ],
			[ 'commercial-after-builders', 'After Builders Cleaning',  'commercial', 11, 'freequote' ],
			[ 'commercial-end-tenancy',    'End of Tenancy Cleaning',  'commercial', 12, 'freequote' ],
			[ 'commercial-hard-floor',     'Hard Floor Cleaning',      'commercial', 13, 'freequote' ],
			[ 'commercial-wooden-floor',   'Wooden Floor Services',    'commercial', 14, 'freequote' ],
			[ 'commercial-gardening',      'Gardening',                'commercial', 15, 'freequote' ],
			[ 'commercial-car-valeting',   'Mobile Car Valeting',      'commercial', 16, 'freequote' ],
		];

		foreach ( $services as [ $key, $name, $cat, $order, $payment ] ) {
			$wpdb->insert( "{$p}fome_services", [
				'service_key'  => $key,
				'name'         => $name,
				'category'     => $cat,
				'sort_order'   => $order,
				'payment_type' => $payment,
				'form_schema'  => '{}',
			] );
		}
	}

	private static function seed_prices(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$prices = [
			// Minimum bookings
			[ 'regular_cleaning_minimum',                  'Regular Cleaning — minimum booking',                    'minimums',      30 ],
			[ 'regular_luxury_minimum',                    'Regular Cleaning Luxury — minimum booking',             'minimums',      32 ],
			[ 'commercial_cleaning_minimum',               'Commercial Cleaning — minimum booking',                 'minimums',      32 ],
			[ 'antiviral_sanitisation_minimum',            'Antiviral Sanitisation — minimum booking',              'minimums',     200 ],
			[ 'antiviral_sanitisation_commercial_minimum', 'Antiviral Sanitisation Commercial — minimum booking',   'minimums',     600 ],
			[ 'one_off_cleaning_minimum',                  'One Off Cleaning — minimum booking',                    'minimums',     125 ],
			[ 'carpet_cleaning_minimum',                   'Carpet Cleaning — minimum booking',                     'minimums',      85 ],
			[ 'mattress_cleaning_minimum',                 'Mattress Cleaning — minimum booking',                   'minimums',      85 ],
			[ 'upholstery_cleaning_minimum',               'Upholstery Cleaning — minimum booking',                 'minimums',      85 ],
			[ 'window_cleaning_minimum',                   'Window Cleaning — minimum booking',                     'minimums',      85 ],
			[ 'curtain_cleaning_minimum',                  'Curtain Cleaning — minimum booking',                    'minimums',      85 ],
			[ 'oven_cleaning_minimum',                     'Oven Cleaning — minimum booking',                       'minimums',      85 ],
			[ 'rubbish_removal_minimum',                   'Rubbish Removal — minimum booking',                     'minimums',      85 ],
			[ 'after_builders_minimum',                    'After Builders Cleaning — minimum booking',             'minimums',      85 ],
			[ 'end_of_tenancy_flat_minimum',               'End of Tenancy Cleaning (flat) — minimum booking',      'minimums',     120 ],
			[ 'end_of_tenancy_house_minimum',              'End of Tenancy Cleaning (house) — minimum booking',     'minimums',     160 ],
			[ 'hard_floor_minimum',                        'Hard Floor Cleaning — minimum booking',                 'minimums',     100 ],
			[ 'gardening_minimum',                         'Gardening — minimum booking',                           'minimums',     127 ],
			[ 'wooden_floor_minimum',                      'Wooden Floor Services — minimum booking',               'minimums',     350 ],
			[ 'car_valeting_internal_minimum',             'Car Valeting Internal — minimum booking',               'minimums',      75 ],
			[ 'car_valeting_external_minimum',             'Car Valeting External — minimum booking',               'minimums',      75 ],
			[ 'car_valeting_mini_minimum',                 'Car Valeting Mini — minimum booking',                   'minimums',      75 ],
			[ 'car_valeting_midi_minimum',                 'Car Valeting Midi — minimum booking',                   'minimums',      80 ],
			[ 'car_valeting_full_minimum',                 'Car Valeting Full — minimum booking',                   'minimums',     100 ],

			// Hourly rates
			[ 'regular_cleaning_price',         'Regular Cleaning — hourly rate',         'hourly_rates', 20 ],
			[ 'regular_luxury_price',            'Regular Cleaning Luxury — hourly rate',  'hourly_rates', 25 ],
			[ 'commercial_cleaning_price',       'Commercial Cleaning — hourly rate',      'hourly_rates', 20 ],
			[ 'after_builders_price',            'After Builders Cleaning — hourly rate',  'hourly_rates', 29 ],
			[ 'after_builders_equipment_price',  'After Builders + Equipment — hourly rate','hourly_rates',36 ],
			[ 'gardening_price',                 'Gardening — hourly rate',                'hourly_rates', 60 ],

			// Antiviral
			[ 'antiviral_price',                 'Antiviral Sanitisation — per sq m',      'antiviral',   2.5 ],
			[ 'antiviral_computer_price',        'Antiviral — per computer',               'antiviral',   3   ],
			[ 'antiviral_hotel_rooms_price',     'Antiviral — per hotel room',             'antiviral',   30  ],
			[ 'antiviral_comm_200_price',        'Antiviral Commercial (up to 200 sq m)',  'antiviral',   6   ],
			[ 'antiviral_comm_500_price',        'Antiviral Commercial (up to 500 sq m)',  'antiviral',   5   ],
			[ 'antiviral_comm_750_price',        'Antiviral Commercial (up to 750 sq m)',  'antiviral',   4   ],
			[ 'antiviral_comm_above_750_price',  'Antiviral Commercial (above 750 sq m)',  'antiviral',   3   ],
			[ 'antiviral_comm_computer_price',   'Antiviral Commercial — per computer',    'antiviral',   6   ],
			[ 'antiviral_comm_hotel_rooms_price','Antiviral Commercial — per hotel room',  'antiviral',   50  ],

			// One off
			[ 'one_off_price',                   'One Off Cleaning — hourly rate',         'one_off',     28 ],
			[ 'one_off_equipment_price',         'One Off + Equipment — hourly rate',      'one_off',     33 ],
			[ 'one_off_eco_natural_price',       'One Off Eco Natural — hourly rate',      'one_off',     30 ],
			[ 'one_off_eco_ionised_price',       'One Off Eco Ionised — hourly rate',      'one_off',     37 ],
			[ 'one_off_eco_bravo_price',         'One Off Eco Bravo — hourly rate',        'one_off',     27 ],
			[ 'one_off_bare_equipment_price',    'One Off Bare Equipment — hourly rate',   'one_off',     23 ],
			[ 'one_off_inside_fridge_price',     'One Off — inside fridge',                'one_off',     25 ],
			[ 'one_off_inside_freezer_price',    'One Off — inside freezer',               'one_off',     25 ],
			[ 'one_off_inside_cupboards_price',  'One Off — inside cupboards (small)',     'one_off',     50 ],
			[ 'one_off_inside_cupboards_med_price','One Off — inside cupboards (medium)',  'one_off',     70 ],
			[ 'one_off_inside_cupboards_lrg_price','One Off — inside cupboards (large)',   'one_off',     90 ],
			[ 'one_off_wooden_blinds_price',     'One Off — wooden blinds per blind',      'one_off',     10 ],
			[ 'one_off_glass_panel_price',       'One Off — glass panel per panel',        'one_off',     25 ],

			// Carpet steam
			[ 'carpet_steam_stairs_price',       'Carpet Steam — stairs',                  'carpet',      40 ],
			[ 'carpet_steam_lounge_price',       'Carpet Steam — lounge',                  'carpet',     105 ],
			[ 'carpet_steam_dining_price',       'Carpet Steam — dining room',             'carpet',      65 ],
			[ 'carpet_steam_kitchen_price',      'Carpet Steam — kitchen',                 'carpet',      25 ],
			[ 'carpet_steam_bedroom_price',      'Carpet Steam — single bedroom',          'carpet',      50 ],
			[ 'carpet_steam_double_bedroom_price','Carpet Steam — double bedroom',         'carpet',      55 ],
			[ 'carpet_steam_hallway_price',      'Carpet Steam — hallway',                 'carpet',      40 ],
			[ 'carpet_steam_landing_price',      'Carpet Steam — landing',                 'carpet',      25 ],
			[ 'carpet_steam_bathroom_price',     'Carpet Steam — bathroom',                'carpet',      25 ],
			[ 'carpet_steam_medium_rug_price',   'Carpet Steam — medium rug',              'carpet',      50 ],
			[ 'carpet_steam_large_rug_price',    'Carpet Steam — large rug',               'carpet',      65 ],

			// Carpet dry
			[ 'carpet_dry_stairs_price',         'Carpet Dry — stairs',                    'carpet',      75 ],
			[ 'carpet_dry_lounge_price',         'Carpet Dry — lounge',                    'carpet',     135 ],
			[ 'carpet_dry_dining_price',         'Carpet Dry — dining room',               'carpet',      95 ],
			[ 'carpet_dry_kitchen_price',        'Carpet Dry — kitchen',                   'carpet',      30 ],
			[ 'carpet_dry_bedroom_price',        'Carpet Dry — single bedroom',            'carpet',      65 ],
			[ 'carpet_dry_double_bedroom_price', 'Carpet Dry — double bedroom',            'carpet',      75 ],
			[ 'carpet_dry_hallway_price',        'Carpet Dry — hallway',                   'carpet',      55 ],
			[ 'carpet_dry_landing_price',        'Carpet Dry — landing',                   'carpet',      45 ],
			[ 'carpet_dry_bathroom_price',       'Carpet Dry — bathroom',                  'carpet',      30 ],
			[ 'carpet_dry_medium_rug_price',     'Carpet Dry — medium rug',                'carpet',      70 ],
			[ 'carpet_dry_large_rug_price',      'Carpet Dry — large rug',                 'carpet',      85 ],

			// Mattress steam
			[ 'mattress_steam_babycot_price',    'Mattress Steam — babycot',               'mattress',    15 ],
			[ 'mattress_steam_single_price',     'Mattress Steam — single',                'mattress',    35 ],
			[ 'mattress_steam_double_price',     'Mattress Steam — double',                'mattress',    50 ],
			[ 'mattress_steam_queen_price',      'Mattress Steam — queen size',            'mattress',    60 ],
			[ 'mattress_steam_king_price',       'Mattress Steam — king size',             'mattress',    70 ],

			// Mattress dry
			[ 'mattress_dry_single_price',       'Mattress Dry — single',                  'mattress',    60 ],
			[ 'mattress_dry_double_price',       'Mattress Dry — double',                  'mattress',    60 ],
			[ 'mattress_dry_queen_price',        'Mattress Dry — queen size',              'mattress',    65 ],
			[ 'mattress_dry_king_price',         'Mattress Dry — king size',               'mattress',    70 ],

			// Upholstery steam fabric
			[ 'upholstery_steam_2seater_fabric_price', 'Upholstery Steam — 2 seater fabric',  'upholstery', 75 ],
			[ 'upholstery_steam_3seater_fabric_price', 'Upholstery Steam — 3 seater fabric',  'upholstery', 90 ],
			[ 'upholstery_steam_5seater_fabric_price', 'Upholstery Steam — 5 seater fabric',  'upholstery',105 ],
			[ 'upholstery_steam_armchair_fabric_price','Upholstery Steam — armchair fabric',  'upholstery', 53 ],
			[ 'upholstery_steam_dining_chair_price',   'Upholstery Steam — dining chair',     'upholstery', 18 ],
			[ 'upholstery_steam_headboard_price',      'Upholstery Steam — headboard',        'upholstery', 10 ],

			// Upholstery steam leather
			[ 'upholstery_steam_2seater_leather_price','Upholstery Steam — 2 seater leather', 'upholstery', 75 ],
			[ 'upholstery_steam_3seater_leather_price','Upholstery Steam — 3 seater leather', 'upholstery', 90 ],
			[ 'upholstery_steam_5seater_leather_price','Upholstery Steam — 5 seater leather', 'upholstery',105 ],
			[ 'upholstery_steam_armchair_leather_price','Upholstery Steam — armchair leather','upholstery', 53 ],

			// Upholstery L-shaped steam fabric
			[ 'upholstery_lshaped_2seater_fabric_price','Upholstery L-shaped — 2 seater fabric','upholstery', 75 ],
			[ 'upholstery_lshaped_3seater_fabric_price','Upholstery L-shaped — 3 seater fabric','upholstery', 95 ],
			[ 'upholstery_lshaped_4seater_fabric_price','Upholstery L-shaped — 4 seater fabric','upholstery',115 ],
			[ 'upholstery_lshaped_5seater_fabric_price','Upholstery L-shaped — 5 seater fabric','upholstery',130 ],

			// Upholstery L-shaped steam leather
			[ 'upholstery_lshaped_2seater_leather_price','Upholstery L-shaped — 2 seater leather','upholstery',115 ],
			[ 'upholstery_lshaped_3seater_leather_price','Upholstery L-shaped — 3 seater leather','upholstery',135 ],
			[ 'upholstery_lshaped_4seater_leather_price','Upholstery L-shaped — 4 seater leather','upholstery',150 ],
			[ 'upholstery_lshaped_5seater_leather_price','Upholstery L-shaped — 5 seater leather','upholstery',175 ],

			// Upholstery dry
			[ 'upholstery_dry_2seater_price',  'Upholstery Dry — 2 seater', 'upholstery', 100 ],
			[ 'upholstery_dry_3seater_price',  'Upholstery Dry — 3 seater', 'upholstery', 120 ],
			[ 'upholstery_dry_5seater_price',  'Upholstery Dry — 5 seater', 'upholstery', 170 ],
			[ 'upholstery_dry_armchair_price', 'Upholstery Dry — armchair', 'upholstery',  75 ],
			[ 'upholstery_dry_dining_chair_price','Upholstery Dry — dining chair','upholstery', 50 ],

			// Windows
			[ 'window_small_price',       'Window — small',        'windows',  9 ],
			[ 'window_medium_price',      'Window — medium',       'windows', 12 ],
			[ 'window_large_price',       'Window — large',        'windows', 12 ],
			[ 'window_bay_price',         'Window — bay',          'windows', 24 ],
			[ 'window_french_door_price', 'Window — French door',  'windows', 15 ],

			// Curtains
			[ 'curtain_steam_short_price',       'Curtain Steam — short',       'curtains', 45 ],
			[ 'curtain_steam_full_length_price', 'Curtain Steam — full length', 'curtains', 55 ],
			[ 'curtain_dry_short_price',         'Curtain Dry — short',         'curtains', 55 ],
			[ 'curtain_dry_full_length_price',   'Curtain Dry — full length',   'curtains', 60 ],

			// Oven
			[ 'oven_gas_hobs_price',           'Oven — gas hobs',          'oven',  26 ],
			[ 'oven_gas_hobs_double_price',    'Oven — gas hobs double',   'oven',  31 ],
			[ 'oven_ceramic_hobs_price',       'Oven — ceramic hobs',      'oven',  24 ],
			[ 'oven_single_price',             'Oven — single',            'oven',  80 ],
			[ 'oven_double_price',             'Oven — double',            'oven',  93 ],
			[ 'oven_microwave_price',          'Oven — microwave',         'oven',  60 ],
			[ 'oven_extractor_price',          'Oven — extractor',         'oven',  26 ],
			[ 'oven_extractor_double_price',   'Oven — extractor double',  'oven',  31 ],
			[ 'oven_range_oven_price',         'Oven Range — oven',        'oven',  83 ],
			[ 'oven_range_hood_price',         'Oven Range — hood',        'oven',  38 ],
			[ 'oven_range_gas_hobs_price',     'Oven Range — gas hobs',    'oven',  30 ],
			[ 'oven_range_electric_hobs_price','Oven Range — electric hobs','oven', 25 ],
			[ 'oven_range_ceramic_hobs_price', 'Oven Range — ceramic hobs','oven',  20 ],
			[ 'oven_range_extractor_price',    'Oven Range — extractor',   'oven',  39 ],
			[ 'oven_range_wide_price',         'Oven Range — wide',        'oven',  78 ],
			[ 'oven_range_half_size_price',    'Oven Range — half size',   'oven',  55 ],
			[ 'oven_warming_drawer_price',     'Oven — warming drawer',    'oven',  14 ],
			[ 'oven_master_range_90_price',    'Oven Master Range 90cm',   'oven', 154 ],
			[ 'oven_master_range_100_price',   'Oven Master Range 100cm',  'oven', 169 ],
			[ 'oven_two_size_price',           'Oven — 2-size',            'oven', 124 ],
			[ 'oven_four_size_price',          'Oven — 4-size',            'oven', 147 ],
			[ 'oven_side_module_price',        'Oven — side module',       'oven',  66 ],
			[ 'oven_small_bbq_price',          'Oven — small BBQ',         'oven',  70 ],
			[ 'oven_large_bbq_price',          'Oven — large BBQ',         'oven',  95 ],
			[ 'oven_combo_single_price',       'Oven Combo — single+hob+extractor','oven',106 ],
			[ 'oven_combo_double_price',       'Oven Combo — double+hob+extractor','oven',124 ],
			[ 'oven_combo_range90_price',      'Oven Combo — Range90+hob+extractor','oven',149],
			[ 'oven_combo_range100_price',     'Oven Combo — Range100+hob+extractor','oven',164],

			// Hard floor
			[ 'hard_floor_regular_price', 'Hard Floor — regular per sq m', 'hard_floor', 4 ],
			[ 'hard_floor_wood_price',    'Hard Floor — wood per sq m',    'hard_floor', 6 ],

			// End of tenancy keys
			[ 'end_of_tenancy_keys_near_price', 'End of Tenancy — keys nearby',     'end_of_tenancy', 10 ],
			[ 'end_of_tenancy_keys_far_price',  'End of Tenancy — keys far',         'end_of_tenancy', 20 ],

			// Gardening rubbish
			[ 'rubbish_clearance_small_bag_price', 'Rubbish Clearance — small bag',  'rubbish',   4.5 ],
			[ 'rubbish_clearance_jumbo_bag_price',  'Rubbish Clearance — jumbo bag', 'rubbish',  60   ],
		];

		foreach ( $prices as [ $key, $label, $group, $amount ] ) {
			$wpdb->insert( "{$p}fome_prices", [
				'price_key'   => $key,
				'label'       => $label,
				'price_group' => $group,
				'amount'      => $amount,
			] );
		}
	}

	private static function seed_postcodes(): void {
		global $wpdb;
		$p = $wpdb->prefix;

		$outcodes = [ 'SW','SE','W','WC','E','EC','N','NW','NE','BR','CR','DA','EN','HA','IG','KT','RM','SM','TW','UB','WD' ];
		foreach ( $outcodes as $oc ) {
			$wpdb->insert( "{$p}fome_postcodes", [ 'outcode' => $oc ] );
		}
	}
}
