<?php
defined( 'ABSPATH' ) || exit;
/**
 * Service-specific option panels.
 * Each panel is hidden by default; JS shows the correct one when a service card is clicked.
 * All inputs feed into the form POST that goes to Fome_Booking_Submission_Handler.
 */
?>

<!-- =========================================================
     Regular Cleaning / Regular Cleaning Luxury
     ========================================================= -->
<div class="fome-options-panel" data-for="regular-cleaning" hidden>
	<div class="fome-counter-row">
		<div class="fome-counter">
			<label><?php _e( 'Bedrooms', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="bedrooms" data-min="1">−</button>
				<input type="number" name="bedrooms" id="fome-bedrooms" value="1" min="1" max="10" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="bedrooms" data-max="10">+</button>
			</div>
		</div>
		<div class="fome-counter">
			<label><?php _e( 'Bathrooms', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="bathrooms" data-min="1">−</button>
				<input type="number" name="bathrooms" id="fome-bathrooms" value="1" min="1" max="5" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="bathrooms" data-max="5">+</button>
			</div>
		</div>
	</div>

	<div class="fome-field">
		<label><?php _e( 'How often?', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="frequency">
			<button type="button" class="fome-chip fome-chip--selected" data-value="weekly"><?php _e( 'Weekly', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="fortnightly"><?php _e( 'Fortnightly', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="frequently"><?php _e( 'More than weekly', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="frequency" value="weekly">
	</div>

	<div class="fome-field">
		<label><?php _e( 'Hours of cleaning', 'fome-booking' ); ?></label>
		<div class="fome-counter__controls">
			<button type="button" class="fome-counter__btn" data-action="dec" data-target="cleaning-hours" data-min="2">−</button>
			<input type="number" name="hours" id="fome-cleaning-hours" value="2" min="2" max="20" readonly>
			<button type="button" class="fome-counter__btn" data-action="inc" data-target="cleaning-hours" data-max="20">+</button>
		</div>
		<span class="fome-field__note"><?php _e( 'Minimum 2 hrs. We recommend 1 hr per bedroom.', 'fome-booking' ); ?></span>
	</div>

	<div class="fome-field">
		<label><?php _e( 'Additional tasks (optional)', 'fome-booking' ); ?></label>
		<div class="fome-chip-group fome-chip-group--multi" data-name="extra-tasks">
			<button type="button" class="fome-chip" data-value="Inside fridge"><?php _e( 'Inside fridge', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="Laundry"><?php _e( 'Laundry', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="Inside oven"><?php _e( 'Inside oven', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="Inside windows"><?php _e( 'Inside windows', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="Ironing"><?php _e( 'Ironing', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="extra-tasks" value="">
	</div>

	<div class="fome-field fome-checkbox-field">
		<label>
			<input type="checkbox" name="have-pets" value="1" id="fome-have-pets">
			<?php _e( 'I have pets', 'fome-booking' ); ?>
		</label>
		<textarea name="pets" id="fome-pets-detail" rows="2" placeholder="<?php esc_attr_e( 'What pets do you have?', 'fome-booking' ); ?>" hidden></textarea>
	</div>
</div>

<!-- =========================================================
     One-Off Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="one-off-cleaning" hidden>
	<div class="fome-counter-row">
		<div class="fome-counter">
			<label><?php _e( 'Bedrooms', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="oo-bedrooms" data-min="0">−</button>
				<input type="number" name="bedrooms" id="fome-oo-bedrooms" value="1" min="0" max="10" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="oo-bedrooms" data-max="10">+</button>
			</div>
		</div>
		<div class="fome-counter">
			<label><?php _e( 'Bathrooms', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="oo-bathrooms" data-min="0">−</button>
				<input type="number" name="bathrooms" id="fome-oo-bathrooms" value="1" min="0" max="5" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="oo-bathrooms" data-max="5">+</button>
			</div>
		</div>
	</div>
	<div class="fome-field">
		<label><?php _e( 'Property type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="property-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="flat"><?php _e( 'Flat', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="house"><?php _e( 'House', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="property-type" value="flat">
	</div>
</div>

<!-- =========================================================
     End of Tenancy Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="end-of-tenancy-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Property type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="eot-property-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="flat"><?php _e( 'Flat / Studio', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="house"><?php _e( 'House', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="property-type" value="flat">
	</div>
	<div class="fome-counter-row">
		<div class="fome-counter">
			<label><?php _e( 'Bedrooms', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="eot-bedrooms" data-min="0">−</button>
				<input type="number" name="bedrooms" id="fome-eot-bedrooms" value="1" min="0" max="10" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="eot-bedrooms" data-max="10">+</button>
			</div>
		</div>
	</div>
	<div class="fome-field">
		<label><?php _e( 'Furnished?', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="furnished">
			<button type="button" class="fome-chip fome-chip--selected" data-value="furnished"><?php _e( 'Furnished', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="unfurnished"><?php _e( 'Unfurnished', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="furnished" value="furnished">
	</div>
</div>

<!-- =========================================================
     After Builders Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="after-builders-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Property size', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="ab-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="studio"><?php _e( 'Studio', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="1bed"><?php _e( '1 Bed', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="2bed"><?php _e( '2 Bed', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="3bed"><?php _e( '3 Bed', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="4bed"><?php _e( '4 Bed', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="ab-size" value="studio">
	</div>
</div>

<!-- =========================================================
     Carpet Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="carpet-cleaning" hidden>
	<div class="fome-counter">
		<label><?php _e( 'Number of rooms', 'fome-booking' ); ?></label>
		<div class="fome-counter__controls">
			<button type="button" class="fome-counter__btn" data-action="dec" data-target="carpet-rooms" data-min="1">−</button>
			<input type="number" name="carpet-rooms" id="fome-carpet-rooms" value="1" min="1" max="20" readonly>
			<button type="button" class="fome-counter__btn" data-action="inc" data-target="carpet-rooms" data-max="20">+</button>
		</div>
	</div>
	<div class="fome-field">
		<label><?php _e( 'Treatment type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="fabric-or-steam">
			<button type="button" class="fome-chip fome-chip--selected" data-value="steam"><?php _e( 'Steam', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="dry-cleaning"><?php _e( 'Dry cleaning', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="fabric-or-steam" value="steam">
	</div>
	<div class="fome-field">
		<label><?php _e( 'Add-ons (optional)', 'fome-booking' ); ?></label>
		<div class="fome-chip-group fome-chip-group--multi" data-name="carpet-addons">
			<button type="button" class="fome-chip" data-value="scotchgard"><?php _e( 'Scotchgard protection', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="deodoriser"><?php _e( 'Deodoriser', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="carpet-addons" value="">
	</div>
	<div class="fome-field fome-checkbox-field">
		<label>
			<input type="checkbox" name="pets" value="1">
			<?php _e( 'Pets in property', 'fome-booking' ); ?>
		</label>
	</div>
	<div class="fome-field fome-checkbox-field">
		<label>
			<input type="checkbox" name="stains" value="1">
			<?php _e( 'Heavy stains / soiling', 'fome-booking' ); ?>
		</label>
	</div>
</div>

<!-- =========================================================
     Upholstery Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="upholstery-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Sofa type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="sofa-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="2seater"><?php _e( '2-seater', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="3seater"><?php _e( '3-seater', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="corner"><?php _e( 'Corner sofa', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="armchair"><?php _e( 'Armchair', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="sofa-type" value="2seater">
	</div>
	<div class="fome-field">
		<label><?php _e( 'Treatment type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="upholstery-fabric-or-steam">
			<button type="button" class="fome-chip fome-chip--selected" data-value="steam"><?php _e( 'Steam', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="dry-cleaning"><?php _e( 'Dry cleaning', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="fabric-or-steam" value="steam">
	</div>
</div>

<!-- =========================================================
     Mattress Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="mattress-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Mattress size', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="mattress-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="single"><?php _e( 'Single', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="double"><?php _e( 'Double', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="king"><?php _e( 'King', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="super-king"><?php _e( 'Super king', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="mattress-size" value="single">
	</div>
	<div class="fome-field">
		<label><?php _e( 'Treatment type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="mattress-fabric-or-steam">
			<button type="button" class="fome-chip fome-chip--selected" data-value="steam"><?php _e( 'Steam', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="dry-cleaning"><?php _e( 'Dry cleaning', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="fabric-or-steam" value="steam">
	</div>
</div>

<!-- =========================================================
     Window Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="window-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Property type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="window-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="residential"><?php _e( 'Residential', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="commercial"><?php _e( 'Commercial', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="window-type" value="residential">
	</div>
	<div class="fome-counter">
		<label><?php _e( 'Number of windows', 'fome-booking' ); ?></label>
		<div class="fome-counter__controls">
			<button type="button" class="fome-counter__btn" data-action="dec" data-target="window-count" data-min="1">−</button>
			<input type="number" name="window-count" id="fome-window-count" value="1" min="1" max="100" readonly>
			<button type="button" class="fome-counter__btn" data-action="inc" data-target="window-count" data-max="100">+</button>
		</div>
	</div>
	<div class="fome-counter">
		<label><?php _e( 'Number of floors', 'fome-booking' ); ?></label>
		<div class="fome-counter__controls">
			<button type="button" class="fome-counter__btn" data-action="dec" data-target="window-floors" data-min="1">−</button>
			<input type="number" name="window-floors" id="fome-window-floors" value="1" min="1" max="20" readonly>
			<button type="button" class="fome-counter__btn" data-action="inc" data-target="window-floors" data-max="20">+</button>
		</div>
	</div>
</div>

<!-- =========================================================
     Oven Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="oven-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Oven type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="oven-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="single"><?php _e( 'Single oven', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="double"><?php _e( 'Double oven', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="range"><?php _e( 'Range cooker', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="microwave"><?php _e( 'Microwave only', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="oven-type" value="single">
	</div>
	<div class="fome-counter-row">
		<div class="fome-counter">
			<label><?php _e( 'Extra trays', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="oven-trays" data-min="0">−</button>
				<input type="number" name="oven-trays" id="fome-oven-trays" value="0" min="0" max="10" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="oven-trays" data-max="10">+</button>
			</div>
		</div>
		<div class="fome-counter">
			<label><?php _e( 'Extra shelves', 'fome-booking' ); ?></label>
			<div class="fome-counter__controls">
				<button type="button" class="fome-counter__btn" data-action="dec" data-target="oven-shelves" data-min="0">−</button>
				<input type="number" name="oven-shelves" id="fome-oven-shelves" value="0" min="0" max="10" readonly>
				<button type="button" class="fome-counter__btn" data-action="inc" data-target="oven-shelves" data-max="10">+</button>
			</div>
		</div>
	</div>
</div>

<!-- =========================================================
     Curtain Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="curtain-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Curtain type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="curtain-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="pair"><?php _e( 'Pair', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="single"><?php _e( 'Single panel', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="curtain-type" value="pair">
	</div>
	<div class="fome-counter">
		<label><?php _e( 'Number of curtains', 'fome-booking' ); ?></label>
		<div class="fome-counter__controls">
			<button type="button" class="fome-counter__btn" data-action="dec" data-target="curtain-count" data-min="1">−</button>
			<input type="number" name="curtain-count" id="fome-curtain-count" value="1" min="1" max="20" readonly>
			<button type="button" class="fome-counter__btn" data-action="inc" data-target="curtain-count" data-max="20">+</button>
		</div>
	</div>
</div>

<!-- =========================================================
     Rubbish Removal
     ========================================================= -->
<div class="fome-options-panel" data-for="rubbish-removal" hidden>
	<div class="fome-field">
		<label><?php _e( 'Volume', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="rubbish-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="small"><?php _e( 'Small (up to ¼ van)', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="medium"><?php _e( 'Medium (up to ½ van)', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="large"><?php _e( 'Large (full van)', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="rubbish-size" value="small">
	</div>
</div>

<!-- =========================================================
     Hard Floor Cleaning
     ========================================================= -->
<div class="fome-options-panel" data-for="hard-floor-cleaning" hidden>
	<div class="fome-field">
		<label><?php _e( 'Floor area', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="floor-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="small"><?php _e( 'Small (up to 30m²)', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="medium"><?php _e( 'Medium (30–60m²)', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="large"><?php _e( 'Large (60m²+)', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="floor-size" value="small">
	</div>
</div>

<!-- =========================================================
     Wooden Floor Polishing
     ========================================================= -->
<div class="fome-options-panel" data-for="wooden-floor-polishing" hidden>
	<div class="fome-field">
		<label><?php _e( 'Floor area', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="wooden-floor-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="small"><?php _e( 'Small (up to 30m²)', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="medium"><?php _e( 'Medium (30–60m²)', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="large"><?php _e( 'Large (60m²+)', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="floor-size" value="small">
	</div>
</div>

<!-- =========================================================
     Gardening
     ========================================================= -->
<div class="fome-options-panel" data-for="gardening" hidden>
	<div class="fome-field">
		<label><?php _e( 'Garden size', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="garden-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="small"><?php _e( 'Small', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="medium"><?php _e( 'Medium', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="large"><?php _e( 'Large', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="garden-size" value="small">
	</div>
</div>

<!-- =========================================================
     Car Valeting
     ========================================================= -->
<div class="fome-options-panel" data-for="car-valeting" hidden>
	<div class="fome-field">
		<label><?php _e( 'Car type', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="car-type">
			<button type="button" class="fome-chip fome-chip--selected" data-value="hatchback"><?php _e( 'Hatchback', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="saloon"><?php _e( 'Saloon', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="estate"><?php _e( 'Estate / MPV', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="suv"><?php _e( 'SUV / 4×4', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="van"><?php _e( 'Van', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="car-type" value="hatchback">
	</div>
</div>

<!-- =========================================================
     Antiviral Sanitisation
     ========================================================= -->
<div class="fome-options-panel" data-for="antiviral-sanitisation" hidden>
	<div class="fome-field">
		<label><?php _e( 'Property size', 'fome-booking' ); ?></label>
		<div class="fome-chip-group" data-name="antiviral-size">
			<button type="button" class="fome-chip fome-chip--selected" data-value="studio"><?php _e( 'Studio', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="1bed"><?php _e( '1 Bed', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="2bed"><?php _e( '2 Bed', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="3bed"><?php _e( '3 Bed', 'fome-booking' ); ?></button>
			<button type="button" class="fome-chip" data-value="4bed"><?php _e( '4 Bed', 'fome-booking' ); ?></button>
		</div>
		<input type="hidden" name="antiviral-size" value="studio">
	</div>
</div>

<!-- =========================================================
     Commercial / free-quote services — no pricing panel needed
     ========================================================= -->
<div class="fome-options-panel fome-options-panel--quote" data-for="commercial" hidden>
	<p class="fome-notice"><?php _e( 'Fill in your details and we\'ll get back to you with a tailored quote.', 'fome-booking' ); ?></p>
</div>
