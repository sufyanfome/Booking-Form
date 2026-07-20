<?php
/*
Template Name: Booking Form New
*/
?>
<?php get_header();?>


<?php

the_post();
global $post;

if(tw_option('pagebuilder') && get_post_meta($post->ID, 'waves_metabox_shortcode', true) && !post_password_required()){
    the_content();
}else{
    echo '<div class="container">';
        echo '<div class="row">';
            if(get_metabox('layout') == "left" || get_metabox('layout') == "right"){
                get_sidebar();
              /*  echo "<div class='col-md-9'>"; */
                    echo '<div class="entry-content">';
echo '<div class="breadcrumbs">';
    if(function_exists('bcn_display'))
    {
        bcn_display();
}
echo '</div>';
                        the_content();
                    echo '</div>';
                    wp_link_pages();
                   /*  comments_template('', false); */
                echo "</div>";
            }else{
                /* echo "<div class='col-md-12'>"; */
                    echo '<div class="entry-content">';
echo '<div class="breadcrumbs">';
    if(function_exists('bcn_display'))
    {
        bcn_display();
}
echo '</div>';
                        the_content();
                    echo '</div>';
                    wp_link_pages();
                    /* comments_template('', false); */
                echo "</div>";
            }
        echo "</div>";
    echo "</div>";
		
}
?>

<div class="row-container light bg-scroll" style="background-image:url(https://www.fastklean.co.uk/wp-content/uploads/2020/02/Book-a-cleaner-London.jpg);"><div class="container"><div class="row"><div class="col-md-12 "><div class="row"><div class="tw-element  col-md-12 nomarg" style="" ><h2 style="text-align: center;font-size:24px;font-weight:bold">Simply fill out the form below</h2>


<?php
require 'header.php';
require 'config/constants.php';


$config_files = scandir(__DIR__.'/'.'config');

foreach ($config_files as $filename)
{
    if($filename !== '.' && $filename !== '..') {
   	 include_once __DIR__.'/'.'config/'.$filename;
    }
}
?>

<script>
$ = jQuery;
</script>

<link rel="stylesheet" type="text/css" href="<?=MAIN_PATH?>css/reset.css">
<link rel="stylesheet" type="text/css" href="<?=MAIN_PATH?>css/style.css">
<link rel="stylesheet" type="text/css" href="<?=MAIN_PATH?>css/custom.css">
<link rel="stylesheet" type="text/css" href="<?=MAIN_PATH?>css/font-awesome/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="<?=MAIN_PATH?>css/datepicker/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">

<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Montserrat" />

<script src="<?=MAIN_PATH?>js/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>

<form id="booking-form" action="<?=MAIN_PATH?>main.php?submit=true" method="post" enctype="multipart/form-data">
	<ul id="progressbar">
		<li class="progressbar-active">Schedule</li>
		<li>Book</li>
		<li>Details</li>
		<li>Submit</li>
	</ul>
	  
	<!-- SCHEDULE -->
	<fieldset id="step-1">
		<div class='title'>Schedule your Cleaning</div>
		<div id="date-container">
			<div id="service-date-container">
				<div class="title hide-on-desktop">Select date</div>
				<div id="service-date"></div>
			</div>
		</div>
		<div id="service-options-container">
			<div class="title hide-on-desktop">Select service</div>
			<select id="choose-service-type">
				<option value="domestic">Domestic</option>
				<option value="commercial">Commercial</option>
			</select>
			
			<select id="domestic-services" class="choose-service">
				<option value="choose-service">Choose service</option>
				<?php
				foreach($domestic_services as $service) {
					?>
					<option value="<?=$service?>"><?=$service?></option>
					<?php
				}
				?>
			</select>
			<select id="commercial-services" class="choose-service" hidden>
				<option value="choose-service">Choose service</option>
				<?php
				foreach($commercial_services as $service) {
					?>
					<option value="<?=$service?>"><?=$service?></option>
					<?php
				}
				?>
			</select>
			<input type="hidden" id="service-type" name="service-type" />
			<input type="hidden" id="service" name="service" />
			
			<div id="timeslots-container">
				<div id="timeslots"></div>
				<table id="timeslots-disabled"><tr><td>Please select a date and service to view available timeslots</td></tr></table>
				<div id="get-free-quote" hidden>Please fill out the form below for a free quote</div>
			</div>
		</div>
		<div class="clearfix"></div>	
		<div id="preform-details-container">
			<div id="preform-details-title" class='title'>Booking Details</div>
		
			<div class="input-wrapper-half">
				<div class="label">Full name</div>
				<input class="required" type="text" id="preform-name" name="preform-name" autocomplete="cc-name"/>
				<span class="error-message"></span>
			</div>
			<div class="input-wrapper-half">
				<div class="label">Email</div>
				<input class="required validateEmail" type="text" id="preform-email" name="preform-email" autocomplete="email">
				<span class="error-message"></span>
			</div>
			<div class="clearfix"></div>
			<div class="input-wrapper-half">
				<div class="label">Postcode</div>
				<input class="required validatePostcode" type="text" id="preform-postcode" name="preform-postcode" autocomplete="postal-code">
				<span class="error-message"></span>
			</div>

			<div class="input-wrapper-half">
				<div class="label">Phone number</div>
				<input class="required" type="text" id="preform-phone" name="preform-phone" autocomplete="tel">
				<span class="error-message"></span>
			</div>
			
			<div id="commercial-message" class="input-wrapper" hidden>
				<div class="label">Message</div>
				<div class="input-wrapper">
					<textarea  name="preform-message"></textarea>
				</div>
			</div>
		</div>
		<div id="availability-notice"><i class="fa fa-info-circle" aria-hidden="true"></i> Availability may vary depending on the area you live in. Please note that we only cover postcodes that are inside M25.
