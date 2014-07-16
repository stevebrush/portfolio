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
	$content = (isset($_POST["content"])) ? $_POST["content"] : null;
	$messageId = (isset($_POST["messageId"])) ? $_POST["messageId"] : null;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "messages");
	
	$message = new Message($db);
	$reply = new MessageReply($db);
	$reply->getInputs();
	
	$validator = new Validator();
	$validator->addInput($reply->getInput("content"), $content);
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
	
		$content = $validator->getInputValue("content");
		
		// create new reply
		$newReply = $reply->set(array(
			"userId" => $userId,
			"messageId" => $messageId,
			"content" => $content,
			"dateCreated" => $dateCreated,
			"timestamp" => $dateCreated
		))->create();
		
		// update parent message
		$message->set("timestamp", $dateCreated)->update();
		
		// update message_user
		$message_user = new Message_User($db);
		$messageUsers = $message_user->set("messageId", $messageId)->find();
		$recipients = array();
		if ($messageUsers) {
			foreach ($messageUsers as $mu) {
				$mu->set(array(
					"messageStatusId" => ($mu->get("userId") === $userId) ? "1" : "2", // read, unread
					"timestamp" => $dateCreated
				))->update();
				// Send email
				$user = new User($db);
				$user = $user->set("userId", $mu->get("userId"))->find(1);
				if ($user && $user->acceptsEmailFor("new-message")) {
					$recipients[$user->fullName()] = $user->get("emailAddress");
				}
			}
		}
		if (count($recipients)) {
			$email = new Email($app, array(
				"title" => "{$me->fullName()} sent you a message on {$app->config('app', 'name')}",
				"subject" => "{$me->fullName()} sent you a message on {$app->config('app', 'name')}",
				"body" => "<p>{$content}</p><p><a href=\"{$app->config('page', 'message', array('messageId'=>$messageId))}#message-reply-container\">Write a reply</a></p>",
				"recipients" => $recipients
			));
			$email->create();
		}
	
		$successMessage = "Reply successful.";
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