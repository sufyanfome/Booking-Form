<div class="service-options-container">

	<div class="service-option">
		<div class="service-option-title">One-Off Cleaning</div>
		<div class="service-option-price">£<?=ONE_OFF_CLEANING_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="5" id="one-off-cleaning-hours" class="amount" data-type="hours" name="one-off-cleaning-hours" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="service-option">
		<div class="service-option-title">One-Off Cleaning + Equipment</div>
		<div class="service-option-price">£<?=ONE_OFF_CLEANING_EQUIPMENT_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" id="one-off-equipment-cleaning-hours" class="amount" data-type="hours" name="one-off-equipment-cleaning-hours" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
		<div class="notice">We provide all the cleaning equipment</div>
	</div>
	
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

<input type="hidden" class="minimum-booking" value="<?=ONE_OFF_CLEANING_MINIMUM_BOOKING?>" />

<script>
	$('#one-off-cleaning-hours').change(function() {
		$('#one-off-equipment-cleaning-hours').val(0);
	});
	
	$('#one-off-equipment-cleaning-hours').change(function() {
		$('#one-off-cleaning-hours').val(0);
	});
</script>