</div>
		<div class="clearfix"></div>	
		<div class="action-button next go-to-booking">Next</div>
	</fieldset>

	<!-- BOOK -->
	<fieldset id="step-2">
		<div id="service-title" class="title"></div>
		<div id="service-container"></div>
		<div class="price"></div>
		<div id="minimum-booking-notice" class="notice"></div>
		<div class="action-button previous go-to-schedule">Previous</div>
		<div class="action-button next go-to-details">Next</div>
	</fieldset>

	<!-- DETAILS --> 
	<fieldset id="step-3">
		<div id="booking-details-container">
		</div>
		<div class="price"></div>
		<div class="action-button previous go-to-booking">Previous</div>
		<div class="action-button next go-to-submit">Next</div>
	</fieldset>
	
	<!-- SUBMIT -->
	<fieldset id="step-4">
		<div id="finalize-booking-container">
			<div class="title">Finalize booking</div>
			<div class="label">Date</div>
			<div id="booking-date"></div>
			<div class="label">Address</div>
			<div id="booking-address"></div>
			<div class="label">Services</div>
			<div id="booking-services"></div>
			<div class="label">Price</div>
			<div id="booking-price"></div>
			<div style="display: none" id="one-off-terms" class="terms-and-conditions">
                <p><strong>Terms & Conditions:</strong></p>
				<p>The client must provide hot water and electricity for the purpose of providing the service.</p>
				<p>A cancellation period of 48 hours notice is required if the service is not needed. Not adhering to this policy can result in the Cleaning Company charging 50% if less than 48 hours are provided or full payment of the quoted price if cancelled on the same day of the service, unless otherwise agreed.</p>
				<p>The customer agrees to cover any parking charges should a payment be required.</p>
			</div>
		</div>
		<div class="action-button previous go-to-details">Previous</div>
		<div class="action-button next submit">Submit</div>
	</fieldset>
	<div id="error-message"></div>
</form>

<div class="hide-on-desktop booking-form-mobile-text">
<p>Are you tired of having an unclean office or a messy home? Have you had enough of dealing with inefficient, inexperienced and incompetent cleaning companies?</p>

<p>If that is the case, then perhaps it is time to make a change! Our cleaning company has been operating since 2001. We are one of the oldest companies of our type in London and would not be so successful if we did not meet or exceed our clients’ expectations.</p>

<p>FastKlean is one of the most accredited cleaning companies in the UK. Our cleaners are background-checked, fully trained, vetted and insured. Book and pay online today!</p>
</div>

<script>
var testmode = ('true' == '<?=TEST_MODE?>');

$(document).ready(function() {
	$('.ui-state-hover').removeClass('ui-state-hover');
});

