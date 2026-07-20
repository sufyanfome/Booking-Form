$('#step-1').show();
var formValid = false;
var chosenService = false;
var serviceType = 'domestic';
var chosenDate = false;
var chosenTimeslot = false;
var price = 0;
var giftCardAmount = 0;
var giftCardDiscount = 0;
var giftCardCode = '';

jQuery(document).on('keypress', '.amount', function(e) {
	if(!jQuery(this).hasClass('amount-custom')) {
		e.preventDefault();
	}
});

function clearErrors() {
	$('#error-message').html('');
	$('.error-message').each(function() {
		$(this).html('');
	});
}

$('.previous').click(function() {
	clearErrors();
});

$('.choose-service').change(function() {
	$('#service').val($(this).val());
});

$('#choose-service-type').change(function() {
	serviceType = $(this).val();
	$('#service-type').val(serviceType);
	
	$('.choose-service').each(function() {
		$(this).val('choose-service');
	});
	if($(this).find('option:checked').val() === 'domestic') {
		$('.choose-service').hide();
		$('#domestic-services').show();
		$('.go-to-booking').first().html('Next');
		$('#commercial-message').hide();
		$('#get-free-quote').hide();
		$('#timeslots-disabled').show();
		$('#preform-details-title').html('Booking Details');
		$('.go-to-booking').removeClass('submit');
	} else {
		$('.go-to-booking').first().html('Submit');
		$('.choose-service').hide();
		$('#commercial-services').show();
		$('#commercial-message').show();
		$('#get-free-quote').show();
		$('#timeslots-disabled').hide();
		$('#timeslots').html('');
		$('#preform-details-title').html('Get a Free Quote');
		$('.go-to-booking').addClass('submit');
	}
});

/* Validation */
function validate() {
	if(testmode === true) {
		return true;
	}

	clearErrors();
	var valid = true;
	
	$('fieldset').each(function() {	
		if($(this).css('display') === 'block') {
			$(this).find('.required').each(function() {
				if($(this).is(':visible')) {
					/* Checkboxes */
					if($(this).attr('type') === 'checkbox') {
						if(!this.checked) {
							var errorMessage = $(this).attr('data-error-message');
							if(typeof errorMessage !== 'undefined') {
								var errorID = $(this).attr('data-error-id');
								if(typeof errorID !== 'undefined') {
									$('#'+errorID).html(errorMessage);
								} else {
									$('#error-message').append('<div>'+errorMessage+'</div>');
								}
							}
							
							valid = false;
						}
					} else if ($(this).attr('type') === 'text') {
						/* Text fields */
						if($(this).val().length < 1) {
							$(this).next('.error-message').first().html('Field is required');
							valid = false;
						} else if($(this).hasClass('validatePostcode')) {
							var validateCode = $(this).val();
							if(!isPostcodeCovered(validateCode)) {
								$(this).next('.error-message').first().html('Postcode is not covered');
								valid = false;
							}
						} else if($(this).hasClass('validateEmail')) {
							var validateEmail = $(this).val();
							if(!isEmailValid(validateEmail)) {
								$(this).next('.error-message').first().html('Invalid email');
								valid = false;
							}
						}
					}
				}
			});
		}
	});

	$('.error-message').each(function() {
		if($(this).html().length > 0) {
			var thisElement = $(this);
			$([document.documentElement, document.body]).animate({
				scrollTop: thisElement.offset().top - 150
			}, 400);

			return valid;
		}
	})
      
	return valid;
}

/* Book */
$('.go-to-booking').click(function() {
	if($(this).hasClass('submit')) {
		return;
	}
	
	if($(this).hasClass('next')) {
		if(!validate()) {
			return false;
		}
	}
	
	if($(this).hasClass('previous')) {
		changeStep(1);	
	} else {
		if((testmode === true && chosenService !== false) || (chosenService !== false && chosenDate !== false && chosenTimeslot !== false)) {
			addService(chosenService, '#service-container');
			$('#service-title').html(chosenService);
		} else {
			displayErrorMessage('Please select a service and a date');
		}
	}
});

