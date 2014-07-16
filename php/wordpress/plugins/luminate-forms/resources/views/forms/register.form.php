<?php
$loForm = new LuminateForm($loApp, array(
	"name" 				=> "registration",
	"heading" 			=> "Register<span> or <a href=\"{$loApp->config('url','login')}\">Log in</a></span>",
	"action" 			=> "{$loApp->config('action','register')}",
	"luminateExtendData" => array("callback" => "loForms_registerCallback"),
	"cssClass" 			=> "lo-form-register",
	"orientation" 		=> "orientation"
));
$firstName = new LuminateFormField($loForm, array(
	"name" 				=> "name.first",
	"label" 			=> "First name",
	"type" 				=> "text",
	"maxLength" 		=> "50",
	"required" 			=> "true",
	"useMask"			=> "true",
	"submitOnReturn" 	=> "true",
	"class"				=> "field-firstName"
));
$lastName = new LuminateFormField($loForm, array(
	"name" 				=> "name.last",
	"label" 			=> "Last name",
	"type" 				=> "text",
	"maxLength" 		=> "50",
	"required" 			=> "true",
	"useMask"			=> "true",
	"submitOnReturn" 	=> "true",
	"class"				=> "field-lastName"
));
$username = new LuminateFormField($loForm, array(
	"name" 				=> "user_name",
	"label" 			=> "Username",
	"type" 				=> "text",
	"maxLength" 		=> "255",
	"required" 			=> "true",
	"useMask"			=> "true",
	"submitOnReturn" 	=> "true"
));
$password = new LuminateFormField($loForm, array(
	"name" 				=> "user_password",
	"label" 			=> "New password",
	"type" 				=> "password",
	"maxLength" 		=> "90",
	"required" 			=> "true",
	"useMask"			=> "true",
	"submitOnReturn" 	=> "true"
));
$email = new LuminateFormField($loForm, array(
	"name" 				=> "email.primary_address",
	"label" 			=> "Email address",
	"type" 				=> "text",
	"maxLength" 		=> "255",
	"required" 			=> "true",
	"useMask"			=> "true",
	"submitOnReturn" 	=> "true"
));
$submit = new LuminateFormField($loForm, array(
	"name" 				=> "submit",
	"label" 			=> "Register now",
	"type" 				=> "submit",
	"fieldClass" => "btn-primary"
));
$loForm->start();
	$loForm->heading();
	$loForm->alert();
	$loForm->hiddenFields("create");
	$firstName->render();
	$lastName->render();
	$username->render();
	$password->render();
	$email->render();
	$submit->render();
$loForm->stop();