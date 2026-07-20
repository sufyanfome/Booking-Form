<div class="change-service-type-container">
	<div class="change-service-type active" data-service-type="mattress-steam">Steam</div>
	<div class="change-service-type" data-service-type="mattress-dry">Dry</div>
</div>
<div id="mattress-steam" class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">Babycot</div>
		<div class="service-option-price">&pound;<?=MATTRESS_STEAM_BABYCOT_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="mattress-steam-babycot" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Single mattress</div>
		<div class="service-option-price">&pound;<?=MATTRESS_STEAM_SINGLE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-steam-single" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Double mattress</div>
		<div class="service-option-price">&pound;<?=MATTRESS_STEAM_DOUBLE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-steam-double" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Queen size</div>
		<div class="service-option-price">&pound;<?=MATTRESS_STEAM_QUEEN_SIZE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-steam-queen-size" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">King size</div>
		<div class="service-option-price">&pound;<?=MATTRESS_STEAM_KING_SIZE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-steam-king-size" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<!-- Dry cleaning -->

<div id="mattress-dry" class="service-options-container" hidden>
	<div class="service-option">
		<div class="service-option-title">Single mattress</div>
		<div class="service-option-price">&pound;<?=MATTRESS_DRY_SINGLE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-dry-single" min="0" max="20" data-type="(dry)">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Double mattress</div>
		<div class="service-option-price">&pound;<?=MATTRESS_DRY_DOUBLE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-dry-double" min="0" max="20" data-type="(dry)">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Queen size</div>
		<div class="service-option-price">&pound;<?=MATTRESS_DRY_QUEEN_SIZE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-dry-queen-size" min="0" max="20" data-type="(dry)">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">King size</div>
		<div class="service-option-price">&pound;<?=MATTRESS_DRY_KING_SIZE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0"  class="amount" name="mattress-dry-king-size" min="0" max="20" data-type="(dry)">
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
		<div class="label"><input type="checkbox" class="add-service" name="added-upholstery-cleaning" data-add-service="<?=UPHOLSTERY_CLEANING_SERVICE?>" />Upholstery</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-curtain-cleaning" data-add-service="<?=CURTAIN_CLEANING_SERVICE?>" />Curtain</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-window-cleaning" data-add-service="<?=WINDOW_CLEANING_SERVICE?>" />Window</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-oven-cleaning" data-add-service="<?=OVEN_CLEANING_SERVICE?>" />Oven</div>
	</div>
	<div class="clearfix"></div>
</div>

<input type="hidden" class="minimum-booking" value="<?=MATTRESS_CLEANING_MINIMUM_BOOKING?>" />