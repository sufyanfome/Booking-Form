<div class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">Bedrooms</div>
		<div class="service-option-price"></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="1" id="end-of-tenancy-bedrooms" class="amount calculate-custom" name="end-of-tenancy-bedrooms" min="0" max="5" data-calculate="endOfTenancyPrice">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Bathrooms</div>
		<div class="service-option-price"></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="1" id="end-of-tenancy-bathrooms" class="amount"  name="end-of-tenancy-bathrooms" min="0" max="5">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Additional rooms</div>
		<div class="service-option-price"></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" id="end-of-tenancy-additional" class="amount" name="end-of-tenancy-additional" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="label" style="margin-top: 10px;">What type is your property:</div>

<div class="input-wrapper-half">
	<select id="property-type" name="property-type" class="calculate">
		<option>Flat</option>
		<option>House</option>
	</select>
</div>
<div style="clear: both"></div>

<div class="checkbox-wrapper">
	<label class="label"><input id="keys" type="checkbox" name="collect-keys" class="calculate toggle" data-toggle="collect-keys">Do we need to collect the keys to the property from a different address?</label>
</div>
<div id="collect-keys" class="input-wrapper" hidden>
	<div  class="label">How far is that address from the property?</div>
	<div class="radio-wrapper">
		<div class="radio">
			<input class="calculate" type="radio" value="near" name="keys-distance" checked>
			Up to 15 minutes walking distance
		</div>
		<div class="radio">
			<input class="calculate" type="radio" value="far" name="keys-distance">
			More than 15 minutes walking distance
		</div>
	</div>
</div>


<div class="label" style="margin-top: 10px;">When would you be able to do an inventory check? (optional): </div>
<div class="input-wrapper-half">
	<input type="text" id="inventory-check-date" name="inventory-check-date">
	</div>
</div>
<div class="input-wrapper-half">
<select name="inventory-check-time">
	<option value="">- Select -</option> 
	<option value="morning">Morning</option>
	<option value="afternoon">Afternoon</option>
	<option value="evening">Evening</option>
</select>
</div>

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
<div class="clearfix"></div>

<input id="end-of-tenancy-minimum-booking" type="hidden" class="minimum-booking" value="<?=END_OF_TENANCY_CLEANING_FLAT_MINIMUM_BOOKING?>" />

<script>
$(document).ready(function() {
	$("#inventory-check-date").datepicker({
		minDate: +2, 
		maxDate: "+2M",
		setDate: new Date()
	});
	
	$('#property-type').change(function() {
		if($(this).find('option:selected').html() === 'Flat') {
			$('#end-of-tenancy-minimum-booking').val("<?=END_OF_TENANCY_CLEANING_FLAT_MINIMUM_BOOKING?>");
		} else {
			$('#end-of-tenancy-minimum-booking').val("<?=END_OF_TENANCY_CLEANING_HOUSE_MINIMUM_BOOKING?>");
		}
	});
});

</script>

<style>
#ui-datepicker-div {
	max-width: 200px;
}
</style>
