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
	$userIds = (isset($_POST["userIds"])) ? $_POST["userIds"] : null;
	$content = (isset($_POST["content"])) ? $_POST["content"] : null;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "messages");
	
	$message = new Message($db);
	$message->getInputs();
	
	$validator = new Validator();
	$validator->addInput($message->getInput("content"), $content);
	$validator->run();
	
	// Signature
	if (!isset($signature) || $me->createSignature("new-message") !== $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if (!isset($userIds)) {
		$validator->addError("Please specify at least one recipient.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));

	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date", "format"));
	
		$newMessage = $message->set(array(
			"userId" => $userId,
			"content" => $validator->getInputValue("content"),
			"dateCreated" => $dateCreated,
			"timestamp" => $dateCreated
		))->create();
		
		// add author of message to all IDs
		$userIds[] = $userId;
		
		$recipients = array();
		foreach ($userIds as $id) {
			$message_user = new Message_User($db);
			$message_user->set(array(
				"messageId" => $newMessage->get("messageId"),
				"userId" => $id,
				"messageStatusId" => ($id === $userId) ? "1" : "2", // read, unread
				"timestamp" => $dateCreated
			))->create();
			
			// Send email
			if ($id !== $userId) {
				$user = new User($db);
				$user = $user->set("userId", $id)->find(1);
				if ($user && $user->acceptsEmailFor("new-message")) {
					$recipients[$user->fullName()] = $user->get("emailAddress");
				}
			}
		}
		if (count($recipients)) {
			$email = new Email($app, array(
				"title" => "{$me->fullName()} sent you a message on {$app->config('app', 'name')}",
				"subject" => "{$me->fullName()} sent you a message on {$app->config('app', 'name')}",
				"body" => "<p>{$content}</p><p><a href=\"{$app->config('page', 'message', array('messageId'=>$newMessage->get('messageId')))}#message-reply-container\">Write a reply</a></p>",
				"recipients" => $recipients
			));
			$email->create();
		}
	
		$successMessage = "Message successfully sent. <a href=\"" . $app->config("page", "message", array("messageId" => $newMessage->get("messageId"))) . "\">View&nbsp;&rarr;</a>";
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