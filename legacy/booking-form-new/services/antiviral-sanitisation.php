<div class="service-option">
	<div class="service-option-title">Area (sq. m.)</div>
	<div class="service-option-price">£<?=ANTIVIRAL_SANITISATION_PRICE?></div>
	<div class="service-option-amount">
		<input type="text" value="0" id="antiviral-sanitisation-area" class="amount amount-custom" data-type="sq.m." name="antiviral-sanitisation-area" min="0" max="1000">
	</div>
	<div class="clearfix"></div>
	<div class="notice">Price is calculated per square meter</div>
	<div id="area-notice" class="notice" hidden>For areas above 1000 sq. m., please <a class="link" href="<?=CONTACT_PAGE?>">contact us</a> for a free quote</div>
</div>

<div class="service-option">
	<div class="service-option-title">Computers</div>
	<div class="service-option-price">£<?=ANTIVIRAL_SANITISATION_COMPUTER_PRICE?></div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" value="0" id="antiviral-sanitisation-computers" class="amount" data-type="" name="antiviral-sanitisation-computers" min="0" max="20">
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
	<div class="notice">Monitor, terminal, keypad, mouse</div>
</div>

<?php
    if(DISCOUNT_CODE === true) {
        ?>

        <label class="label">Discount code</label><input class="calculate" id="discount-code" name="discount-code"/>
        <?php
    }
?>

<input type="hidden" class="minimum-booking" value="<?=ANTIVIRAL_SANITISATION_MINIMUM_BOOKING?>" />

<script>
	$('#antiviral-sanitisation-area').change(function() {
		var area = parseInt($(this).val());
		var max = $(this).attr('max');
		
		if(area > max) {
			$(this).val(1000);
			$('#area-notice').show();
		} else {
			$('#area-notice').hide();
		}
	});
</script>