/* Submit (Finalize) */
$('.go-to-submit').click(function() {
	if($(this).hasClass('next')) {
		if(!validate()) {
			return false;
		}
	}
	
	changeStep(3);
	
	if(chosenDate !== false) {
		var serviceDate = new Date(chosenDate);
		$('#booking-date').html(serviceDate.toLocaleDateString("en-GB") + ' ' + chosenTimeslot);
	}
	$('#booking-address').html($('#address').val());
	$('#booking-services').html('');
	
	$('#booking-services').append('<div class="service">' + chosenService + '</div>');
	var addedServices = new Array();
	
	
	$('.amount:not(.not-service)').each(function() {
		
		if(jQuery(this).parents('.added-service').length > 0) {
			var addedServiceID = jQuery(this).parents('.added-service').first().attr('id');
			if(!addedServices.includes(addedServiceID)) {
				addedServices.push(addedServiceID);			
				addedService = addedServiceID.replace('added-', '').replace('-', ' ');
				addedService = titleCase(addedService);
				
				$('#booking-services').append('<div class="service">' + addedService + '</div>');
			}
		}
		
		var amount = $(this).val();
		
		if(parseInt(amount) > 0) {
			var serviceName = $(this).parents('.service-option').first().find('.service-option-title').first().html();
			var amountType = $(this).attr('data-type');
			serviceName += ' - ' + amount;
			if(typeof amountType !== 'undefined') {
				serviceName += ' ' + amountType;
			}
			$('#booking-services').append('<div class="booking-service">' + serviceName + '</div>');
		}

        $('.terms-and-conditions').hide();
		if(chosenService === 'One Off Cleaning') {
			$('#one-off-terms').show();
		}
	});
	
	$('#booking-price').html('£' + (price.toFixed(2)));
});

$(document).on('click', '.submit', function() {
	if($(this).hasClass('next')) {
		if(!validate()) {
			return false;
		}
	}

	if($('#choose-service-type').val() === 'domestic') {
		$('#booking-form').submit();
		return false;
	}
});


$(document).on('change', '#choose-service-type, #domestic-services', function() {
   chosenTimeslot = false;
});


function addService(service, container, title = '') {
	$.ajax({
		url: path + 'main.php?service=' + service
	}).done(function(result) {
		if(result.length < 1) {
			displayErrorMessage('An error has occurred');
		} else {
			changeStep(1);	
			$(container).html(result);

			$(container).find('.add-service').each(function() {
				let addServiceOption = $(this).attr('data-add-service');
				if(!availableDomesticCleaningServices.includes(addServiceOption)) {
					$(this).parents('.input-wrapper-half').first().remove();
				}
			});

			var counter = 0;

			$('.additional-services').each(function() {
				if(counter > 0) {
					$(this).remove();
				}
				counter++;
			});

			if(title.length > 0) {
				$(container).prepend('<div class="title">'+title+'</div>');
			}
			calculatePrice();
			
			$('.service-option-members-price').each(function() {
				$(this).attr('title', 'Join the FastKlean Club and get a discounted price');
				$(this).qtip({
					adjust: {
						  x: 200,
						  y: 200
				   }
				});
			});
		}
	});
}

function addMembersPrice() {
	$('.service-option-price').each(function() {
		var servicePrice = parseFloat($(this).html().replace('£', ''));
		if(!isNaN(servicePrice)) {
			var discountedPrice = Math.floor(servicePrice * 0.9);
			if(!$(this).next().hasClass('service-option-members-price')) {
				$(this).after('<div class="service-option-members-price">£'+discountedPrice+'</div>');
			} else {
				
				$(this).next().html('£'+discountedPrice);
			}
		}
	});
}

/* Details */
$('.go-to-details').click(function() {
	if($(this).hasClass('next')) {
		if(!validate()) {
			return false;
		}
	} 
	
	if($(this).hasClass('previous')) {
		changeStep(2);
	} else {
		$.ajax({
			url: path + 'main.php?booking-details=true'
		}).done(function(result) {
			if(result.length < 1) {
				displayErrorMessage('An error has occurred');
			} else {
				changeStep(2);
				$('#booking-details-container').html(result);
				$('#name').val($('#preform-name').val());
				$('#email').val($('#preform-email').val());
				$('#phone').val($('#preform-phone').val());
				$('#postcode').val($('#preform-postcode').val());
			}
		});
	}
});

