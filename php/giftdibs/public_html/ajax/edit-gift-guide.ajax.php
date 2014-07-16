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
	
	$signature = $_POST["signature"];
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput( $me->getInput("interests"), $_POST["interests"]);
	$validator->addInput( $me->getInput("favoriteStores"), $_POST["favoriteStores"]);
	$validator->addInput( $me->getInput("shirtSize"), $_POST["shirtSize"]);
	$validator->addInput( $me->getInput("shoeSize"), $_POST["shoeSize"]);
	$validator->addInput( $me->getInput("pantSize"), $_POST["pantSize"]);
	$validator->addInput( $me->getInput("hatSize"), $_POST["hatSize"]);
	$validator->addInput( $me->getInput("ringSize"), $_POST["ringSize"]);
	$validator->run();
	
	// Signature
	if (!isset($signature) || $me->createSignature("edit-gift-guide") != $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		$date = new DateTime(); 
		$dateUpdated = $date->format($app->config("date","format"));
		$me->set(array(
			"interests" => $validator->getInputValue("interests"),
			"favoriteStores" => $validator->getInputValue("favoriteStores"),
			"shirtSize" => $validator->getInputValue("shirtSize"),
			"shoeSize" => $validator->getInputValue("shoeSize"),
			"pantSize" => $validator->getInputValue("pantSize"),
			"hatSize" => $validator->getInputValue("hatSize"),
			"ringSize" => $validator->getInputValue("ringSize"),
			"timestamp" => $dateUpdated
		))->update();
		$successMessage = "Gift guide saved.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config("page", "edit-gift-guide")
		));
	}
	
	$response->sendIt();
	
}
