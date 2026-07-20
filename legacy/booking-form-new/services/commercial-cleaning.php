<div class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">Commercial Cleaning</div>
		<div class="service-option-price">£<?=COMMERCIAL_CLEANING_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" disabled value="2" id="cleaning-hours" class="amount" data-type="hours" name="commercial-cleaning-hours" min="2" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
		<div class="notice">Minimum - 2 hours</div>
	</div>
</div>

<input type="hidden" class="minimum-booking" value="<?=COMMERCIAL_CLEANING_MINIMUM_BOOKING?>" />