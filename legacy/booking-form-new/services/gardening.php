<select class='select-toggle' id="select-gardening-service" name="gardening-service">
	<option data-toggle="basic-gardening-desc">Basic gardening</option>
	<option data-toggle="professional-gardening-desc">Professional garden maintenance</option>
	<option data-toggle="other-gardening-desc">Other services</option>
</select>

<div id="gardening-description-wrapper" class="note">
	<div class='gardening-desc notice' id="basic-gardening-desc">
		This service includes lawn mowing, grass cutting, weeding, hedge and bush trimming, spring and autumn tidy ups.
	</div>
	<div class='gardening-desc notice' id="professional-gardening-desc" hidden>
		This service includes patio, path and deck gardening, tree trimming, cutting of large bushes or big garden clearances.
	</div>
	<div class='gardening-desc notice' id="other-gardening-desc" hidden>
		For tasks such as laying new grass, turfing, stonework, building a pond, fireplace, fountain, etc.
	</div>
</div>

<div class="service-option">
	<div class="service-option-title">Gardening</div>
	<div id="gardening-price" class="service-option-price">£<?=GARDENING_PRICE?></div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" value="2" id="gardening-hours" class="amount calculate-custom" data-type="hours" name="gardening-hours" min="2" max="20" data-calculate="gardeningPrice"><div class="field-desc"> hr</div>
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
</div>
	
<div class='title'>Rubbish Clearance</div>
<div class="service-option">
	<div class="service-option-title">Small bin bag</div>
	<div class="service-option-price">&pound;<?=RUBBISH_CLEARANCE_SMALL_BAG_PRICE?></div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" value="0" class="amount" name="small-bin-bag" min="0" max="20">
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="service-option">
	<div class="service-option-title">Jumbo bin bag</div>
	<div class="service-option-price">&pound;<?=RUBBISH_CLEARANCE_JUMBO_BAG_PRICE?></div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" value="0" class="amount" name="jumbo-bin-bag" min="0" max="20">
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="service-option">
	<label class="label">Would you be able to provide us with:</label>
	<div class="input-wrapper-half">
		<div class="checkbox-wrapper">
			<div  class="label"><input type="checkbox" name="electricity" class="toggle" data-toggle="electric-outlet">Electricity</div>
		</div>
	</div>
	<div class="input-wrapper-half">
		<div class="checkbox-wrapper">
			<div class="label"><input type="checkbox" name="water" class="toggle" data-toggle="water-tap">Water</div >
		</div>
	</div>
	<div style="clear: both"></div>

	<div id="electric-outlet" class="input-wrapper" hidden>
		<div  class="label">Where is the electrical outlet located:</div>
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

<div class="notice small-notice info" bubble="The gardeners will take away and dispose for free of 3 bags of green garden waste only. Each bag is 60 Lt, in total they will take 180 Lt for free (please note if they are using 120 Lt bags, they will take 2 bags). In case you have additional bags of Rubbish, bags of 60 Lt will be charged 4.00 GBP and 120 Lt  5.50 GBP. However, if there is a large amount of green waste, please bear in mind that they can use Jumbo bags (up to 1000kg), the price for them starts from 60.00GBP, depending on the size. 
*in case you do not wish the team to dispose the waste after the service, it will not be filled in bags but left in a pile at one corner of the garden"><i class="fa fa-info-circle" aria-hidden="true" ></i> Additional information</div>


<input type="hidden" id="gardening-minimum-booking" class="minimum-booking" value="<?=GARDENING_MINIMUM_BOOKING?>" />