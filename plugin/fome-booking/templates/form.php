<?php
defined( 'ABSPATH' ) || exit;
/**
 * Main booking form template — 3-step layout.
 * Variables available: $config, $brand_color, $services
 */
$domestic_services    = array_filter( $services, fn( $s ) => ( $s['category'] ?? 'domestic' ) === 'domestic' );
$commercial_services  = array_filter( $services, fn( $s ) => ( $s['category'] ?? '' ) === 'commercial' );
?>
<div class="fome-booking" style="--fome-brand:<?php echo esc_attr( $brand_color ); ?>">

	<!-- Progress bar -->
	<div class="fome-progress" aria-label="<?php esc_attr_e( 'Booking steps', 'fome-booking' ); ?>">
		<div class="fome-progress__step fome-progress__step--active" data-step="1">
			<span class="fome-progress__num">1</span>
			<span class="fome-progress__label"><?php _e( 'Service', 'fome-booking' ); ?></span>
		</div>
		<div class="fome-progress__connector"></div>
		<div class="fome-progress__step" data-step="2">
			<span class="fome-progress__num">2</span>
			<span class="fome-progress__label"><?php _e( 'Your details', 'fome-booking' ); ?></span>
		</div>
		<div class="fome-progress__connector"></div>
		<div class="fome-progress__step" data-step="3">
			<span class="fome-progress__num">3</span>
			<span class="fome-progress__label"><?php _e( 'Review & confirm', 'fome-booking' ); ?></span>
		</div>
	</div>

	<form id="fome-booking-form" novalidate>
		<?php wp_nonce_field( 'fome_booking_nonce', 'nonce' ); ?>

		<!-- ================================================================
		     STEP 1 — Service selection
		     ================================================================ -->
		<div class="fome-step" data-step="1">
			<h2 class="fome-step__heading"><?php _e( 'Select a service', 'fome-booking' ); ?></h2>

			<?php if ( ! empty( $domestic_services ) ) : ?>
			<div class="fome-service-group">
				<h3 class="fome-service-group__title"><?php _e( 'Domestic cleaning', 'fome-booking' ); ?></h3>
				<div class="fome-service-grid">
					<?php foreach ( $domestic_services as $svc ) : ?>
					<button type="button"
					        class="fome-service-card"
					        data-service="<?php echo esc_attr( $svc['name'] ); ?>">
						<?php echo esc_html( $svc['name'] ); ?>
					</button>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $commercial_services ) ) : ?>
			<div class="fome-service-group">
				<h3 class="fome-service-group__title"><?php _e( 'Commercial & office', 'fome-booking' ); ?></h3>
				<div class="fome-service-grid">
					<?php foreach ( $commercial_services as $svc ) : ?>
					<button type="button"
					        class="fome-service-card fome-service-card--commercial"
					        data-service="<?php echo esc_attr( $svc['name'] ); ?>">
						<?php echo esc_html( $svc['name'] ); ?>
					</button>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

			<input type="hidden" name="service" id="fome-service-input" required>

			<!-- Service-specific option panels — shown by JS when a service is selected -->
			<div id="fome-service-options" class="fome-service-options" hidden>
				<?php include __DIR__ . '/service-options.php'; ?>
			</div>

			<div class="fome-step__actions fome-step__actions--right">
				<button type="button" class="fome-btn fome-btn--primary" id="fome-step1-next" disabled>
					<?php _e( 'Continue', 'fome-booking' ); ?> &rarr;
				</button>
			</div>
		</div>

		<!-- ================================================================
		     STEP 2 — Customer details
		     ================================================================ -->
		<div class="fome-step" data-step="2" hidden>
			<h2 class="fome-step__heading"><?php _e( 'Your details', 'fome-booking' ); ?></h2>

			<div class="fome-field-row">
				<div class="fome-field">
					<label for="fome-name"><?php _e( 'Full name', 'fome-booking' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" id="fome-name" name="name" autocomplete="name" required>
				</div>
				<div class="fome-field">
					<label for="fome-phone"><?php _e( 'Phone number', 'fome-booking' ); ?> <span aria-hidden="true">*</span></label>
					<input type="tel" id="fome-phone" name="phone" autocomplete="tel" required>
				</div>
			</div>

			<div class="fome-field-row">
				<div class="fome-field">
					<label for="fome-email"><?php _e( 'Email address', 'fome-booking' ); ?> <span aria-hidden="true">*</span></label>
					<input type="email" id="fome-email" name="email" autocomplete="email" required>
				</div>
				<div class="fome-field">
					<label for="fome-postcode"><?php _e( 'Postcode', 'fome-booking' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" id="fome-postcode" name="postcode" autocomplete="postal-code" required>
					<span class="fome-field__note fome-postcode-status" aria-live="polite"></span>
				</div>
			</div>

			<div class="fome-field">
				<label for="fome-address"><?php _e( 'Address', 'fome-booking' ); ?></label>
				<textarea id="fome-address" name="address" rows="3" autocomplete="street-address"></textarea>
			</div>

			<div class="fome-field-row">
				<div class="fome-field">
					<label for="fome-calling-hours"><?php _e( 'Preferred calling hours', 'fome-booking' ); ?></label>
					<select id="fome-calling-hours" name="calling-hours">
						<option value="Anytime"><?php _e( 'Anytime', 'fome-booking' ); ?></option>
						<option value="Morning (9am–12pm)"><?php _e( 'Morning (9am–12pm)', 'fome-booking' ); ?></option>
						<option value="Afternoon (12pm–5pm)"><?php _e( 'Afternoon (12pm–5pm)', 'fome-booking' ); ?></option>
						<option value="Evening (5pm–8pm)"><?php _e( 'Evening (5pm–8pm)', 'fome-booking' ); ?></option>
					</select>
				</div>
				<div class="fome-field">
					<label for="fome-date"><?php _e( 'Preferred date', 'fome-booking' ); ?> <span aria-hidden="true">*</span></label>
					<input type="date" id="fome-date" name="date" required min="">
				</div>
			</div>

			<div class="fome-field">
				<label><?php _e( 'Preferred time slot', 'fome-booking' ); ?> <span aria-hidden="true">*</span></label>
				<div class="fome-time-slots" id="fome-time-slots">
					<!-- Populated by JS based on selected date + service -->
				</div>
				<input type="hidden" name="time" id="fome-time-input" required>
			</div>

			<div class="fome-field">
				<label for="fome-note"><?php _e( 'Additional notes', 'fome-booking' ); ?></label>
				<textarea id="fome-note" name="note" rows="3" placeholder="<?php esc_attr_e( 'Anything we should know?', 'fome-booking' ); ?>"></textarea>
			</div>

			<!-- Promo codes section -->
			<div class="fome-promo">
				<button type="button" class="fome-promo__toggle">
					<?php _e( '+ Have a discount code or gift card?', 'fome-booking' ); ?>
				</button>
				<div class="fome-promo__fields" hidden>
					<div class="fome-field-row">
						<div class="fome-field">
							<label for="fome-discount-code"><?php _e( 'Discount code', 'fome-booking' ); ?></label>
							<div class="fome-inline">
								<input type="text" id="fome-discount-code" name="discount-code"
								       placeholder="FRIEND10" style="text-transform:uppercase">
								<button type="button" class="fome-btn fome-btn--secondary" id="fome-apply-discount">
									<?php _e( 'Apply', 'fome-booking' ); ?>
								</button>
							</div>
							<span class="fome-field__note fome-discount-status" aria-live="polite"></span>
						</div>
						<div class="fome-field">
							<label for="fome-gift-card-code"><?php _e( 'Gift card', 'fome-booking' ); ?></label>
							<div class="fome-inline">
								<input type="text" id="fome-gift-card-code" name="gift-card-code"
								       placeholder="GIFT100" style="text-transform:uppercase">
								<button type="button" class="fome-btn fome-btn--secondary" id="fome-apply-gift">
									<?php _e( 'Apply', 'fome-booking' ); ?>
								</button>
							</div>
							<span class="fome-field__note fome-gift-status" aria-live="polite"></span>
						</div>
					</div>
				</div>
			</div>

			<div class="fome-step__actions fome-step__actions--between">
				<button type="button" class="fome-btn fome-btn--ghost" id="fome-step2-back">
					&larr; <?php _e( 'Back', 'fome-booking' ); ?>
				</button>
				<button type="button" class="fome-btn fome-btn--primary" id="fome-step2-next">
					<?php _e( 'Review booking', 'fome-booking' ); ?> &rarr;
				</button>
			</div>
		</div>

		<!-- ================================================================
		     STEP 3 — Review & confirm
		     ================================================================ -->
		<div class="fome-step" data-step="3" hidden>
			<h2 class="fome-step__heading"><?php _e( 'Review &amp; confirm', 'fome-booking' ); ?></h2>

			<div class="fome-review">
				<div class="fome-review__section">
					<h3><?php _e( 'Service', 'fome-booking' ); ?></h3>
					<dl class="fome-review__list" id="fome-review-service"></dl>
				</div>
				<div class="fome-review__section">
					<h3><?php _e( 'Your details', 'fome-booking' ); ?></h3>
					<dl class="fome-review__list" id="fome-review-details"></dl>
				</div>
				<div class="fome-review__section fome-review__section--price">
					<div class="fome-price-display" id="fome-price-display">
						<span class="fome-price-display__label"><?php _e( 'Total', 'fome-booking' ); ?></span>
						<span class="fome-price-display__amount" id="fome-total-price"></span>
					</div>
					<p class="fome-price-display__note" id="fome-price-note"></p>
				</div>
			</div>

			<p class="fome-terms">
				<?php
				printf(
					wp_kses(
						__( 'By confirming, you agree to our <a href="%s">Terms &amp; Conditions</a>.', 'fome-booking' ),
						[ 'a' => [ 'href' => [] ] ]
					),
					esc_url( home_url( '/terms/' ) )
				);
				?>
			</p>

			<div class="fome-step__actions fome-step__actions--between">
				<button type="button" class="fome-btn fome-btn--ghost" id="fome-step3-back">
					&larr; <?php _e( 'Back', 'fome-booking' ); ?>
				</button>
				<button type="submit" class="fome-btn fome-btn--primary fome-btn--large" id="fome-submit">
					<?php _e( 'Confirm &amp; pay', 'fome-booking' ); ?>
				</button>
			</div>
		</div>

		<!-- Global error/loading state -->
		<div class="fome-alert fome-alert--error" id="fome-error" role="alert" hidden></div>
		<div class="fome-loading" id="fome-loading" hidden aria-label="<?php esc_attr_e( 'Processing…', 'fome-booking' ); ?>">
			<span class="fome-loading__spinner"></span>
			<span><?php _e( 'Processing…', 'fome-booking' ); ?></span>
		</div>

	</form>
</div>
