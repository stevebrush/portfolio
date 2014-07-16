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
	
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput($me->getInput("emailAddress"), $_POST["emailAddress"]);
	$validator->addInput($me->getInput("currencyId"), $_POST["currencyId"]);
	$validator->run();
	
	if ($me->emailExists($validator->getInputValue("emailAddress"))) {
		$validator->addError("Your email address already exists in our records. Wanna <a href=\"{$app->config('page','login')}\">login</a>?");
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature("account-details") != $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
	
		// email is different?
		$sendConfirmEmailRequest = false;
		$emailAddress = $validator->getInputValue("emailAddress");
		if ($me->get("emailAddress") != $emailAddress) {
			$sendConfirmEmailRequest = true;
		}
		
		$successMessage = "";

		$me->set(array(
			"emailAddress" => $emailAddress,
			"currencyId" => $validator->getInputValue("currencyId"),
			"timestamp" => $dateCreated
		));
		
		if ($sendConfirmEmailRequest) {
		
			$me->set("emailConfirmed", 0);
			
			/* Create Confirm Email Token */
			$ce = new ConfirmEmailToken($db);
			$ce->set("userId", $session->getUserId())->delete(); // no duplicates
			$rawToken = $ce->generateToken(); // generate user-facing token
			$ce->setToken($rawToken)->create(); // encrypt token and save to database
			$ceLink = $app->config("page", "confirm-email", array( "token" => $rawToken )); // create clickable link
			
			
			// Email Confirmation
			$ceEmail = new Email($app, array(
				"title" => "Please Verify Your Email Address",
				"subject" => "Verify your email address",
				"body" => "<p><a href=\"{$ceLink}\">Verify My Email Address</a></p>",
				"recipients" => array(
					$me->fullName() => $me->get("emailAddress")
				)
			));
			$ceEmail->create();
			
			$successMessage = "Your settings have been saved, but since you changed your email address, we'll need to verify it. Please click on the link provided in the email body to verify your new email address.";
			$session->setMessageType("default");
			
		} else {
		
			$successMessage = "Settings saved.";
			$session->setMessageType("success");
			
		}
		
		$me->update();
		
		$session->setMessage($successMessage);
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config("page","account-details")
		));
		
	}
	$response->sendIt();
}
