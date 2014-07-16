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
	$messageId = (isset($_POST["messageId"])) ? $_POST["messageId"] : null;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "messages");
	
	$message = new Message($db);
	$validator = new Validator();
	$validator->run();
	
	if (! $message = $message->set("messageId", $messageId)->find(1)) {
		$validator->addError("The message does not exist.");
	}
	
	// Signature
	else if (!isset($signature) || $me->createSignature($message->get("messageId")) !== $signature) {
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
		
		$message_user = new Message_User($db);
		$messageUser = $message_user->set(array(
			"messageId" => $messageId,
			"userId" => $userId
		))->find(1);
		$messageUser->set("messageStatusId", "2")->update();
		
		$successMessage = "Message marked as \"unread\".";
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