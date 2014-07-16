<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must be logged in to complete this action.");
	$session->setMessageType("error");
	$session->redirectTo($app->config("page","home"));
	die;
}

if ($_POST) {
	
	$confirmEmailToken = new ConfirmEmailToken($db);
	$confirmEmailToken->set("userId", $session->getUserId())->delete(); // no duplicates
	$ceToken = $confirmEmailToken->generateToken();
	$confirmEmailToken->setToken($ceToken);
	$confirmEmailToken->create();
	$ceLink = $app->config("page", "confirm-email", array("token"=>$ceToken));
	
	// Email Confirmation
	$ceEmail = new Email($app, array(
		"title" => "Please Verify Your Email Address",
		"subject" => "Verify your email address",
		"body" => "<p><a href=\"{$ceLink}\">Verify your email address</a></p>",
		"recipients" => array(
			$me->fullName() => $me->get("emailAddress")
		)
	));
	$ceEmail->create();
	
	$successMessage = "<strong>A new confirmation email was successfully sent.</strong><br>Click on the link in the body of the email to confirm your email address with {$app->config('app','name')}.";
	$session->setMessage($successMessage);
	$session->setMessageType("success");
	$response = new Response($app, array(
		"status" => "success",
		"message" => $successMessage,
		"redirect" => $app->config("page","home")
	));

	$response->sendIt();
}