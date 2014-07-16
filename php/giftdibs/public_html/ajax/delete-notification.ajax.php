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

	$notificationId = $_POST["notificationId"];
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "notifications");
	
	$notification = new Notification($db);
	$notification = $notification->set("notificationId", $notificationId)->find(1);
	
	$validator = new Validator();
	$validator->run();
	
	if (!$notification) {
		$validator->addError("The notification doesn't exist.");
	}
	
	if ($notification->get("userId") != $me->get("userId")) {
		$validator->addError("You do not have permission to delete this notification.");
	}
	
	if (!isset($signature) || $me->createSignature($notificationId) !== $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		
		$notification->delete();
		
		$successMessage = "Notification successfully deleted.";
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
