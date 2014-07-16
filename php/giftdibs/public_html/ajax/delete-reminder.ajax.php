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

	$notificationTypeCustomId = $_POST["notificationTypeCustomId"];
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "edit-reminders");
	
	$reminder = new NotificationTypeCustom($db);
	$reminder = $reminder->set("notificationTypeCustomId", $notificationTypeCustomId)->find(1);
	
	$validator = new Validator();
	$validator->run();
	
	if (!$reminder) {
		$validator->addError("The reminder doesn't exist.");
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($notificationTypeCustomId) !== $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		$reminder->delete();	
		$successMessage = "Reminder successfully deleted.";
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
