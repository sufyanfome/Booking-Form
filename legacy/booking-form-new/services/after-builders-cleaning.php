<div class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">After Builders Cleaning</div>
		<div class="service-option-price">&pound;<?=AFTER_BUILDERS_CLEANING_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="5" id="after-building-cleaning-hours" class="amount" data-type="hours" name="after-builders-cleaning-hours" min="5" max="20"><div class="field-desc"> hr</div>
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">After Builders Cleaning + Equipment</div>
		<div class="service-option-price">&pound;<?=AFTER_BUILDERS_EQUIPMENT_CLEANING_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" id="after-building-equipment-cleaning-hours" class="amount" data-type="hours" name="after-building-equipment-cleaning-hours" min="5" max="20"><div class="field-desc"> hr</div>
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>


<div class="title title-margin-top">Additional Tasks</div>
	<div class="service-option">
		<div class="service-option-title">Inside Fridge</div>
		<div class="service-option-price">£<?=ONE_OFF_INSIDE_FRIDGE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="inside-fridge" min="0" max="10">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>	

	<div class="service-option">
		<div class="service-option-title">Inside Freezer</div>
		<div class="service-option-price">£<?=ONE_OFF_INSIDE_FREEZER_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="inside-freezer" min="0" max="10">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>	

	<div class="service-option">
		<div class="service-option-title">Inside Cupboards</div>
		<div id="cupboards-price" class="service-option-price">£<?=ONE_OFF_INSIDE_CUPBOARDS_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="inside-cupboards" min="0" max="10">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="service-option">
		<div class="service-option-title">Wooden blinds</div>
		<div class="service-option-price">£<?=ONE_OFF_WOODEN_BLINDS_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="wooden-blinds" min="0" max="10">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>		

	<div class="service-option">
		<div class="service-option-title">Glass panel</div>
		<div class="service-option-price">£<?=ONE_OFF_GLASS_PANEL_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="glass-panel" min="0" max="10">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div	

	<div class="clearfix"></div>
	
	<div class="additional-services">
		<div class="title title-margin-top">Specialist Cleaning</div>
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
</div>

<input type="hidden" class="minimum-booking" value="<?=AFTER_BUILDERS_CLEANING_MINIMUM_BOOKING?>" />

<script>
	$('#after-building-cleaning-hours').change(function() {
		$('#after-building-equipment-cleaning-hours').val(0);
	});
	
	$('#after-building-equipment-cleaning-hours').change(function() {
		$('#after-building-cleaning-hours').val(0);
	});
</script>