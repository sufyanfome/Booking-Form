<div id="window" class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">Small window</div>
		<div class="service-option-price">&pound;<?=WINDOW_SMALL_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="window-small" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Medium window</div>
		<div class="service-option-price">&pound;<?=WINDOW_MEDIUM_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="window-medium" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Large window</div>
		<div class="service-option-price">&pound;<?=WINDOW_LARGE_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="window-large" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Bay window</div>
		<div class="service-option-price">&pound;<?=WINDOW_BAY_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="window-bay" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">French door</div>
		<div class="service-option-price">&pound;<?=WINDOW_FRENCH_DOOR_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="window-french-door" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="service-option">
	<div class="checkbox-wrapper no-margin">
		<label class="label"><input class="added-cost" type="checkbox" name="window-internal-cleaning" class="calculate" data-added-cost="50%" data-service="window">Do you also require internal windows cleaning?</label>
	</div>
	<div class="notice">Adding internal cleaning will increase the price of the service by 50%.</div>
</div>

<div class="service-option">
	<label class="label">Would you be able to provide us with:</label>
	<div class="input-wrapper-half">
		<div class="checkbox-wrapper">
			<div class="label"><input type="checkbox" name="electricity" class="toggle" data-toggle="electric-outlet">Electricity</div>
		</div>
	</div>
	<div class="input-wrapper-half">
		<div class="checkbox-wrapper">
			<div class="label"><input type="checkbox" name="water" class="toggle" data-toggle="water-tap">Water</div >
		</div>
	</div>
	<div style="clear: both"></div>

	<div id="electric-outlet" class="input-wrapper" hidden>
		<div class="label">Where is the electrical outlet located:</div>
		<div class="radio-wrapper">
			<div class="radio">
				<input type="radio" value="inside" name="outlet-location" checked="checked">
				Inside
			</div>
			<div class="radio">
				<input type="radio" value="outside" name="outlet-location">
				Outside
			</div>
		</div>
	</div>

	<div id="water-tap" class="input-wrapper" hidden>
		<div class="label">Where is your water tap located:</div>
		<div class="radio-wrapper">
			<div class="radio">
				<input type="radio" value="inside" name="tap-location" checked="checked">
				Inside
			</div>
			<div class="radio">
				<input type="radio" value="outside" name="tap-location">
				Outside
			</div>
		</div>
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
		<div class="label"><input type="checkbox" class="add-service" name="added-curtain-cleaning" data-add-service="<?=CURTAIN_CLEANING_SERVICE?>" />Curtain</div>
	</div>
	<div class="input-wrapper-half">
		<div class="label"><input type="checkbox" class="add-service" name="added-oven-cleaning" data-add-service="<?=OVEN_CLEANING_SERVICE?>" />Oven</div>
	</div>
	<div class="clearfix"></div>
</div>
	
<input type="hidden" class="minimum-booking" value="<?=WINDOW_CLEANING_MINIMUM_BOOKING?>" />