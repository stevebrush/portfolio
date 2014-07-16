<?php
require "../../resources/config.php";
require "../../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must be logged in to complete this action.");
	$session->setMessageType("error");
	$session->redirectTo($app->config("page", "home"));
	die;
}

if ($_POST) {

	$errors 	= "";
	$signature 	= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect 	= (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","profile");
	$giftId 	= $_POST["giftId"];
	
	$gift = new Gift($db);
	
	if (!$gift = $gift->set("giftId",$giftId)->find(1)) {
		$errors = "The gift doesn't exist.";
	}
	
	else if ($gift->get("userId") != $session->getUserId()) {
		$errors = "You do not have permission to modify this gift.";
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($giftId) !== $signature) {
		$errors = "The signature was not valid.";
	}
	
	if ($errors) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateUpdated = $date->format($app->config("date", "format"));
		
		$gift->set(array(
			"isReceived" => "1",
			"timestamp" => $dateUpdated
		))->update();
		
		// send notification to dibber if they exists
		$dib = new Dib($db);
		$dibs = $dib->set("giftId", $giftId)->find();
		if ($dibs) {
			$recipients = array();
			foreach ($dibs as $dib) {
			
				$dib->set(array(
					"dateDelivered" => $dateUpdated
				))->update();
				
				$notification = new Notification($db);
				$nt = new NotificationType($db);
				if ($notificationType = $nt->set("slug", "gift-received")->find(1)) {
					$dibber = new User($db);
					$dibber = $dibber->set("userId", $dib->get("userId"))->find(1);
					$notification->set(array(
						"notificationTypeId" => $notificationType->get("notificationTypeId"),
						"userId" => $dibber->get("userId"),
						"giftId" => $giftId,
						"followerId" => $me->get("userId"),
						"dateCreated" => $dateUpdated
					))->create();
				}
				// send email notification
				if ($dibber->acceptsEmailFor("gift-received")) {
					$recipients[$dibber->fullName()] = $dibber->get("emailAddress");
				}
			}
			if (count($recipients)) {
				$email = new Email($app, array(
					"title" => "Did you dib the gift \"{$gift->get('name')}\"?",
					"subject" => "Did you dib the gift \"{$gift->get('name')}\"?",
					"body" => "<p><a href=\"{$app->config('page','profile',array('userId'=>$me->get('userId')))}\">{$me->fullName()}</a> has marked {$me->pronoun('his')} gift <strong><a href=\"{$app->config('page','gift',array('giftId'=>$giftId))}\">{$gift->get('name')}</a></strong> as received.</p><p><a href=\"\">Yes, I delivered this gift to {$me->get('firstName')}.</a>&nbsp;<a href=\"{}\">No, I didn't deliver this gift.</a></p>",
					"recipients" => $recipients
				));
				$email->create();
			}
		}
		
		$successMessage = "<strong>Your gift was successfully marked as received.</strong><br>If someone dibbed your gift, they have been notified to confirm.";
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
