<div class="service-option">
	<div class="service-option-title">Area (sq. m.)</div>
	<div id="antiviral-sanitisation-price" class="service-option-price">£<?=ANTIVIRAL_SANITISATION_COMMERCIAL_200_PRICE?></div>
	<div class="service-option-amount">
		<input type="text" value="0" id="antiviral-sanitisation-commercial-area" class="amount amount-custom" data-type="hours" name="antiviral-sanitisation-commercial-area" min="0" max="5000">
	</div>
	<div class="clearfix"></div>
</div>

<div class="service-option">
	<div class="service-option-title">Computers</div>
	<div class="service-option-price">£<?=ANTIVIRAL_SANITISATION_COMMERCIAL_COMPUTER_PRICE?></div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" disabled value="0" id="antiviral-sanitisation-commercial-computers" class="amount" data-type="hours" name="antiviral-sanitisation-commercial-computers" min="0" max="100">
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
	<div class="notice">Monitor, terminal, keypad, mouse</div>
</div>

<div class="title">Antiviral Sanitisation for Hotel Rooms</div>

<div class="service-option">
	<div class="service-option-title">Rooms</div>
	<div class="service-option-price">£<?=ANTIVIRAL_SANITISATION_COMMERCIAL_HOTEL_ROOMS_PRICE?></div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" disabled value="4" id="antiviral-sanitisation-commercial-hotel-rooms" class="amount" data-type="hours" name="antiviral-sanitisation-commercial-hotel-rooms" min="4" max="200">
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
</div>

<input type="hidden" class="minimum-booking" value="<?=ANTIVIRAL_SANITISATION_COMMERCIAL_MINIMUM_BOOKING?>" />

<script>
	var prices = {
		0: <?=ANTIVIRAL_SANITISATION_COMMERCIAL_200_PRICE?>,
		199: <?=ANTIVIRAL_SANITISATION_COMMERCIAL_500_PRICE?>,
		499: <?=ANTIVIRAL_SANITISATION_COMMERCIAL_750_PRICE?>,
		749: <?=ANTIVIRAL_SANITISATION_COMMERCIAL_ABOVE_750_PRICE?>,
	};
	
	$("#antiviral-sanitisation-commercial-area").change(function() {
		var area = parseInt($(this).val());
		var pricePerSqMeter = <?=ANTIVIRAL_SANITISATION_COMMERCIAL_200_PRICE?>;
		for(price in prices) {
			if(area > price) {
				pricePerSqMeter = prices[price];
			}
		}

		$('#antiviral-sanitisation-price').html('£'+pricePerSqMeter);
	});
</script>
