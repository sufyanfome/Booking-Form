<div class="title">Details</div>
<div class="label">Full name</div>
<div class="input-wrapper">
	<input class="required" type="text" id="name" name="name" />
	<span class="error-message"></span>
</div>

<div class="label">Email</div>
<div class="input-wrapper">
	<input class="required validateEmail" type="text" id="email" name="email">
	<span class="error-message"></span>
</div>

<div class="label">Postcode</div>
<div class="input-wrapper">
	<input class="required validatePostcode" type="text" id="postcode" name="postcode">
	<span class="error-message"></span>
</div>
<!--
<div class="label">what3words address: (optional)</div>
<div class="input-wrapper">
<what3words-autosuggest  />
</div>
-->
<div class="label">Address</div>
<div class="input-wrapper">
	<input class="required" type="text" id="address" name="address" autocomplete="street-address">
	<span class="error-message"></span>
</div>

<div class="label">Phone number</div>
<div class="input-wrapper">
	<input class="required" type="text" id="phone" name="phone">
	<span class="error-message"></span>
</div>

<div class="label">Preferred calling hours</div>
<div class="input-wrapper">
	<select name="calling-hours">
		<option>Anytime</option>
		<option>Before working hours</option>
		<option>During working hours</option>
		<option>After working hours</option>
	</select>
</div>

<div class="label">Additional notes (optional)</div>
<div class="input-wrapper">
	<textarea id="notes" name="note"></textarea>
</div>

<div id="file-uploads">
	<div class="label">Photo attachments (optional)</div>
	<div class="input-wrapper">
	  <input class="attach-photo" type="file" name="file-attachment-1" />
	</div>
</div>

<div class="checkbox-wrapper parking" hidden>
	<label class="label"><input class="toggle required" type="checkbox" name="parking" data-toggle="parking-type" data-error-id="parking-error" data-error-message="We cannot carry out the service if you are not able to provide us with a parking space">Would you be able to provide us with a parking space?</label>
</div>
<div id="parking-error" class="error-message"></div>

 <div id="parking-type" hidden>
	 <div class="label">What type of parking are you able to provide us with?</div>
	 <div class="radio-wrapper">
		<div class="radio"><input type="radio" value="free-parking" name="parking-type" checked>Free parking available</div>
		<div class="radio"><input type="radio" value="parking-permit" name="parking-type">Will provide a parking permit</div>
		<div class="radio"><input type="radio" value="parking-meter" name="parking-type">Will provide change for parking meter</div>
		<div class="radio"><input type="radio" value="invoice" name="parking-type">Will cover parking charge upon invoice/receipt</div>
		<div class="radio"><input type="radio" value="cash" name="parking-type">Will cover charges in cash</div>
	</div>
 </div>

<div class="checkbox-wrapper congestion-charge" hidden>
	<label class="label"><input id="congestion-charge" type="checkbox" name="congestion-charge" class="calculate" title="+27.5£">Is your property within the Congestion Charge Zone?</label>
</div>

<?php
if(GIFT_CARDS_ENABLED) {
	?>
	<div class="gift-cards-container">
		<div class="input-wrapper-half">
			<label class="label">Gift card code (optional)</label>
			<input type="text" name="gift-card-code" id="gift-card-code" />
			<div id="gift-card-message"></div>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php
}
?>

<input type="hidden" id="final-price" name="price" />
<input type="hidden" id="original-price" name="original-price" />
<input type="hidden" id="gift-card-code" name="gift-card-code"  />
<input type="hidden" id="gift-card-amount" name="gift-card-amount" />
<input type="hidden" id="date" name="date" />
<input type="hidden" id="time" name="time" />

<script src="https://assets.what3words.com/sdk/v3.1/what3words.js?key=X0OJZXEM"></script>

<script>
	console.log(giftCardCode);
	
	if($('.discounted-price').length > 0) {
		$('#final-price').val($('.discounted-price').first().html().replace('£', ''));
		$('#original-price').val($('.original-price').first().html().replace('£', ''));
	} else {
		$('#final-price').val($('.original-price').first().html().replace('£', ''));
	}
	if(chosenDate !== false) {
		$('#date').val(chosenDate);
	}
	
	$('#time').val($('.selected-timeslot').first().html());
	
	if(chosenService !== '<?=REGULAR_CLEANING_SERVICE?>' && chosenService !== '<?=REGULAR_LUXURY_CLEANING_SERVICE?>') {
		$('.parking').show();
		$('.congestion-charge').show();
	}
	
	$('#congestion-charge').qtip();
</script>