/* Schedule */
$('.go-to-schedule').click(function() {
	changeStep(0);
});

var currentStep = 0;

function changeStep(step) {
	currentStep = step;
	$('fieldset').hide();
	$('#step-' + (step + 1)).show();
	$('.progressbar-active').removeClass('progressbar-active');
	$('#progressbar li').eq(step).addClass('progressbar-active');
}

function resetTimeslot() {
	chosenTimeslot = false;
	$('.selected-timeslot').removeClass('selected-timeslot');
}

$('.choose-service').change(function() {
	var service = $(this).find('option:selected').val();
	if(service !== 'choose-service') {
		if(service === 'Regular Cleaning' || service === 'Regular Cleaning Luxury') {
			setDatepicker(3);
		} else {
			setDatepicker(2);
		}
		chosenService = service;
		showAvailableTimeslots();
	} else {
		chosenService = false;
	}
});

setDatepicker();

function setDatepicker(daysOffset = 2) {
	jQuery("#service-date").datepicker("destroy");
	
	$("#service-date").datepicker({ 
		onSelect: function(date) {
			resetTimeslot();
			if(date.length > 0) {
				chosenDate = date;
				showAvailableTimeslots();
			} else {
				chosenDate = false;
			}
		},
		minDate: +daysOffset, 
		maxDate: "+2M",
		setDate: new Date(),
		firstDay: 1
	});
	
	jQuery('#service-date .ui-state-active').trigger('click');
}


$('.ui-state-active').removeClass('ui-state-active');

$(document).on('click', '.timeslot', function() {
	resetTimeslot();
	$(this).addClass('selected-timeslot');
	chosenTimeslot = $(this).html();
});

var daysOfWeek = {0:'sunday', 1:'monday', 2:'tuesday', 3:'wednesday', 4:'thursday', 5:'friday', 6:'saturday'};

function showAvailableTimeslots() {
	$('#timeslots').html('');
	if(serviceType === 'domestic') {
		jQuery('#timeslots-container').show();
		$('#timeslots').html('');
		if(chosenService !== false && chosenDate !== false) {
			$('#timeslots-disabled').hide();
			var date = new Date(chosenDate);
			
			for(timeslot in servicesHours[chosenService][daysOfWeek[date.getDay()]]) {
				$("#timeslots").append('<div class="timeslot">' +  servicesHours[chosenService][daysOfWeek[date.getDay()]][timeslot] + '</div>');
			}
		}
	} 
}

function displayErrorMessage(message) {
	$('#error-message').html(message);
}

$(document).on('click', '.increase-amount', function() {
	var amountField = $(this).parent('.service-option-amount').find('.amount').first();
	var amount = parseInt(amountField.val());
	var amountMin = parseInt(amountField.attr('min'));
	var amountMax = parseInt(amountField.attr('max'));
	
	if(amount < (amountMin - 1)) {
		amount = amountMin - 1;
	}
	if(amount < amountMax) {
		amountField.val(amount + 1);
		amountField.trigger('change');
	}
});

$(document).on('click', '.decrease-amount', function() {
	var amountField = $(this).parent('.service-option-amount').find('.amount').first();
	var amount = parseInt(amountField.val());
	var amountMin = parseInt(amountField.attr('min'));
	
	if(amount > amountMin) {
		amountField.val(amount - 1);
		amountField.trigger('change');
	}
});

$(document).on('click', '.display-option', function () {
	var display = $(this).attr('data-display');
	$('#'+display).toggle(this.checked);
});


$(document).on('click', '.change-service-type', function() {
	var currentActive = $(this).parent('.change-service-type-container').find('.active').first().removeClass('active').attr('data-service-type');
	$('#'+currentActive).hide();
	var newActive = $(this).addClass('active').attr('data-service-type');
	$('#'+newActive).show();
});


