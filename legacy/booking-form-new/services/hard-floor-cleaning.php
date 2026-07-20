<div class="input-wrapper">
	  <div class="label">What is the type of your floor?</div>
	  <div class="radio-wrapper">
		<div class="radio"><input class='calculate' type="radio" value="wooden" name="floor-type" checked="checked">Wooden floor (with a rotary machine)</div>
		<div class="radio"><input class='calculate' type="radio" value="porous" name="floor-type">Porous floor (cork, concrete, terracotta, limestone)</div>
		<div class="radio"><input class='calculate' type="radio" value="semi-porous" name="floor-type">Semi-porous (safety flooring, conductive flooring)</div>
		<div class="radio"><input class='calculate' type="radio" value="non-porous" name="floor-type">Non-porous (ceramic tiles, granite, marble, quarry tiles, resin & polyester, laminate)</div>
	  </div>
</div>

<div class="input-wrapper">
	  <div class="label">Are there any edges on the floor?</div>
	  <div class="radio-wrapper">
		<div class="radio"><input type="radio" value="flat" name="floor-edges" checked="checked">Flat surface</div>
		<div class="radio"><input type="radio" value="build-up" name="floor-edges">Build-up</div>
		<div class="radio"><input type="radio" value="polish" name="floor-edges">Non-removable polish/seal</div>
		<div class="radio"><input type="radio" value="loose" name="floor-edges">Loose</div>
		<div class="radio"><input type="radio" value="sealed" name="floor-edges">Sealed</div>
		<div class="radio"><input type="radio" value="curled" name="floor-edges">Curled</div>
	  </div>
</div>

<div class="input-wrapper-half">
	<div class="label">Please enter your floor size in square meters:</div>
	<input class='calculate calculate-custom' id='floor-size' name='floor-size' data-calculate="hardFloorPrice" />
</div>

<div class="clearfix"></div>

<input type="hidden" class="minimum-booking" value="<?=HARD_FLOOR_CLEANING_MINIMUM_BOOKING?>" />