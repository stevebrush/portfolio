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

	$userId = $session->getUserId();
	$label = (isset($_POST["label"])) ? $_POST["label"] : null;
	$month = (isset($_POST["month"])) ? $_POST["month"] : null;
	$day = (isset($_POST["day"])) ? $_POST["day"] : null;
	$year = date("Y");
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "profile");
	$reminder = new NotificationTypeCustom($db);
	$reminder->getInputs();
	
	$validator = new Validator();
	$validator->addInput($reminder->getInput("label"), $label);
	$validator->addInput($reminder->getInput("month"), $month);
	$validator->addInput($reminder->getInput("day"), $day);
	$validator->run();
	
	if (!checkdate((int)$month, (int)$day, (int)$year)) {
		$validator->addError("The month/day combonation isn't a real date.");
	}
	
	// Signature
	else if (!isset($signature) || $me->createSignature("new-reminder") !== $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));

	} else {
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date", "format"));
		$reminder = $reminder->set(array(
			"userId" => $userId,
			"label" => $validator->getInputValue("label"),
			"month" => $validator->getInputValue("month"),
			"day" => $validator->getInputValue("day"),
			"dateCreated" => $dateCreated
		))->create();
		
		$successMessage = "New reminder successfully created.";
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