function calculatePrice() {
	price = 0;
	
	$('.amount:not(.calculate-custom)').each(function() {
		var servicePriceField = $(this).parents('.service-option').first().find('.service-option-price');
		if(servicePriceField.length > 0) {
			var servicePrice = parseFloat(servicePriceField.html().replace('£', '')) * parseInt($(this).val());
			if($('#frequently').is(':checked')) {
				servicePrice = servicePrice * parseInt($('#sessions-per-week').val());
			}
		} else {
			var servicePrice = 0;
		}
		
		if(isNaN(servicePrice)) {
			servicePrice = 0;
		}
		price += servicePrice;
	});
	
	$('.input-amount').each(function() {
		var servicePrice = parseFloat($(this).attr('data-price'));
		var inputAmount = parseInt($(this).val());
		
		if(!isNaN(servicePrice) && !isNaN(inputAmount)) {
			price += servicePrice * inputAmount;
		}
	});

	var calcFunctions = new Array();
	
	$('.calculate-custom').each(function() {
		var calcFunction = $(this).attr('data-calculate');
		if(!calcFunctions.includes(calcFunction)) {
			calcFunctions.push(calcFunction);
			price += eval(calcFunction + '()');
		}
	});

	var minimumBooking = 0;

	$('.minimum-booking').each(function() {
		minimumBooking += parseFloat(jQuery(this).val());
	});

	if(typeof minimumBooking !== 'undefined' && price < minimumBooking) {
		price = minimumBooking;
	}

	var discountedPrice = calculateDiscount(price);
    if(typeof minimumBooking !== 'undefined' && discountedPrice < minimumBooking) {
		discountedPrice = minimumBooking;
	}       

	if(vat === 'true') {
		var vatPrice = price * 0.2;
		var priceBeforeVat = price;
		price = price + vatPrice;
		discountedPrice = discountedPrice * 1.2;
	}
	
	if($('#congestion-charge').is(':checked')) {
		price += 27.5;
		discountedPrice += 27.5;
	}
	
	if($('input[name="walking-distance"]').is(':checked') && $('input[name="walking-distance"]').is(':visible')) {
		price += parseInt($('input[name="walking-distance"]:checked').val());
		discountedPrice += parseInt($('input[name="walking-distance"]:checked').val());
	}
	
	// Stripe doesn't accept prices below 0.30
	if(discountedPrice.toFixed(2) < 0.3) {
		discountedPrice = 0;
	}

	if(price.toFixed(2) < 0.3) {
		price = 0;
	}

	$('.price').html('<span class="original-price">' + '£' + price.toFixed(2) + '</div>');
	
    discountedPrice = addGiftCard(discountedPrice);

	if(discountedPrice.toFixed(2) < price.toFixed(2)) {
		$('.original-price').addClass('price-discounted');
		$('.original-price').after('<span class="discounted-price">£' + discountedPrice.toFixed(2)+'</span>');
		price = discountedPrice;
	}

	if(vat === 'true') {
		$('.vat-price').remove();
		$('.price').prepend('<div class="vat-price"></div>');
		$('.vat-price').html('£' + priceBeforeVat.toFixed(2) + ' + £' + vatPrice.toFixed(2) + ' VAT');
	}
	
	if(vat === 'true') {
		$('#minimum-booking-notice').html('The minumum booking fee for this service is £' + minimumBooking + ' + VAT');
	} else {
		$('#minimum-booking-notice').html('The minumum booking fee for this service is £' + minimumBooking);
	}
	
	if(members === 'true') {
		addMembersPrice();
	}
}

function addGiftCard(pr) {
	if(giftCardAmount > 0) {
		if(giftCardAmount >= pr) {
			giftCardDiscount = pr;
		} else {
			giftCardDiscount = giftCardAmount;
		}

		pr = pr - giftCardDiscount;

        $('#gift-card-message').append('<p>Total amount in card: <strong>&pound;' + giftCardAmount.toFixed(2) + '</strong></p>');
        $('#gift-card-message').append('<p>Amount left after purchase: <strong>&pound;' + (giftCardAmount - giftCardDiscount).toFixed(2) + '</strong></p>');
	} 
    
    return pr;
}

