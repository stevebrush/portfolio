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
	$giftId = (isset($_POST["giftId"])) ? $_POST["giftId"] : null;
	$content = (isset($_POST["content"])) ? $_POST["content"] : null;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "profile");
	
	$comment = new Comment($db);
	$comment->getInputs();
	
	$validator = new Validator();
	$validator->addInput($comment->getInput("content"), $content);
	$validator->run();
	
	// Gift exists?
	$gift = new Gift($db);
	if (! $gift = $gift->set("giftId", $giftId)->find(1)) {
		$validator->addError("That gift doesn't exist.");
	}
	// Signature
	else if (!isset($signature) || $me->createSignature($giftId) !== $signature) {
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
		
		$comment = $comment->set(array(
			"userId" => $userId,
			"giftId" => $giftId,
			"content" => $content,
			"dateCreated" => $dateCreated,
			"timestamp" => $dateCreated
		))->create();
		
		$user = new User($db);
		$giftOwner = $user->set("userId", $gift->get("userId"))->find(1);
		
		// In-app notifications for gift owner
		if (!$me->isAlso($giftOwner)) {
			
			$notification = new Notification($db);
			$nt = new NotificationType($db);
			if ($notificationType = $nt->set("slug", "gift-comment")->find(1)) {
				$notification->set(array(
					"notificationTypeId" => $notificationType->get("notificationTypeId"),
					"userId" => $giftOwner->get("userId"),
					"giftId" => $giftId,
					"followerId" => $userId,
					"dateCreated" => $dateCreated
				))->create();
			}
			
			// Emails...
			if ($giftOwner->acceptsEmailFor("gift-comment")) {
				$email = new Email($app, array(
					"title" => "{$me->fullName()} commented on your gift, {$gift->get('name')}",
					"subject" => "{$me->fullName()} commented on your gift, {$gift->get('name')}",
					"body" => "<p>&ldquo;{$content}&rdquo;</p><p><a href=\"" . $app->config("page", "gift", array("giftId" => $giftId)) . "#tab-comments\">View comment thread &gt;</a></p>",
					"recipients" => array(
						$giftOwner->fullName() => $giftOwner->get("emailAddress")
					)
				));
				$email->create();
			}
		}
		
		// Send notifications to everyone on the thread	
		$nt = new NotificationType($db);
		if ($notificationType = $nt->set("slug", "gift-comment-also")->find(1)) {
			// Send to other members
			$sql = "SELECT * FROM User, Comment WHERE Comment.giftId = {$giftId} AND User.userId != {$giftOwner->get('userId')} AND User.userId != {$userId} AND Comment.userId = User.userId";
			if ($users = $user->query($sql)) {
				$recipients = array();
				foreach ($users as $u) {
					$notification = new Notification($db);
					$notification->set(array(
						"notificationTypeId" => $notificationType->get("notificationTypeId"),
						"userId" => $u->get("userId"),
						"giftId" => $giftId,
						"followerId" => $userId,
						"dateCreated" => $dateCreated
					))->create();
					
					// Emails...
					if ($u->acceptsEmailFor("gift-comment-also")) {
						$recipients[$u->fullName()] = $u->get("emailAddress");
					}
				}
				if (count($recipients)) {
					$email = new Email($app, array(
						"title" => "{$me->fullName()} also commented on {$giftOwner->firstNamePossessive()} gift, {$gift->get('name')}",
						"subject" => "{$me->fullName()} also commented on {$giftOwner->firstNamePossessive()} gift, {$gift->get('name')}",
						"body" => "<p>&ldquo;{$content}&rdquo;</p><p><a href=\"" . $app->config("page", "gift", array("giftId" => $giftId)) . "#tab-comments\">View comment thread &gt;</a></p>",
						"recipients" => $recipients
					));
					$email->create();
				}
			}
		}
		$successMessage = "Comment successful.";
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