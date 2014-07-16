<?php
$loForm = new LuminateForm($loApp, array(
	"name" 		=> "profile",
	"heading" 	=> "Edit profile",
	"action" 	=> "{$loApp->config('action','profile')}",
	"luminateExtendData" => array("callback" => "loForms_profileCallback"),
	"cssClass" => "lo-form-profile"
));

// account details
$username = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Username",
	"name" 			=> "user_name",
	"maxLength" 	=> "255",
	"required" 		=> "true",
	"value" 		=> $loConst->getFieldValue('user_name')
));

$resetPasswordLink = new LuminateFormField($loForm, array(
	"type" 			=> "link",
	"label" 		=> "Reset my password&nbsp;&#155;",
	"href" 			=> $loApp->config('url','reset-password'),
	"class" 		=> "field-reset-password"
));

$email = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Email address",
	"name" 			=> "email.primary_address",
	"maxLength" 	=> "255",
	"required" 		=> "true",
	"value" 		=> $loConst->getFieldValue('email.primary_address')
));

$acceptsEmail = new LuminateFormField($loForm, array(
	"type" 			=> "checkbox",
	"name" 			=> "email.accepts_email",
	"label" 		=> "Send me email updates and news",
	"value" 		=> "true",
	"checked" 		=> (bool)$loConst->getFieldValue('email.accepts_email')
));


// bio
$title = new LuminateFormField($loForm, array(
	"type" 			=> "select",
	"label" 		=> "Title",
	"name" 			=> "name.title",
	"choices" 		=> $loApp->fetchFieldChoices('name.title'),
	"value" 		=> $loConst->getFieldValue('name.title')
));

$firstName = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "First name",
	"name" 			=> "name.first",
	"maxLength" 	=> "50",
	"required" 		=> "true",
	"value" 		=> $loConst->getFieldValue('name.first')
));

$middleName = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Middle name",
	"name" 			=> "name.middle",
	"maxLength" 	=> "50",
	"value" 		=> $loConst->getFieldValue('name.middle')
));

$lastName = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Last name",
	"name" 			=> "name.last",
	"maxLength" 	=> "50",
	"required" 		=> "true",
	"value" 		=> $loConst->getFieldValue('name.last')
));

$suffix = new LuminateFormField($loForm, array(
	"type" 			=> "select",
	"label" 		=> "Suffix",
	"name" 			=> "name.suffix",
	"choices" 		=> $loApp->fetchFieldChoices('name.suffix'),
	"value" 		=> $loConst->getFieldValue('name.suffix')
));

$profSuffix = new LuminateFormField($loForm, array(
	"type" 			=> "select",
	"label" 		=> "Professional suffix",
	"name" 			=> "name.prof_suffix",
	"choices" 		=> $loApp->fetchFieldChoices('name.prof_suffix'),
	"value" 		=> $loConst->getFieldValue('name.prof_suffix')
));


// address
$address1 = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Address line 1",
	"name" 			=> "primary_address.street1",
	"required" 		=> "true",
	"class" 		=> "field-street-1",
	"maxLength" 	=> "128",
	"value" 		=> $loConst->getFieldValue('primary_address.street1')
));

$address2 = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Address line 2",
	"name" 			=> "primary_address.street2",
	"required" 		=> "false",
	"class" 		=> "field-street-2",
	"maxLength" 	=> "128",
	"value" 		=> $loConst->getFieldValue('primary_address.street2')
));

$city = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "City",
	"name" 			=> "primary_address.city",
	"required" 		=> "true",
	"class" 		=> "field-city",
	"maxLength" 	=> "64",
	"value" 		=> $loConst->getFieldValue('primary_address.city')
));

$state = new LuminateFormField($loForm, array(
	"type" 			=> "select",
	"label" 		=> "State/province",
	"name" 			=> "primary_address.state",
	"choices" 		=> $loApp->fetchFieldChoices('primary_address.state'),
	"required" 		=> "true",
	"class" 		=> "field-state",
	"value" 		=> $loConst->getFieldValue('primary_address.state')
));

