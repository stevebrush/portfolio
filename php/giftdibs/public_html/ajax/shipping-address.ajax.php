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
	
	$validator = new Validator($db);
	$me->getInputs();
	$validator->addInput( $me->getInput("address1"), $_POST["address1"]);
	$validator->addInput( $me->getInput("address2"), $_POST["address2"]);
	$validator->addInput( $me->getInput("city"), $_POST["city"]);
	$validator->addInput( $me->getInput("state"), $_POST["state"]);
	$validator->addInput( $me->getInput("zip"), $_POST["zip"]);
	$validator->run();
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
	} 
	
	else {
		
		$date = new DateTime(); 
		$dateUpdated = $date->format($app->config('date','format'));
		
		$me->set(array(
			"userId" => $session->getUserId(),
			"address1" => $validator->getInputValue("address1"),
			"address2" => $validator->getInputValue("address2"),
			"city" => $validator->getInputValue("city"),
			"state" => $validator->getInputValue("state"),
			"zip" => $validator->getInputValue("zip"),
			"timestamp" => $dateUpdated
		));
		$me->update();
		
		$successMessage = "Shipping address successfully updated.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config("page","shipping-address")
		));
		
	}
	$response->sendIt();
}