var discount = parseFloat('<?=DISCOUNT?>');
var discountCodes = JSON.parse('<?=json_encode($discountCodes)?>');

var path = '<?=MAIN_PATH?>';
var members = '<?=MEMBERS?>';
var vat = '<?=VAT?>';
var coveredAreas = JSON.parse('<?=json_encode($coveredAreas)?>');

var carValetingCityCarPrices = '<?=json_encode($carValetingCityCarPrices)?>';
var carValetingSpaceCruisePrices = '<?=json_encode($carValetingSpaceCruisePrices)?>';
var carValetingFourByFourPrices = '<?=json_encode($carValetingFourByFourPrices)?>';
var carValetingVanPrices = '<?=json_encode($carValetingVanPrices)?>';

var rubbishCubicYardsPrices = '<?=json_encode($rubbishCubicYardsPrices)?>';
var rubbishWeightPrices = '<?=json_encode($rubbishWeightPrices)?>';

var hardFloorWoodPrice = '<?=HARD_FLOOR_WOOD_PRICE?>';
var hardFloorRegularPrice = '<?=HARD_FLOOR_REGULAR_PRICE?>';

var endOfTenancyKeysNearPrice = '<?=END_OF_TENANCY_KEYS_NEAR_PRICE?>';
var endOfTenancyKeysFarPrice = '<?=END_OF_TENANCY_KEYS_FAR_PRICE?>';

let servicesHours = <?php echo json_encode($available_hours)?>;
let availableDomesticCleaningServices = <?php echo json_encode($domestic_services)?>;
</script>

<script src="<?=MAIN_PATH?>js/script.js"></script>




</div></div></div></div></div></div><div class="row-container light bg-scroll"><div class="container"><div class="row"><div class="col-md-12 "><div class="row"><div style="" class="tw-element  col-md-12 tw-animate-gen" data-animation-offset="90%" data-animation-delay="200" data-animation="fadeIn"><div class="waves-title"><h3>Our Location</h3><div class="title-bg" style="width: 433px;"></div></div><div class="waves-map"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2482.6151775904186!2d-0.16090782746973284!3d51.520275668351225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48761acead4c1ad3%3A0xf26e56456e8e410b!2sFastKlean!5e0!3m2!1sen!2suk!4v1463492177837" width="585" height="400" style="width:100%" class="makeFluid"></iframe></div></div></div></div></div>
</div></div>

<div style="background-color: #ffffff;" class="row-container light bg-scroll"><div id="above-footer"><div class="container"><div class="row"><div style="margin-top:-60px"></div><div class="col-md-12 "><div class="row"><div class="tw-element  col-md-4"><p class="foot-image-container"><div class="services-effect">
<div class="hovereffect"><img class="img-responsive" src="https://www.fastklean.co.uk/wp-content/uploads/2020/02/Special-Offers.jpg" alt="specialoffer" /><div class="overlay"><h2><a href="https://www.fastklean.co.uk/special-offers/">Special Offers</a></h2><p class="text">
<a class="info" href="https://www.fastklean.co.uk/special-offers/">VIEW MORE</a></p></div></div></div></p>

</div><div style="" class="tw-element  col-md-4"><p class="foot-image-container"><div class="services-effect">
<div class="hovereffect"><img class="img-responsive" src="https://www.fastklean.co.uk/wp-content/uploads/2020/02/Book-a-cleaner-online.jpg" alt="book a cleaner" /><div class="overlay"><h2><a href="https://www.fastklean.co.uk/book-a-cleaner/">Book a Cleaner</a></h2><p class="text">
<a class="info" href="https://www.fastklean.co.uk/book-a-cleaner/">VIEW MORE</a></p></div></div></div></p>

</div><div class="tw-element  col-md-4"><p class="foot-image-container"><div class="services-effect">
<div class="hovereffect"><img class="img-responsive" src="https://www.fastklean.co.uk/wp-content/uploads/2020/02/Call-us-FastKlean.jpg" alt="contactus" /><div class="overlay"><h2><a href="https://www.fastklean.co.uk/contact-a-cleaner/">Contact Us</a></h2><p class="text">
<a class="info" href="https://www.fastklean.co.uk/contact-a-cleaner/">VIEW MORE</a></p></div></div></div></p>
</div></div></div></div></div></div>
</div>
<?php get_footer(); ?>