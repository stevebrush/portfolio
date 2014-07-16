<?php
if (!$response = $loApp->_getDonationFormInfo($form_id)) {
	echo "<p>The donation form with ID ".$form_id." could not be found.</p>";
	return false;
}

$response = $response['getDonationFormInfoResponse'];

$supportsInstallments 	= $response['supportsInstallments']; // true, false
$supportsDesignation 	= $response['supportsDesignation']; // true, false
$supportsSustaining 	= $response['supportsSustaining']; // true, false
$supportsTribute 		= $response['supportsTribute']; // true, false

$donationLevels 		= $response['donationLevels']['donationLevel']; // $5.00, etc.
$paymentCards 			= $response['paymentCards']['paymentCard']; // visa, mastercard, american express, discover
$externalProcessors		= $response['externalProcessors']['externalProcessor']; // paypal, amazon
$donationFields			= $response['donationFields']['donationField'];

$loForm = new LuminateForm($loApp, array(
	"slug" => "donate",
	"action" => "{$loApp->config('action','donate')}",
	"enctype" => "application/x-www-form-urlencoded",
	"luminateExtendData" => array("callback" => "loForms_donateCallback"),
	"cssClass" => "lo-form-donate",
	"orientation" => "horizontal"
));

$donor_firstName = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.name.first",
	"label" => "First name:",
	"required" => "true",
	"value" => $loConst->getFieldValue('name.first')
));

$donor_lastName = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.name.last",
	"label" => "Last name:",
	"required" => "true",
	"value" => $loConst->getFieldValue('name.last')
));

$donor_address1 = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.address.street1",
	"label" => "Address line 1:",
	"required" => "true",
	"value" => $loConst->getFieldValue('primary_address.street1')
));

$donor_address2 = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.address.street2",
	"label" => "Address line 2:",
	"value" => $loConst->getFieldValue('primary_address.street2')
));

$donor_city = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.address.city",
	"label" => "City:",
	"required" => "true",
	"value" => $loConst->getFieldValue('primary_address.city')
));

$donor_state = new LuminateFormField($loForm, array(
	"type" => "select",
	"name" => "donor.address.state",
	"label" => "State:",
	"choices" => $loApp->fetchFieldChoices('primary_address.state'),
	"required" => "true",
	"value" => $loConst->getFieldValue('primary_address.state')
));

$donor_zip = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.address.zip",
	"label" => "Postal code:",
	"required" => "true",
	"value" => $loConst->getFieldValue('primary_address.zip')
));

$donor_phone = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.phone",
	"label" => "Phone number:",
	"required" => "true",
	"value" => $loConst->getFieldValue('home_phone')
));

$donor_email = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "donor.email",
	"label" => "Email address:",
	"required" => "true",
	"maxLength" => "225",
	"value" => $loConst->getFieldValue('email.primary_address')
));

$donor_email_opt = new LuminateFormField($loForm, array(
	"type" => "checkbox",
	"name" => "donor.email_opt_in",
	"label" => "Please send me news and updates",
	"checked" => "true"
));

$billing_sameAsDonor = new LuminateFormField($loForm, array(
	"type" => "checkbox",
	"name" => "same-as-donor",
	"label" => "Same as donor information",
	"value" => "true",
	"checked" => "false"
));

$billing_firstName = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.name.first",
	"label" => "First name:",
	"required" => "true",
	"value" => $loConst->getFieldValue('name.first')
));

$billing_lastName = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.name.last",
	"label" => "Last name:",
	"required" => "true",
	"value" => $loConst->getFieldValue('name.last')
));

$billing_address1 = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.address.street1",
	"label" => "Address line 1:",
	"required" => "true"
));

$billing_address2 = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.address.street2",
	"label" => "Address line 2:"
));

$billing_city = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.address.city",
	"label" => "City:",
	"required" => "true"
));

$billing_state = new LuminateFormField($loForm, array(
	"type" => "select",
	"name" => "billing.address.state",
	"label" => "State:",
	"choices" => $loApp->fetchFieldChoices('primary_address.state'),
	"required" => "true",
	"value" => $loConst->getFieldValue('primary_address.state')
));

$billing_zip = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.address.zip",
	"label" => "Postal code:",
	"required" => "true"
));

$billing_phone = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "billing.phone",
	"label" => "Phone number:",
	"required" => "true"
));

$cardNumber = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "card_number",
	"label" => "Credit card number:",
	"required" => "true"
));
/*
$cardExpirationMonth = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "card_exp_date_month",
	"label" => "Expiration month",
	"required" => "true"
));

$cardExpirationYear = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "card_exp_date_year",
	"label" => "Expiration year",
	"required" => "true"
));
*/

