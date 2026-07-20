<div class="change-service-type-container">
	<div class="change-service-type active" data-service-type="oven-standard">Standard</div>
	<div class="change-service-type" data-service-type="oven-range">Range</div>
	<div class="change-service-type" data-service-type="oven-aga">AGA</div>
	<div class="change-service-type" data-service-type="oven-bbq">BBQs</div>
</div>

<div id="oven">
	<div id="oven-standard" class="service-options-container">
		<div class="service-option">
			<div class="service-option-title">Gas hob</div>
			<div class="service-option-price">&pound;<?=OVEN_GAS_HOBS_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-gas-hobs" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Gas hob (double)</div>
			<div class="service-option-price">&pound;<?=OVEN_GAS_HOBS_DOUBLE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-gas-hobs-double" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Ceramic hob</div>
			<div class="service-option-price">&pound;<?=OVEN_CERAMIC_HOBS_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-ceramic-hobs" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Microwave oven</div>
			<div class="service-option-price">&pound;<?=OVEN_MICROWAVE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-microwave" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Single oven, including two racks</div>
			<div class="service-option-price">&pound;<?=OVEN_SINGLE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-single" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Double oven, including three racks</div>
			<div class="service-option-price">&pound;<?=OVEN_DOUBLE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-double" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Extractor</div>
			<div class="service-option-price">&pound;<?=OVEN_EXTRACTOR_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-extractor" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Extractor (double)</div>
			<div class="service-option-price">&pound;<?=OVEN_EXTRACTOR_DOUBLE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-extractor-double" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<div id="oven-range" class="service-options-container" hidden>
		<!--
		<div class="service-option">
			<div class="service-option-title">Range oven</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_OVEN_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		-->
		
		<div class="service-option">
			<div class="service-option-title">Range hood or hob</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_HOOD_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-hood" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<!--
		<div class="service-option">
			<div class="service-option-title">Gas hob</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_GAS_HOBS_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-gas-hob" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Electric hobs</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_ELECTRIC_HOBS_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-electric-hob" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Ceramic hobs</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_CERAMIC_HOBS_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-ceramic-hob" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		-->
		<div class="service-option">
			<div class="service-option-title">Range extractor</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_EXTRACTOR_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-extractor" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Single wide oven</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_WIDE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-wide" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Range 1/2 size oven</div>
			<div class="service-option-price">&pound;<?=OVEN_RANGE_HALF_SIZE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-half-size" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Master range - 90cm (excluding burners)</div>
			<div class="service-option-price">&pound;<?=OVEN_MASTER_RANGE_90_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-90" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Master range - 100cm (excluding burners)</div>
			<div class="service-option-price">&pound;<?=OVEN_MASTER_RANGE_100_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-range-100" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Warming drawer door</div>
			<div class="service-option-price">&pound;<?=OVEN_WARMING_DRAWER_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-warming-drawer" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<div id="oven-aga" class="service-options-container" hidden>
		<div class="service-option">
			<div class="service-option-title">Two oven size</div>
			<div class="service-option-price">&pound;<?=OVEN_TWO_SIZE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-two-size" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Four oven size</div>
			<div class="service-option-price">&pound;<?=OVEN_FOUR_SIZE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-four-size" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Side module</div>
			<div class="service-option-price">&pound;<?=OVEN_SIDE_MODULE_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-side-module" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<div id="oven-bbq" class="service-options-container" hidden>
		<div class="service-option">
			<div class="service-option-title">Small barbecue</div>
			<div class="service-option-price">&pound;<?=OVEN_SMALL_BBQ_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-small-bbq" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<div class="service-option">
			<div class="service-option-title">Large barbecue</div>
			<div class="service-option-price">&pound;<?=OVEN_LARGE_BBQ_PRICE?></div>
			<div class="service-option-amount">
				<div class="change-amount decrease-amount">-</div>
				<input type="text" value="0" class="amount" name="oven-large-bbq" min="0" max="20">
				<div class="change-amount increase-amount">+</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>

<label class="label"><input class="toggle" type="checkbox" data-toggle="oven-combo-deals">Combo deals</label>
<div style="display: none" id="oven-combo-deals" class="service-options-container">

	<div class="service-option">
		<div class="service-option-title">Single Oven (2 racks) + Hob + Extractor</div>
		<div class="service-option-price">&pound;<?=SINGLE_OVEN_HOB_EXTRACTOR?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="oven-combo-deal-1" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="service-option">
		<div class="service-option-title">Double Oven (3 racks) + Hob + Extractor</div>
		<div class="service-option-price">&pound;<?=DOUBLE_OVEN_HOB_EXTRACTOR?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="oven-combo-deal-2" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="service-option">
		<div class="service-option-title">Master Range - 90cm + hob and extractor</div>
		<div class="service-option-price">&pound;<?=MASTER_RANGE_90_HOB_EXTRACTOR?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="oven-combo-deal-3" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="service-option">
		<div class="service-option-title">Master Range - 100cm + hob and extractor</div>
		<div class="service-option-price">&pound;<?=MASTER_RANGE_100_HOB_EXTRACTOR?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="0" class="amount" name="oven-combo-deal-4" min="0" max="20">
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div id="file-uploads">
	<div class="label">Photo attachments (optional)</div>
	<div class="input-wrapper">
	  <input class="attach-photo" type="file" name="file-attachment-oven" />
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
		<div class="label"><input type="checkbox" class="add-service" name="added-window-cleaning" data-add-service="<?=WINDOW_CLEANING_SERVICE?>" />Window</div>
	</div>
	<div class="clearfix"></div>
</div>
<?php
    if(DISCOUNT_CODE === true) {
        ?>
        <label class="label">Discount code</label><input class="calculate" id="discount-code" name="discount-code"/>
        <?php
    }
?>

<input type="hidden" class="minimum-booking" value="<?=OVEN_CLEANING_MINIMUM_BOOKING?>" />