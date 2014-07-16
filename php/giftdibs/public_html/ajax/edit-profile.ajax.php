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

	$imageErrors = "";
	
	$signature 			= $_POST["signature"];
	$thumbnail 			= (isset($_FILES["thumbnail"])) ? $_FILES["thumbnail"] : null;
	$birthdayPrivate	= (isset($_POST["birthdayPrivate"])) ? 0 : 1;
	$deleteThumbnail	= (isset($_POST["deleteThumbnail"])) ? 1 : 0;

	$validator = new Validator();
	$me->getInputs();
	$validator->addInput( $me->getInput("firstName"), $_POST["firstName"]);
	$validator->addInput( $me->getInput("lastName"), $_POST["lastName"]);
	$validator->addInput( $me->getInput("gender"), (isset($_POST["gender"])) ? $_POST["gender"] : null);
	$validator->addInput( $me->getInput("birthdayMonth"), $_POST["birthdayMonth"]);
	$validator->addInput( $me->getInput("birthdayDay"), $_POST["birthdayDay"]);
	$validator->addInput( $me->getInput("birthdayYear"), $_POST["birthdayYear"]);
	$validator->run();
	
	$formattedBirthday = $validator->getInputValue("birthdayYear") . "-" . $validator->getInputValue("birthdayMonth") . "-" . $validator->getInputValue("birthdayDay");

	if (!$app->validateAge($formattedBirthday)) {
		$validator->addError("You must be at least {$app->getMinAgeAllowed()} years old to use {$app->config('app','name')}.");
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature("edit-profile") != $signature) {
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
		
		$member = new User($db);
		$member = $member->set("userId", $session->getUserId())->find(1);
		$member->set(array(
			"firstName" => $validator->getInputValue("firstName"),
			"lastName" => $validator->getInputValue("lastName"),
			"gender" => $validator->getInputValue("gender"),
			"birthday" => $formattedBirthday,
			"birthdayPrivate" => $birthdayPrivate,
			"timestamp" => $dateUpdated
		))->update();

		if ($deleteThumbnail) {
			$member->deleteThumbnail();
		}

		if (isset($thumbnail)) {
			$img = $member->createThumbnail( $thumbnail );
			if ($img->isCreated()) {
				$member->set( "imageId", $img->get("imageId") )->update();
			} else {
				$imageErrors = $img->getErrors();
			}
		}
		
		if ($imageErrors) {
			$session->setMessage($imageErrors);
			$session->setMessageType("error");
			$response = new Response($app, array(
				"status" => "error",
				"message" => $imageErrors
			));
		} else {
			$successMessage = "Settings saved.";
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $app->config("page","edit-profile")
			));
		}
	}
	$response->sendIt();
}