$cardSecurityCode = new LuminateFormField($loForm, array(
	"type" => "text",
	"name" => "card_cvv",
	"label" => "Security code (CVV):",
	"required" => "true",
	"maxLength" => "3"
));

$button = new LuminateFormField($loForm, array(
	"type" => "submit",
	"label" => "Submit donation",
	"fieldClass" => "btn-primary"
));

$loForm->start();
	$loForm->heading();
	?>
	<p><span class="lo-required-field-marker">*</span> = required</p>
	<?php
	$loForm->alert();
	$loForm->hiddenFields('donate');
	?>
	
	<input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
	<input type="hidden" name="validate" value="true">
	
	
	<!-- TEST MODE -->
	<input type="hidden" name="df_preview" value="true">
	
	
	<?php
	// Giving levels
	if ($donationLevels) {
	
		echo "<h3 class=\"section-heading\">{$giving_levels_heading}</h3>";
		echo "<div class=\"form-group\">";
			echo "<label class=\"control-label col-sm-3\">Please select:</label>";
			echo "<div class=\"radio-group col-sm-9\">";
		
				$counter = 0;
				
				foreach ($donationLevels as $level) {
					
					$counter++;
					
					echo "<div class=\"radio\">";
					if ($level['userSpecified'] == "true") {
						echo "<div class=\"other-amount\">";
							echo "<label for=\"level_{$level['level_id']}_{$loForm->getFormId()}_{$counter}\"><input type=\"radio\" data-user-specified=\"true\" name=\"level_id\" value=\"{$level['level_id']}\" id=\"level_{$level['level_id']}_{$loForm->getFormId()}_{$counter}\"><strong>{$level['name']}</strong></label>";
							echo "<input placeholder=\"Enter an amount\" style=\"display:none;\" disabled=\"disabled\" data-min-amount=\"{$level['amount']['decimal']}\" type=\"text\" name=\"other_amount\" maxlength=\"10\" class=\"form-control form-control-other\">";
						echo "</div>";
					} else {
						echo "<label for=\"level_{$level['level_id']}_{$loForm->getFormId()}_{$counter}\"><input type=\"radio\" data-user-specified=\"false\" name=\"level_id\" value=\"{$level['level_id']}\" id=\"level_{$level['level_id']}_{$loForm->getFormId()}_{$counter}\"><strong>{$level['name']}</strong> - {$level['amount']['formatted']}</label>";
					}
					echo "</div>";
					
				}
			echo "</div>";
		echo "</div>";
	}
	
	// Donor info
	echo "<h3 class=\"section-heading\">{$donor_information_heading}</h3>";
	echo "<div class=\"form-group fieldset-donor\">";
		$donor_firstName->render();
		$donor_lastName->render();
		$donor_address1->render();
		$donor_address2->render();
		$donor_city->render();
		$donor_state->render();
		$donor_zip->render();
		$donor_phone->render();
		$donor_email->render();
		$donor_email_opt->render();
	echo "</div>";
	
	// billing info
	echo "<h3 class=\"section-heading\">{$billing_information_heading}</h3>";
	$billing_sameAsDonor->render();
	echo "<div class=\"form-group fieldset-billing\">";
		$billing_firstName->render();
		$billing_lastName->render();
		$billing_address1->render();
		$billing_address2->render();
		$billing_city->render();
		$billing_state->render();
		$billing_zip->render();
		$billing_phone->render();
	echo "</div>";
	
	// card info
	echo "<h3 class=\"section-heading\">{$payment_information_heading}</h3>";
	$cardNumber->render();
	$cardSecurityCode->render();
	/*
	$cardExpirationMonth->render();
	$cardExpirationYear->render();
	*/
	?>
	<div class="form-group">
		<label class="control-label col-sm-3">Expiration date:</label>
		<div class="col-sm-9">
			<div class="row">
				<div class="col-sm-6">
					<select class="form-control" name="card_exp_date_month">
						<option>Month</option>
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</div>
				<div class="col-sm-6">
					<select class="form-control" name="card_exp_date_year">
						<option>Year</option>
						<option value="2014">2014</option>
						<option value="2015">2015</option>
						<option value="2016">2016</option>
						<option value="2017">2017</option>
						<option value="2018">2018</option>
						<option value="2019">2019</option>
						<option value="2019">2020</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<?php
	$button->render();
$loForm->stop();
?>