<div class="change-service-type-container">
	<div class="change-service-type active" data-service-type="curtain-steam">Steam</div>
	<div class="change-service-type" data-service-type="curtain-dry">Dry</div>
</div>
<div id="curtain-steam" class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">Short</div>
		<div class="service-option-price">&pound;<?=CURTAIN_STEAM_SHORT_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="curtain-steam-short" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Full length</div>
		<div class="service-option-price">&pound;<?=CURTAIN_STEAM_FULL_LENGTH_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="curtain-steam-full-length" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>


<!-- Dry cleaning -->
<div id="curtain-dry" class="service-options-container" hidden>
	<div class="service-option">
		<div class="service-option-title">Short</div>
		<div class="service-option-price">&pound;<?=CURTAIN_DRY_SHORT_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="curtain-dry-short" min="0" max="20" data-type="(dry)">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Full length</div>
		<div class="service-option-price">&pound;<?=CURTAIN_DRY_FULL_LENGTH_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="curtain-dry-full-length" min="0" max="20" data-type="(dry)">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="additional-services">
<div class="title title-margin-top">Additional Services</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-carpet-cleaning" data-add-service="<?=CARPET_CLEANING_SERVICE?>" />Carpet</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-mattress-cleaning" data-add-service="<?=MATTRESS_CLEANING_SERVICE?>" />Mattress</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-upholstery-cleaning" data-add-service="<?=UPHOLSTERY_CLEANING_SERVICE?>" />Upholstery</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-window-cleaning" data-add-service="<?=WINDOW_CLEANING_SERVICE?>" />Window</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-oven-cleaning" data-add-service="<?=OVEN_CLEANING_SERVICE?>" />Oven</div>
	</div>
	<div class="clearfix"></div>
</div>

<input type="hidden" class="minimum-booking" value="<?=CURTAIN_CLEANING_MINIMUM_BOOKING?>" />