<?php
$loForm = new LuminateForm($loApp, array(
	"name" => "login",
	"action" => $loApp->config("action","login"),
	"heading" => "Log in or <a href=\"{$loApp->config('url','register')}\">Sign up</a>",
	"luminateExtendData" => array("callback" => "loForms_loginCallback"),
	"cssClass" => "lo-form-login"
));

$userName = new LuminateFormField($loForm, array(
	"name" 				=> "user_name",
	"type" 				=> "text",
	"required" 			=> "true",
	"autoComplete" 		=> "on",
	"placeholder"		=> "Username",
	"submitOnReturn" 	=> "true"
));

$password = new LuminateFormField($loForm, array(
	"name" 				=> "password",
	"type" 				=> "password",
	"required" 			=> "true",
	"maxLength"			=> "90",
	"autoComplete" 		=> "on",
	"placeholder"		=> "Password",
	"submitOnReturn" 	=> "true"
));

$submitButton = new LuminateFormField($loForm, array(
	"name" 				=> "submit",
	"label" 			=> "Log in",
	"type" 				=> "submit",
	"fieldClass" 		=> "btn-primary"
));

$rememberMe = new LuminateFormField($loForm, array(
	"name" 				=> "remember_me",
	"type" 				=> "checkbox",
	"label" 			=> "Remember me",
	"checked"			=> "true",
	"value"				=> "true"
));

$loForm->start();
	$loForm->heading();
	$loForm->alert();
	$loForm->hiddenFields('login');
	$userName->render();
	$password->render();
	$submitButton->render();
	$rememberMe->render();
	?>
	<a href="<?php echo $loApp->config("url","reset-password"); ?>">I forgot my password&nbsp;&rarr;</a>
	<?php
$loForm->stop();