function calculateDiscount(price) {
	var discountCode = $('#discount-code').val();
	var priceDiscounted = false;

	if(isDiscountCodeValid()) {
		if(discountCode.toLowerCase() === 'carpet25') {
			var standardSizeRooms = 0;
			var hallways = 0;

			$('.standard-sized-room').each(function() {
				standardSizeRooms += parseInt($(this).val());
			});
			
			$('.hallway').each(function() {
				hallways += parseInt($(this).val());
			});

			if(standardSizeRooms >= 3 && hallways >= 1) {
				price = price * 0.75;
				priceDiscounted = true;
			}
		} else if (discountCode.toLowerCase() === 'oven10') {
			price = price * 0.90;
			priceDiscounted = true;
		} else if (discountCode.toLowerCase() === 'anti10') {
			price = price * 0.90;
			priceDiscounted = true;
		} else if (discountCode.toLowerCase() === 'reg10') {
			price = price * 0.90;
			priceDiscounted = true;
		} else if (discountCode.toLowerCase() === 'grcleaning') {
			price = price * 0.865;
			priceDiscounted = true;
		}
	}

	return price;
}


function carValetingPrice() {
	servicePrice = 0;
	var cityCarPrices = JSON.parse(carValetingCityCarPrices);
	var spaceCruisePrices = JSON.parse(carValetingSpaceCruisePrices);
	var fourByFourPrices = JSON.parse(carValetingFourByFourPrices);
	var vanPrices = JSON.parse(carValetingVanPrices);
	
	var vehicleTypes = $('.vehicle-type').length;
	
	var i = 1;
	for(i; i <= vehicleTypes; i++) {
		var priceForType = 0;
		
		var vehicleAmount = parseInt($('[name=car-valeting-vehicle-amount-'+i+']').val());
		var vehicleType = $('[name=vehicle-type-'+i+']').val();
		var valetingServiceType = $('[name=service-type-'+i+']').val();
		
		switch(vehicleType) {
			case 'city':
				priceForType = cityCarPrices[valetingServiceType];
				break;
			case 'space-cruiser':
				priceForType = spaceCruisePrices[valetingServiceType];
				break;
			case '4x4':
				priceForType = fourByFourPrices[valetingServiceType];
				break;		
			case 'van':
				priceForType = vanPrices[valetingServiceType];
				break;	
			default: 
				priceForType = cityCarPrices[valetingServiceType];
		}
		
		servicePrice += parseInt(priceForType) * vehicleAmount;
	}
	
	var haveElectricity = $('#electricity').is(':checked');
	var haveWater = $('#water').is(':checked');
	
	if(!haveElectricity || !haveWater) {
		servicePrice+= 20;
	}
	
	return servicePrice;
}

function gardeningPrice() {
	servicePrice = 0;	
	var gardeningHours = parseInt($('#gardening-hours').val());
	var gardeningPrice = parseFloat($('#gardening-price').html().replace('£', ''));
	var gardeningMinimumBooking = parseFloat(jQuery('#gardening-minimum-booking').val());

	if(gardeningHours <= 2) {
		servicePrice = parseInt(gardeningMinimumBooking);
	} else {
		servicePrice = (gardeningHours - 2) * gardeningPrice + gardeningMinimumBooking;
	}

	return servicePrice;
}

function rubbishRemovalPrice() {
	servicePrice = 0;
	var cubicYardsInput = parseInt($('#rubbish-cubic-yards').val());
	var weightInput = parseInt($('#rubbish-weight').val());
	var cubicYardPrices = JSON.parse(rubbishCubicYardsPrices);
	var weightPrices = JSON.parse(rubbishWeightPrices);
	
	for(yards in cubicYardPrices) {
		if(cubicYardsInput >= yards && cubicYardPrices[yards] > servicePrice){
			servicePrice = cubicYardPrices[yards];
		}
	}
	
	for(weight in weightPrices) {
		if(weightInput >= weight && weightPrices[weight] > servicePrice){
			servicePrice = weightPrices[weight];
		}
	}
	
	return servicePrice;
}

function hardFloorPrice() {
	servicePrice = 0;
	
	var floorType = $('[name=floor-type]:checked').val();
	var sqMeters = parseInt($('#floor-size').val());
	
	if(isNaN(sqMeters)) {
		sqMeters = 0;
	}
	
	if(floorType === 'wooden') {
		var pricePerSqMeter = hardFloorWoodPrice;
	} else {
		var pricePerSqMeter = hardFloorRegularPrice;
	}
	
	servicePrice = sqMeters * pricePerSqMeter;
	
	return servicePrice;
}


