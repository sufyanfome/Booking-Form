<div class="service-options-container">
	<div class="service-option">
		<div class="service-option-title">Bedrooms</div>
		<div class="service-option-price"></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount change-rooms">-</div>
			<input type="text" value="1" id="regular-cleaning-bedrooms" class="amount rooms not-service" data-type="hours" name="regular-cleaning-bedrooms" min="1" max="5">
			<div class="change-amount increase-amount change-rooms">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<div class="service-option">
		<div class="service-option-title">Bathrooms</div>
		<div class="service-option-price"></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount change-rooms">-</div>
			<input type="text" value="1" id="regular-cleaning-bathrooms" class="amount rooms not-service" data-type="hours" name="regular-cleaning-bathrooms" min="1" max="5">
			<div class="change-amount increase-amount change-rooms">+</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="service-options-container">
	<div class="service-option">
		<div class="input-wrapper">
			 <div class="label">Additional tasks (optional)</div>
			 <div id="additional-tasks-container">
				 <div class='additional-tasks calculate-hours' data-hours="1" data-checkbox='additional-fridge'>
					 <div class='additional-tasks-image'><img src="<?=MAIN_PATH?>images/fridge.png" /></div>
					 <div class='additional-tasks-label'>Inside fridge</div>
					 <input type='checkbox' id='additional-fridge' name='additional-fridge' hidden />
				 </div>
				 <div class='additional-tasks calculate-hours' data-hours="1" data-checkbox='additional-laundry'>
					 <div class='additional-tasks-image'><img src="<?=MAIN_PATH?>images/laundry.png" /></div>
					 <div class='additional-tasks-label'>Laundry</div>
					 <input type='checkbox' id='additional-laundry' name='additional-laundry' hidden />
				 </div>
				 <div class='additional-tasks calculate-hours' data-hours="1" data-checkbox='additional-oven'>
					 <div class='additional-tasks-image'><img src="<?=MAIN_PATH?>images/oven.png" /></div>
					 <div class='additional-tasks-label'>Inside oven</div>
					 <input type='checkbox' id='additional-oven' name='additional-oven' hidden />
				 </div>
				 <div class='additional-tasks calculate-hours' data-hours="1" data-checkbox='additional-windows'>
					 <div class='additional-tasks-image'><img src="<?=MAIN_PATH?>images/window.png" /></div>
					 <div class='additional-tasks-label'>Inside windows</div>
					 <input type='checkbox' id='additional-windows' name='additional-windows' hidden />
				 </div>
				 <div id="ironing" class='additional-tasks calculate-hours' data-hours="1" data-checkbox='additional-ironing'>
					 <div class='additional-tasks-image'><img src="<?=MAIN_PATH?>images/iron.png" /></div>
					 <div class='additional-tasks-label'>Ironing</div>
					 <input type='checkbox' id='additional-ironing' name='additional-ironing' hidden />
				 </div>
			 </div>
		</div>
	</div>
</div>

<div class="service-options-container">
	<div class="service-option">
		<div id="main-service" class="service-option-title">Cleaning</div>
		<div id="main-service-price" class="service-option-price">£<?=REGULAR_CLEANING_PRICE?></div>
		<div class="service-option-amount">
			<div class="change-amount decrease-amount">-</div>
			<input type="text" value="2" id="cleaning-hours" class="amount" data-calculate="regularPrice" data-type="hours" name="cleaning-hours" min="2" max="20"><div class="field-desc"> hr</div>
			<div class="change-amount increase-amount">+</div>
		</div>
		<div class="clearfix"></div>
		<div class="notice">Recommended hours - minimum call out charge 5h</span></div>
	</div>
</div>

<div class="service-options-container">
	<div class="service-option">
	
	</div>
</div>

<div class="input-wrapper">
	 <div class="label"><i class="fa fa-refresh" aria-hidden="true"></i> How often?</div>
	 <div class="how-often calculate" data-how-often="frequently">More than weekly</div>
	 <div class="how-often calculate selected" data-how-often="every-week">Once per week</div>
	 <div class="how-often calculate" data-how-often="every-2-weeks">Every two weeks</div>
</div>	 

<div class="input-wrapper-half" hidden>
	 <div class="label"><i class="fa fa-refresh" aria-hidden="true"></i> How often?</div>
	 <div class="radio-wrapper">
		<div class="radio">
			<input class="toggle calculate" type="radio" id="frequently" value="frequently" name="how-often" checked="checked" data-toggle="sessions">
			More than once per week
		</div>
		<div class="radio">
			<input class="calculate" type="radio" id="every-week" value="weekly" name="how-often" checked="checked">
			Once per week
		</div>
		<div class="radio">
			<input class="calculate" type="radio" id="every-2-weeks" value="fortnightly" name="how-often">
			Every two weeks
		</div>
	 </div>
</div>
<div class="clearfix"></div>

<div id="sessions" style="margin-top: 20px;" hidden>
	<div class="service-option-title">Sessions per week</div>
	<div class="service-option-amount">
		<div class="change-amount decrease-amount">-</div>
		<input type="text" value="2" id="sessions-per-week" class="amount not-service" data-type="hours" name="sessions-per-week" min="2" max="7">
		<div class="change-amount increase-amount">+</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="clearfix"></div>
<div class="checkbox-wrapper">
	<div class="label"><input type="checkbox" class="display-option" data-display="pets" name="have-pets">Do you have any pets?</div>
</div>

