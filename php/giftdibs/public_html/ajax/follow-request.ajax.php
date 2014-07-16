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
	
	$signature 		= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$leaderId 		= $_POST["leaderId"];
	$followerId 	= $_POST["followerId"];
	$redirect 		= isset($_POST["redirect"]) ? $_POST["redirect"] : $app->config("page","home");
	
	$user = new User($db);
	$leader = $user->set("userId", $leaderId)->find(1);
	$follower = $user->set("userId", $followerId)->find(1);
	
	if (!$leader || !$follower) {
		$errors = "The appropriate users where not found.";
	}
	
	else if ($leaderId === $followerId) {
		$errors = "You can't follow yourself.";
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($followerId) != $signature) {
		$errors .= "The signature was not valid.<br>";
	}
	
	if ($errors) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
		$link = $app->config("page", "follow-me", array("leaderId" => $leader->get("userId")));
		
		// email
		if ($follower->acceptsEmailFor("follow-request")) {
			$email = new Email($app, array(
				"title" => "{$leader->fullName()} wants you to {$leader->pronoun('him')} on {$app->config('app','name')}.",
				"subject" => "Follow me on {$app->config('app','name')}?",
				"body" => "<p><a href=\"{$link}\">Follow {$leader->get('firstName')} now</a></p>",
				"recipients" => array(
					$follower->fullName() => $follower->get("emailAddress")
				)
			));
			$email->create();
		}
		
		// account notification
		$notification = new Notification($db);
		$notificationType = new NotificationType($db);
		$notificationType = $notificationType->set("slug", "follow-request")->find(1);
		if ($notificationType) {
			$notification->set(array(
				"notificationTypeId" => $notificationType->get("notificationTypeId"),
				"userId" => $follower->get("userId"),
				"followerId" => $leader->get("userId"),
				"dateCreated" => $dateCreated
			))->create();
		}
		
		$successMessage = "Your follow request was successfully sent.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"redirect" => $redirect,
			"message" => $successMessage
		));
	}
	$response->sendIt();
}
