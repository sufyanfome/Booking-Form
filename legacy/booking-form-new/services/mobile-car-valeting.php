<div class="car-valeting-container">
	<div style="padding-left: 0 !important" class="service-option">
		<div style="font-size: 13px !important;" class="service-option-title">Vehicles</div>
		<div class="service-option-price"></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="1" class="amount not-service" data-type="hours" name="car-valeting-vehicle-amount-1" min="1" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="label">Vehicle type</div>
	<select class="calculate calculate-custom vehicle-type" name="vehicle-type-1" data-calculate="carValetingPrice">
		<option value="city">City/sports/convertible car</option>
		<option value="space-cruiser">Space cruiser</option>
		<option value="4x4">4x4</option>
		<option value="van">Van, truck</option>
	</select>

	<div class="label">Service type</div>
	<select class="calculate select-vehicle-type" name="service-type-1">
		<option value="internal">Internal only</option>
		<option value="external">External only</option>
		<option value="mini">Mini</option>
		<option value="midi">Midi</option>
		<option value="full">Full</option>
	</select>

	<div class="label">Interior material</div>
	<select name="interior-material-1">
		<option value="fabric">Fabric</option>
		<option value="leather">Leather</option>
	</select>
</div>

 
<div class='add-remove-vehicles remove-additional-vehicles calculate' hidden>
	-
</div>

<div class='add-remove-vehicles add-additional-vehicles calculate'>
	+
</div>

<div id="additional-vehicles-label">
	Add additional vehicle types
</div>


<div class="clearfix"></div>
<div class="service-option">
	<label class="label">Would you be able to provide us with:</label>
	<div class="input-wrapper-half">
		<div class="checkbox-wrapper">
			<div class="label"><input type="checkbox" id="electricity" name="electricity" class="toggle calculate" data-toggle="electric-outlet">Electricity</div>
		</div>
	</div>
	<div class="input-wrapper-half">
		<div class="checkbox-wrapper">
			<div class="label"><input type="checkbox" id="water" name="water" class="toggle calculate" data-toggle="water-tap">Water</div >
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
		<div  class="label">Where is your water tap located:</div>
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

<input type="hidden" class="minimum-booking" value="<?=CAR_VALETING_INTERNAL_MINIMUM_BOOKING?>" />