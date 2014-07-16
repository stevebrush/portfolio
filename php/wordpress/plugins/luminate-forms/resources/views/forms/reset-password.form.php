<?php
$loForm = new LuminateForm($loApp, array(
	"name" => "resetPassword",
	"heading" => "Reset Password<span> or <a href=\"{$loApp->config('url','login')}\">Log In</a></span>",
	"action" => $loApp->config("action","reset-password"),
	"luminateExtendData" => array("callback" => "loForms_resetPasswordCallback"),
	"cssClass" => "lo-form-reset-password"
));
$email = new LuminateFormField($loForm, array(
	"name" => "email",
	"label" => "Email address",
	"type" => "text",
	"required" => "true",
	"submitOnReturn" => "true",
	"useMask" => "true"
));
$submit = new LuminateFormField($loForm, array(
	"name" => "submit",
	"label" => "Send request",
	"type" => "submit",
	"fieldClass" => "btn-primary"
));
$loForm->start();
	$loForm->heading();
	$loForm->deliverResponse();
	$loForm->hiddenFields("login");
	?>
	<input type="hidden" name="send_user_name" value="true">
	<?php
	$email->render();
	$submit->render();
$loForm->stop();