$zip = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "ZIP/postal code",
	"name" 			=> "primary_address.zip",
	"required" 		=> "true",
	"class" 		=> "field-zip",
	"maxLength" 	=> "40",
	"value" 		=> $loConst->getFieldValue('primary_address.zip')
));

$country = new LuminateFormField($loForm, array(
	"type" 			=> "select",
	"label" 		=> "Country",
	"name" 			=> "primary_address.country",
	"choices" 		=> $loApp->fetchFieldChoices('primary_address.country'),
	"required" 		=> "true",
	"class" 		=> "field-country",
	"value" 		=> $loConst->getFieldValue('primary_address.country')
));

$acceptsPostalMail = new LuminateFormField($loForm, array(
	"type" 			=> "checkbox",
	"name" 			=> "accepts_postal_mail",
	"label" 		=> "Okay to send postal mail",
	"value" 		=> "true",
	"checked" 		=> (bool)$loConst->getFieldValue('accepts_postal_mail')
));


// phone
$homePhone = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Home phone",
	"name" 			=> "home_phone",
	"maxLength" 	=> "50",
	"value" 		=> $loConst->getFieldValue('home_phone')
));

$workPhone = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Work phone",
	"name" 			=> "work_phone",
	"maxLength" 	=> "50",
	"value" 		=> $loConst->getFieldValue('work_phone')
));

$cellPhone = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Mobile phone",
	"name" 			=> "mobile_phone",
	"maxLength" 	=> "50",
	"value" 		=> $loConst->getFieldValue('mobile_phone')
));


// employer
$employer = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Employer",
	"name" 			=> "employment.employer",
	"maxLength" 	=> "100",
	"value" 		=> $loConst->getFieldValue('employment.employer')
));

$occupation = new LuminateFormField($loForm, array(
	"type" 			=> "select",
	"label" 		=> "Occupation",
	"name" 			=> "employment.occupation",
	"choices" 		=> $loApp->fetchFieldChoices('employment.occupation'),
	"value" 		=> $loConst->getFieldValue('employment.occupation')
));

$position = new LuminateFormField($loForm, array(
	"type" 			=> "text",
	"label" 		=> "Position",
	"name" 			=> "employment.position",
	"maxLength" 	=> "64",
	"value" 		=> $loConst->getFieldValue('employment.position')
));

$submitButton = new LuminateFormField($loForm, array(
	"name" 			=> "submit",
	"label" 		=> "Update profile",
	"type" 			=> "submit",
	"fieldClass" => "btn-primary"
));

$loForm->start();
	$loForm->heading();
	$loForm->alert();
	$loForm->hiddenFields("update"); 
	?>
	<input type="hidden" name="cons_id" value="<?php echo $loConst->getConsId(); ?>">
	<input type="hidden" name="sso_auth_token" value="<?php echo $loConst->getAuthToken(); ?>">
	<?php
	
	echo "<h3 class=\"section-heading\">Account details</h3>";
	$username->render();
	$resetPasswordLink->render();
	$email->render();
	$acceptsEmail->render();
	
	echo "<h3 class=\"section-heading\">Biographical information</h3>";
	$title->render();
	$firstName->render();
	$middleName->render();
	$lastName->render();
	$suffix->render();
	$profSuffix->render();
	
	echo "<h3 class=\"section-heading\">Address</h3>";
	$address1->render();
	$address2->render();
	$city->render();
	$state->render();
	$zip->render();
	$country->render();
	$acceptsPostalMail->render();
	
	echo "<h3 class=\"section-heading\">Phone</h3>";
	$homePhone->render();
	$workPhone->render();
	$cellPhone->render();
	
	echo "<h3 class=\"section-heading\">Employment</h3>";
	$employer->render();
	$occupation->render();
	$position->render();
	$submitButton->render();
$loForm->stop();