function endOfTenancyPrice() {
	servicePrice = 0;
	var propertyType = $('#property-type option:selected').html();
	var bedrooms = parseInt($('#end-of-tenancy-bedrooms').val());
	var bathrooms = parseInt($('#end-of-tenancy-bathrooms').val());
	var additionalRooms = parseInt($('#end-of-tenancy-additional').val());
	
	if(propertyType === 'Flat') {
	if((bathrooms === 1 && bedrooms === 0) || (bathrooms === 0 && bedrooms === 1)) {
        servicePrice = 190;
    } else if(bathrooms === 1 && bedrooms === 1) {
        servicePrice = 225;
    } else if (bathrooms === 1 && bedrooms === 2) {
        servicePrice = 250;
    } else if (bathrooms === 2 && bedrooms === 2) {
        servicePrice = 260;
    } else if (bathrooms === 1 && bedrooms === 3) {
        servicePrice = 310;
    } else if (bathrooms === 2 && bedrooms === 3) {
        servicePrice = 350;
    } else if (bathrooms === 3 && bedrooms === 3) {
        servicePrice = 380;
    } else if (bathrooms === 2 && bedrooms === 4) {
        servicePrice = 400;
    } else if (bathrooms === 3 && bedrooms === 4) {
        servicePrice = 440;
    } else if (bathrooms === 2 && bedrooms === 5) {
        servicePrice = 460;
    } else if (bathrooms === 3 && bedrooms === 5) {
        servicePrice = 490;
    } else if (bathrooms === 4 && bedrooms === 5 ) {
        servicePrice = 510;
    } else {
        servicePrice = 330 + (((bathrooms + bedrooms) - 5) * 40);
    }
} else if(propertyType === 'House') {
    if((bathrooms === 1 && bedrooms === 1) || (bathrooms === 1 && bedrooms === 0) || (bathrooms === 0 && bedrooms === 1)) {
        servicePrice = 240;
    } else if (bathrooms === 1 && bedrooms === 2) {
        servicePrice = 280;
    } else if (bathrooms === 2 && bedrooms === 2) {
        servicePrice = 310;
    } else if (bathrooms === 1 && bedrooms === 3) {
        servicePrice = 340;
    } else if (bathrooms === 2 && bedrooms === 3) {
        servicePrice = 370;
    } else if (bathrooms === 3 && bedrooms === 3) {
        servicePrice = 400;
    } else if (bathrooms === 2 && bedrooms === 4) {
        servicePrice = 440;
    } else if (bathrooms === 3 && bedrooms === 4 ) {
        servicePrice = 470;
    } else if (bathrooms === 2 && bedrooms === 5 ) {
        servicePrice = 480;
    } else if (bathrooms === 3 && bedrooms === 5 ) {
        servicePrice = 500;
    } else if (bathrooms === 4 && bedrooms === 5 ) {
        servicePrice = 520;
    } else {
        servicePrice = 350 + (((bathrooms + bedrooms) - 5) * 50);
    }
}
	
	servicePrice += additionalRooms * 50;
	
	if($('#keys').is(':checked')) {
		var distance = $('[name=keys-distance]:checked').first().val()
		if(distance === 'near') {
			servicePrice += parseInt(endOfTenancyKeysNearPrice);
		} else {
			servicePrice += parseInt(endOfTenancyKeysFarPrice); 
		}
	}
	return servicePrice;
}

/* Car valeting */
$(document).on('click', '.add-additional-vehicles', function() {
	var numberOfVehicleForms = $('.car-valeting-container').length;
	$('.remove-additional-vehicles').show();
	var newForm = $('.car-valeting-container').first().clone();
	newForm.find('[name=car-valeting-vehicle-amount-1]').attr('name', 'car-valeting-vehicle-amount-'+(numberOfVehicleForms+1)).val(1);
	newForm.find('[name=vehicle-type-1]').attr('name', 'vehicle-type-'+(numberOfVehicleForms+1));
	newForm.find('[name=service-type-1]').attr('name', 'service-type-'+(numberOfVehicleForms+1));
	newForm.find('[name=interior-material-1]').attr('name', 'interior-material-'+(numberOfVehicleForms+1));
	$('.car-valeting-container').last().after(newForm);
	$([document.documentElement, document.body]).animate({
        scrollTop: $('.car-valeting-container').last().offset().top
    }, 400);
	
	calculatePrice();
});

