<?php
$form = new Form(array(
	"slug" => "reset-password",
	"cssClass" => "reset-password-form",
	"heading" => "Reset password",
	"action" => "{$app->config('ajax','reset-password')}"
));
$me->getInputs();
$email = new FormField($form, $me->getField("emailAddress"));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Send request",
	"fieldClass" => "btn-primary"
));

$form->start();
	$form->heading();
	echo "<p>A link to reset your password will be sent to the email address you used to register at {$app->config('app','name')}.</p>";
	$form->alert();
	$email->render();
	$submit->render();
$form->stop();