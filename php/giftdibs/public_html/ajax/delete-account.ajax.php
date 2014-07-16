<?php
require "../../resources/config.php";
require "../../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must be logged in to complete this action.");
	$session->setMessageType("error");
	$session->redirectTo($app->config("page","home"));
	die;
}

if ($_POST) {

	$errors = "";
	
	$signature = (isset($_POST["signature"])) ? (string)$_POST["signature"] : null;
	
	// Signature
	if (!isset($signature) || $me->createSignature($session->getUserId()) != $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));

	} else {
	
		if ($me->delete()) {
			$successMessage = "Your account was successfully deleted.";
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $app->config("page","home")
			));
			$session->logout();
		} else {
			$response = new Response($app, array(
				"status" => "error",
				"message" => "Oops! Something went wrong. <a href=\"{$app->config('page','contact')}\">Could you tell us what happened?</a>"
			));
		}
		
	}
	$response->sendIt();
}