$(document).on('click', '.remove-additional-vehicles', function() {
	var numberOfVehicleForms = $('.car-valeting-container').length;
	
	if(numberOfVehicleForms > 1) {
		$('.car-valeting-container').last().remove();
	}
	
	if(numberOfVehicleForms === 2) {
		$(this).hide();
	}
	
	calculatePrice();
});


$(document).on('click', '.toggle', function() {
	var toggle = $(this).attr('data-toggle');
	$('#'+toggle).toggle(this.checked);
});

$(document).on('change', '.select-toggle', function() {
	var toggle = $(this).find('option:selected').attr('data-toggle');
	$('#'+toggle).parent().find('div').each(function() {
		if(!$(this).hasClass('label') && !$(this).hasClass('notice')) {
			$(this).hide();
		}
	});
	$('#'+toggle).parent().find('div').hide();
	$('#'+toggle).show();
});

$(document).on('click', '.added-cost', function() {
	var forServices = $(this).attr('data-service').split(',');
	var addedCostData = $(this).attr('data-added-cost');
	var checked = $(this).is(':checked');

	for(serviceID in forServices) {
		$('#'+forServices[serviceID].trim()  + ' .service-option-price').each(function() {
			var optionPrice = parseFloat($(this).html().replace('£', ''));
			if(checked) {
				if(addedCostData.includes('%')) {
					addedCost = parseFloat(addedCostData.replace('%', ''));
					optionPrice = optionPrice + (optionPrice * (addedCost/100));
				} else {
					addedCost = parseFloat(addedCostData);
					optionPrice = optionPrice + addedCost;
				}
			} else {
				if(addedCostData.includes('%')) {
					addedCost = parseFloat(addedCostData.replace('%', ''));
					optionPrice = optionPrice / (1 + (addedCost/100));
				} else {
					addedCost = parseFloat(addedCostData);
					optionPrice = optionPrice - addedCost
				}
			}
			
			$(this).html(Math.round(optionPrice));
			$(this).prepend('£');
		});
	}
});

$(document).on('change click', '.calculate, .added-cost, .change-amount, .amount-custom', calculatePrice);

$(document).on('click', '.add-service', function() {
	var service = $(this).attr('data-add-service');
	var serviceID = 'added-'+ service.toLowerCase().replace(' ', '-');
	var serviceContainer = '<div id="' + serviceID + '" class="added-service"></div>';
	if($(this).is(':checked')) {
		$('#service-container').append(serviceContainer);
		addService(service, '#'+serviceID, service);
		 $([document.documentElement, document.body]).animate({
			scrollTop: $('#'+serviceID).offset().top
		}, 500);
	} else {
		$('#'+serviceID).remove();
		calculatePrice();
	}
});


$(window).on("beforeunload", function() { 
	if(currentStep === 1 || currentStep === 2) {
		sendUnfinishedBookingEmail();
	}
});

function sendUnfinishedBookingEmail() {
	var name = $('#preform-name').val();
	var email = $('#preform-email').val();
	$.ajax({
		url: path + 'unfinished-booking.php?service='+chosenService+'&name='+name+'&email='+email,
		async: true
	});
}

$('.action-button').click(function() {
	$('.message').remove();
});

$(document).on('click', '.next, .previous', function() {
	$([document.documentElement, document.body]).animate({
		scrollTop: $('#booking-form').offset().top
	}, 400);
});

$(document).on('click', '.submit', function(e) {
	if($('#choose-service-type').val() === 'commercial') {
		e.preventDefault();
		e.stopPropagation();
		var data = $('#booking-form').serialize();
		$.ajax({
			url: path + 'main.php?free-quote',
			data: data
		}).done(function(result) {
			$('#preform-details-container input').each(function() {
				$(this).val('');
			});
			
			$('#preform-details-container textarea').val('');
			$('#preform-details-container textarea').after('<div class="message">The form has been submitted!</div>');
			$('#get-free-quote').html('');
		});
	}
});

