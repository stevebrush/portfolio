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

	$errors = "";
	$userId = $session->getUserId();
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$choices = (isset($_POST["holidays"])) ? $_POST["holidays"] : false;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "edit-holidays");
	
	$validator = new Validator();
	$validator->run();
	
	// Signature
	if (!isset($signature) || $me->createSignature("edit-holidays") !== $signature) {
		$validator->addError("The signature was not valid.");
	}

	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {

		if ($choices) {
		
			// Delete all rows to prevent duplicates
			$holiday_user = new Holiday_User($db);
			$holiday_user->set("userId", $userId)->delete();
		
			foreach ($choices as $choice) {
				$holiday_user = new Holiday_User($db);
				$holiday_user->set(array(
					"holidayId" => $choice,
					"userId" => $userId
				))->create();
			}
		}
	
		$successMessage = "Holiday settings saved.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $redirect
		));
	}
	$response->sendIt();
}