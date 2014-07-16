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
	
	$signature 	= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$leaderId 	= $_POST["leaderId"];
	$followerId = $_POST["followerId"];
	$redirect 	= (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","home");
	
	$user = new User($db);
	$leader = $user->set("userId", $leaderId)->find(1);
	$follower = $user->set("userId", $followerId)->find(1);
	
	if (!$leader || !$follower) {
		$errors = "The appropriate users where not found.";
	}
	
	if ($leaderId === $followerId) {
		$errors = "You can't follow yourself.";
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($leaderId) != $signature) {
		$errors .= "The signature was not valid.<br>";
	}
	
	if ($leader->hasBlocked($me)) {
		$errors .= "You cannot follow {$leader->get('firstName')} because {$leader->pronoun('he')} has blocked you.<br>";
	}
	
	if ($errors) {
		$session->setMessage($errors);
		$session->setMessageType("error");
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors,
			"redirect" => $redirect
		));
		
	} else {
		
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config('date','format'));
		
		$follow = new Follow($db);
		$follow->set(array(
			"userId" => $follower->get("userId"),
			"leaderId" => $leader->get("userId")
		));
		
		if ($follower->isFollowing($leader)) {
		
			$buttonText = "Follow";
			$follow->delete(); // unfollow
			$successMessage = "You are no longer following {$leader->get('firstName')}.";
		
		} else {
		
			$buttonText = "Unfollow";
			$follow->set(array(
				"dateCreated" => $dateCreated,
				"timestamp" => $dateCreated
			));
			$follow->create(); // follow
			$successMessage = "You are now following {$leader->get('firstName')}. <a href=\"{$app->config('page','profile', array('userId'=>$leader->get('userId')))}\">View {$leader->pronoun('his')} wish lists&nbsp;&rarr;</a>";
			
			// in-app notification
			$notification = new Notification($db);
			$nt = new NotificationType($db);			
			if ($notificationType = $nt->set("slug", "new-follower")->find(1)) {
				$notification->set(array(
					"notificationTypeId" => $notificationType->get("notificationTypeId"),
					"userId" => $leader->get("userId"),
					"followerId" => $me->get("userId"),
					"dateCreated" => $dateCreated
				))->create();
			}
			
			// email notification
			if ($leader->acceptsEmailFor("new-follower")) {
				$email = new Email($app, array(
					"title" => "{$follower->fullName()} has started following you on {$app->config('app','name')}.",
					"subject" => "Someone is following you on {$app->config('app','name')}",
					"body" => "<p><a href=\"{$app->config('page','profile',array('userId'=>$follower->get('userId')))}\">View {$follower->firstNamePossessive()} profile&nbsp;&#155;</a></p><p>If this is a problem and you need assistance, please <a href=\"{$app->config('page','contact')}\">send us a message</a>.</p>",
					"recipients" => array(
						$leader->fullName() => $leader->get("emailAddress")
					)
				));
				$email->create();
			}
		}
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"redirect" => $redirect,
			"package" => array(
				"button" => array(
					"label" => $buttonText
				)
			)
		));
	}
	
	$response->sendIt();
}
