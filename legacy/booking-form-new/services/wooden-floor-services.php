<select class="floor-service calculate select-toggle" name="wooden-floor-service">
	<option data-toggle='input-1'>Sanding only</option>
	<option data-toggle='input-2'>Sanding and 3 coats of clear lacquer</option>
	<option data-toggle='input-3'>Extra coat of lacquer, hardwax, oil or stain</option>
	<option data-toggle='input-4'>Upgrade to HP Commercial lacquer</option>
	<option data-toggle='input-5'>Sanding and 2 coats of hardwax oil</option>
	<option data-toggle='input-6'>Sanding and 3 coats of wood floor oil</option>
	<option data-toggle='input-7'>Staining/colouring</option>
	<option data-toggle='input-8'>Lime washing/liming/whitening</option>
	<option data-toggle='input-9'>Gap filling sawdust+resin (up to 3mm)</option>
	<option data-toggle='input-10'>Gap filling reclaimed wooden slivers (wider than 3mm)</option>
	<option data-toggle='input-11'>Gap filling flexible gap master (wider than 3mm, up to 9mm)</option>
	<option data-toggle='input-12'>Reclaimed pine floorboards changed</option>
	<option data-toggle='input-13'>Stairs: sand and seal</option>
	<option data-toggle='input-14'>Carpet removal</option>
	<option data-toggle='input-27'>Additional labour (restoration, repairs, furniture movement, etc.)</option>
</select>
<div class="clearfix"></div>
<div class="wooden-floor-input-container">
	<div id="input-1" class="input-wrapper">
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-1" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_1?>">
	</div>
	<div id="input-2" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-2" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_2?>">
	</div>
	<div id="input-3" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-3" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_3?>">
	</div>
	<div id="input-4" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-4" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_4?>">
		<div class='notice'> Please note that the pricing is per coat. Additional coats may be required.</div>
	</div>
	<div id="input-5" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-5" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_5?>">
	</div>
	<div id="input-6" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-6" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_6?>">
	</div>
	<div id="input-7" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-7" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_7?>">
		<div class='notice'>Please note that the pricing is per coat. Additional coats may be required.</div>
	</div>
	<div id="input-8" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-8" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_8?>">
		<div class='notice'> Please note that the pricing is per coat. Additional coats may be required.</div>
	</div>
	<div id="input-9" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-9" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_9?>">
	</div>
	<div id="input-10" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-10" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_10?>">
	</div>
	<div id="input-11" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-11" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_11?>">
	</div>
	<div id="input-12" class="input-wrapper" hidden>
		<div class="label">Linear meters:</div>
		<input name="floor-service-12" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_12?>">
	</div>
	<div id="input-13" class="input-wrapper" hidden>
		<div class="label">Number of steps:</div>
		<input name="floor-service-13" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_13?>">
	</div>
	<div id="input-14" class="input-wrapper" hidden>
		<div class="label">Number of rooms:</div>
		<input name="floor-service-14" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_14?>">
	</div>
	<div id="input-27" class="input-wrapper" hidden>
		<div class="label">Hours:</div>
		<input name="floor-service-27" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_27?>">
	</div>
</div>
<div class="clearfix"></div>

<!--FLOOR INSTALLATION SERVICES -->
<div class='service_container' id="solid-wood-installation-container">
	<div class='title'>Solid Wood Floor Installation</div>
	<select id="floor-installation" class="floor-service" name="floor-installation-service">
		<option data-toggle='input-15' class='floor-size'>Solid wood flooring installation</option>
		<option data-toggle='input-16' class='floor-size'>Parquet/strip floor installation</option>
		<option data-toggle='input-17' class='linear-meter'>Skirting fitting (does not include supplying the skirting)</option>
	</select>
	<div class="clear"></div>

	<div id="input-15" class="input-wrapper">
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-15" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_15?>">
	</div>
	<div id="input-16" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-16" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_16?>">
	</div>
	<div id="input-17" class="input-wrapper" hidden>
		<div class="label">Linear meters:</div>
		<input name="floor-service-17" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_17?>">
	</div>
</div>

<div class="clearfix"></div>
<!-- ENGINEERED WOOD FLOOR FITTING -->
<div class='service_container' id="engineered-floor-container">
	<div class='title'>Engineered Wood Floor Fitting</div>
	<select id="engineered-floor-fitting" class="floor-service" name="engineered-floor-fitting">
		<option data-toggle='input-18' class='floor-size'>Engineered wood floor installation</option>
		<option data-toggle='input-19' class='doorway'>Door trims</option>
		<option data-toggle='input-20' class='linear-meter'>Skirting fitting (does not include supply)</option>
		<option data-toggle='input-21' class='steps'>Staircase fitting per step</option>
	</select>
	<div class="clear"></div>

	<div id="input-18" class="input-wrapper">
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-18" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_18?>">
	</div>

	<div id="input-19" class="input-wrapper" hidden>
		<div class="label">Number of doorways:</div>
		<input name="floor-service-19" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_19?>">
	</div>
	<div id="input-20" class="input-wrapper" hidden>
		<div class="label">Linear meters:</div>
		<input name="floor-service-20" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_20?>">
	</div>
	<div id="input-21" class="input-wrapper" hidden>
		<div class="label">Number of steps:</div>
		<input name="floor-service-21" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_21?>">
	</div>
</div>

<div class="clearfix"></div>
<!-- LAMINATE FLOOR FITTING -->
<div class='service_container' id="laminate-floor-container">
	<div class='title'>Laminate Floor Fitting</div>
	<select id="laminate-floor-fitting" class="floor-service" name="laminate-floor-fitting">
		<option data-toggle='input-22'>Laminate floor installation</option>
		<option data-toggle='input-23'>Door trims</option>
		<option data-toggle='input-24'>Beading fitting (does not include supply)</option>
		<option data-toggle='input-25'>Old laminate removal</option>
		<option data-toggle='input-26'>Wooden kitchen countertop sanding/polishing/oiling</option>
	</select>
	<div class="clear"></div>

	<div id="input-22" class="input-wrapper">
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-22" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_22?>">
	</div>
	<div id="input-23" class="input-wrapper" hidden>
		<div class="label">Number of doorways:</div>
		<input name="floor-service-23" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_23?>">
	</div>
	<div id="input-24" class="input-wrapper" hidden>
		<div class="label">Linear meters:</div>
		<input name="floor-service-24" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_24?>">
	</div>
	<div id="input-25" class="input-wrapper" hidden>
		<div class="label">Floor size (sq m):</div>
		<input name="floor-service-25" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_25?>">
	</div>
	<div id="input-26" class="input-wrapper" hidden>
		<div class="label">Linear meters:</div>
		<input name="floor-service-26" class='calculate input-amount' type="text" data-price="<?=WOODEN_FLOOR_SERVICE_26?>">
	</div>
	<div class="clearfix"></div>
</div>


<input type="hidden" class="minimum-booking" value="<?=WOODEN_FLOOR_MINIMUM_BOOKING?>" />