<div id="pets" class="input-wrapper" hidden >
	<textarea name="pets" placeholder="What pets do you have?"></textarea>
</div>
<div class="clearfix"></div>

<?php
    if(DISCOUNT_CODE === true) {
        ?>
        <label class="label">Discount code</label><input class="calculate" id="discount-code" name="discount-code"/>
        <?php
    }
?>

<input type="hidden" class="minimum-booking" value="<?=REGULAR_CLEANING_MINIMUM_BOOKING?>" />

<script>
var baseHours = 2;
var recommendedHours = baseHours;
var bedrooms = parseInt($('#regular-cleaning-bedrooms').val());
var bathrooms = parseInt($('#regular-cleaning-bathrooms').val());
var fortnightly = false;
var mainServicePrice = parseInt('<?=REGULAR_CLEANING_PRICE?>');

/* Отстъпка */

$('#cleaning-hours, .calculate').change(function() {
	if(parseInt($('#cleaning-hours').val()) >= 20 && $('.how-often.selected').html() !== 'Every two weeks') {
		$('#main-service-price').html('£' + (mainServicePrice - 2));
	} else if(parseInt($('#cleaning-hours').val()) >= 10 && $('.how-often.selected').html() !== 'Every two weeks') {
		$('#main-service-price').html('£' + (mainServicePrice - 1));
	} else if(parseInt($('#cleaning-hours').val()) >= 5 && $('.how-often.selected').html() !== 'Every two weeks') {
		$('#main-service-price').html('£' + (mainServicePrice - 1));
	} else {
		$('#main-service-price').html('£' + mainServicePrice);
	}
});

$('#recommended-hours').html(baseHours);

$('.additional-tasks').click(function() {
	var checkbox = $(this).attr('data-checkbox');
	if($(this).hasClass('selected')) {
		var hours = parseFloat($(this).attr('data-hours'));
		changeRecommendedHours(-hours);
		$(this).removeClass('selected');
		$('#'+checkbox).prop('checked', false);
	} else {
		var hours = parseFloat($(this).attr('data-hours'));
		changeRecommendedHours(hours);
		$(this).addClass('selected');
		$('#'+checkbox).prop('checked', true);
	}
});

function changeRecommendedHours(hours) {
	recommendedHours += hours;
	if(recommendedHours < baseHours) {
		recommendedHours = baseHours;
	}

	var cleaningHours = parseInt($('#cleaning-hours').val());
	cleaningHours = cleaningHours + hours;
	if(cleaningHours >= baseHours) {
		$('#cleaning-hours').val(cleaningHours);
		$('#cleaning-hours').attr('value', cleaningHours);
		calculatePrice();
	}
	
	$('#recommended-hours').html(recommendedHours);
}

$('#regular-cleaning-bedrooms').change(function() {

	var newValue = parseInt($(this).val());
	
	if(newValue > bedrooms) {
		changeRecommendedHours(1);
	} else if(newValue < bedrooms) {
		changeRecommendedHours(-1);
	}
	bedrooms = newValue;
});

$('#regular-cleaning-bathrooms').change(function() {
	var newValue = parseInt($(this).val());
	if(newValue > bathrooms) {
		changeRecommendedHours(1);
	} else if(newValue < bathrooms) {
		changeRecommendedHours(-1);
	}
	bathrooms = newValue;
});

$('.how-often').click(function() {
	$('.how-often').removeClass('selected');
	$(this).addClass('selected');
	var howOften = $(this).attr('data-how-often');
	$('#'+howOften).trigger('click');
	if(howOften === 'every-2-weeks') {
		fortnightly = true;
		$('#cleaning-hours').attr('min', 3);
		var recommended = parseInt($('#recommended-hours').html());
		var cleaningHours = parseInt($('#cleaning-hours').val());
		
		$('#cleaning-hours').val((cleaningHours + 1) + '');
		$('#cleaning-hours').attr('value', (cleaningHours + 1) + '');
		$('#recommended-hours').html((recommended + 1) + '');
		recommendedHours += 1;
	} else {
		if(fortnightly === true) {
			$('#cleaning-hours').attr('min', 2);
			var recommended = parseInt($('#recommended-hours').html());
			var cleaningHours = parseInt($('#cleaning-hours').val());
			
			$('#cleaning-hours').val((cleaningHours - 1) + '');
			$('#cleaning-hours').attr('value', (cleaningHours - 1) + '');
			$('#recommended-hours').html((recommended - 1) + '');
			recommendedHours -= 1;
		}
		
		fortnightly = false;
	}
});



$('[name=how-often]').click(function() {
	if(typeof $(this).attr('toggle') === 'undefined') {
		$('#sessions').hide();
	}
});

$('#ironing').click(function() {
	if($('#additional-ironing').is(':checked')) {
		$('#main-service').append(' + Ironing')
	} else {
		$('#main-service').html($('#main-service').html().replace(' + Ironing', ''));
	}
});


</script>

<style>
.how-often {
    display: inline-block;
    font-size: 12px;
    border: 1px solid #989898;
	color: #7d7d7d;
    padding: 8px;
    cursor: pointer;
    width: 30%;
    text-align: center;
	margin-right: 6px;
}

.how-often:hover {
	border: 2px solid #4197d4;
    color: #4197d4;
    font-weight: 600;
}

.how-often.selected {
	border: 2px solid #4197d4;
    color: #4197d4;
    font-weight: 600;
}
</style>

