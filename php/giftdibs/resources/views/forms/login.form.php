<?php 
$formSettings = array();
$formDefaults = array(
	"action" => "ajax/login.ajax.php",
	"orientation" => "horizontal",
	"cssClass" => "gd-login-form",
	"heading" => "{$app->config('app','name')} Log In"
);

if (isset($formOptions)) {
	$formSettings = array_merge($formDefaults, $formOptions);
} else {
	$formSettings = $formDefaults;
}

$form = new Form($formSettings);

$me->getInputs();

$redirect = new FormField($form, array(
	"type" => "hidden",
	"name" => "redirect",
	"value" => (isset($_GET['redirect'])) ? $_GET['redirect'] : "index.php"
));

$facebookLogin = new FormField($form, array(
	"type" => "static",
	"value" => "<a href=\"#\" class=\"btn btn-primary btn-facebook btn-facebook-login btn-lg \" data-loading-text=\"Processing...\">Log in using Facebook</a>"
));

$email = new FormField($form, $me->getField("emailAddress"));

$password = new FormField($form, $me->getField("password"));
$password->setLabel("Password");
$password->setHelplet("<a href=\"{$app->config('page','reset-password')}\">Forgot your password?</a>");

$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Log In",
	"fieldClass" => "btn-primary"
));

$form->start();?>
	<div class="form-heading">
		<h1 class="form-title">
			<?php echo $form->getHeading(); ?>
		</h1>
		<a href="<?php echo $app->config('page','signup'); ?>">I need an account&nbsp;&rarr;</a>
	</div>
	<div class="form-body">
		<?php
		$form->alert();
		$redirect->render("field");
		$facebookLogin->render();
		$email->render();
		$password->render();
		?>
	</div>
	<div class="form-footer">
		<?php $submit->render("field"); ?>
	</div>
	<?php
$form->stop();
