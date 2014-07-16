<?php
$currentPassword = $me->get("password");
$fbLoggedIn = false;

if (!isset($rpToken)) {
	try {
		$fbMe = $facebook->api('/me');
		$fbProfileLink = $fbMe["link"];
		$fbLoggedIn = true;
	} catch (FacebookApiException $e) {
		$fbLoggedIn = false;
	}
}
$form = new Form(array(
	"slug" => "change-password",
	"cssClass" => "change-password-form",
	"heading" => "Change password",
	"orientation" => "horizontal",
	"action" => $app->config('ajax','change-password')
));

$me->getInputs();

$password = new FormField($form, $me->getField("password"));
$password->setLabel("Old password");

$passwordNew = new FormField($form, $me->getField("password"));
$passwordNew->setName("passwordNew");
$passwordNew->setLabel("New password");

$passwordNewAgain = new FormField($form, $me->getField("password"));
$passwordNewAgain->setName("passwordNewAgain");
$passwordNewAgain->setLabel("New password again");

$userId = new FormField($form, array(
	"type" => "hidden",
	"name" => "userId",
	"value" => $me->get("userId")
));
$signature = new FormField($form, array(
	"type" => "hidden",
	"name" => "signature",
	"value" => $me->createSignature("change-password")
));
$submitButton = new FormField($form, array(
	"type" => "submit",
	"label" => "Submit",
	"fieldClass" => "btn-primary"
));

$form->start(); 
$form->heading();
$form->alert();
$userId->render();
$signature->render();
if ($session->isLoggedIn() && !isset($rpToken)) :
	if ($currentPassword !== "") :
		$password->render();
		$passwordNew->render();
		$passwordNewAgain->render();
		$submitButton->render();
	elseif ($fbLoggedIn) : ?>
		<p class="alert alert-info">Since you registered using your Facebook account, we don't have a unique <?php echo $app->config('app','name'); ?> password for you in our records.</p>
		<p><a href="<?php echo $app->config('page','reset-password') ?>" class="btn btn-primary">Create new password</a></p>
	<?php 
	elseif (!$fbLoggedIn) : ?>
		<p>We don't have a password registered on your account. This usually occurs if you initially registered for <?php echo $app->config('app','name'); ?> using your Facebook account, and later unlinked your account to the <?php echo $app->config('app','name'); ?> application in Facebook Privacy Settings.</p>
		<p>You can either:</p>
		<p><a href="#" class="btn btn-facebook-link-account">Link Your Facebook Account</a></p>
		<p>Or, we can create a unique <?php echo $app->config('app','name'); ?> password for you:</p>
		<p><a href="<?php echo $app->config('page','reset-password') ?>" class="btn btn-primary">Reset password</a></p>
	<?php 
	endif;
elseif (isset($rpToken) || $currentPassword !== "") : ?>
	<input type="hidden" name="token" value="<?php echo $rpToken; ?>">
		<?php 
		$passwordNew->render();
		$passwordNewAgain->render();
		$submitButton->render();
	endif;
$form->stop();
		