function isDiscountCodeValid() {
    $('#discount-code').removeClass('input-error');
	var discountCode = $('#discount-code').val();
	var codeValid = false;
    if(typeof discountCode !== 'undefined') {
        for(dc in discountCodes) {
            if(discountCodes[dc].toLowerCase() === discountCode.toLowerCase()) {
                codeValid = true;
            }
        }
    }

    if(codeValid === false) {
        if(typeof discountCode !== 'undefined' && discountCode.length > 0) {
            $('#discount-code').addClass('input-error');
        }
    }
	return codeValid;
}

$(document).on('change', '#gift-card-code', function() {
	giftCardCode = $(this).val();
	if(chosenService !== 'One Off Cleaning') {
        giftCardAmount = 0;
        $('#gift-card-code').addClass('input-error');
        $('#gift-card-message').html('Incorrect code');
    } else {
        $('#gift-card-code').removeClass('input-error');
        $('#gift-card-amount').val('');
        $('#gift-card-message').html('');

        var giftCardCode = jQuery(this).val();
        
        $.ajax({
            url: path + 'main.php?get-gift-card-amount=' + giftCardCode
        }).done(function(result) {
            if(!isNaN(parseFloat(result))) {
                giftCardAmount = parseFloat(result);
                $('#gift-card-code').val(giftCardCode);
                $('#gift-card-amount').val(result);
            } else {
                giftCardAmount = 0;
                $('#gift-card-code').addClass('input-error');
                $('#gift-card-message').html('Incorrect code');
            }
            calculatePrice();
        });
    }
});


$(document).on('mouseenter', '[bubble]', function(e) {
    
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        var position = $(this).position();
        var message = $(this).attr('bubble');
        var positionLeftOffset = 60;
        var positionTopOffset = 60;
        if($(this).hasClass('sidebar-price')) {
            positionLeftOffset = 20;
        } else if ($(this).is(':checkbox')) {
            positionLeftOffset = 10;
            positionTopOffset = 105;
        } else if ($(this).is(':radio')) {
            positionLeftOffset = 122;
            positionTopOffset = 55;
        }
    } else {
        var position = $(this).position();
        var message = $(this).attr('bubble');
        var positionLeftOffset = 60;
        var positionTopOffset = 60;
        if($(this).hasClass('sidebar-price')) {
            positionLeftOffset = 20;
        } else if ($(this).is(':checkbox')) {
            positionLeftOffset = 100;
            positionTopOffset = 105;
        } else if ($(this).is(':radio')) {
            positionLeftOffset = 122;
            positionTopOffset = 55;
        } else if ($(this).hasClass('info')) {
            positionLeftOffset = 100;
            positionTopOffset = 75;
        }
    }
    
    if(message) {
        $('#speech-bubble').remove();
        $(this).before('<div id="speech-bubble"><div id="bubble-message">' + message + '</div><div id="bubble-arrow"></div></div>');
        $("#speech-bubble").css('top', position.top - positionTopOffset);
        $("#speech-bubble").css('left', position.left - positionLeftOffset);
    }
});

$(document).on('mouseleave', '[bubble], #speech-bubble', function() {
    if($('#speech-bubble:hover').length !== 0) {
        return false;
    }
    $('#speech-bubble').remove();
});

function isPostcodeCovered(postcode) {
	
	return true;
	
	postcode = postcode.replace(/ /g, '');
	postcode = postcode.trim().toUpperCase();
	var codeAccepted = false;
	var coveredAreasLength = coveredAreas.length;
	var i = 0;
	for(i; i < coveredAreasLength; i++) {
		var coveredPostcode = coveredAreas[i];
		var postcodeFirstPart = postcode.substr(0, coveredPostcode.length);
		if(postcodeFirstPart === coveredPostcode) {
			codeAccepted = true;
		}
	}
	
	return codeAccepted;
}

function isEmailValid(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function titleCase(str) {
   var splitStr = str.toLowerCase().split(' ');
   for (var i = 0; i < splitStr.length; i++) {
       // You do not need to check if i is larger than splitStr length, as your for does that for you
       // Assign it back to the array
       splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
   }
   // Directly return the joined string
   return splitStr.join(' '); 
}