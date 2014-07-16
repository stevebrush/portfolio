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

	$dibId 			= (isset($_POST["dibId"])) ? $_POST["dibId"] : null;
	$dibStatusId 	= (isset($_POST["dibStatusId"])) ? $_POST["dibStatusId"] : "1";
	$signature 		= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect 		= (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","dibs");

	$validator = new Validator($app);
	$validator->run();
	
	$dib = new Dib($db);
	$dibStatus = new DibStatus($db);
	
	// Create the dib object
	if ($dib = $dib->set("dibId", $dibId)->find(1)) {
	
		// Only allow dib status to be edited if
		// it is being set to "reserved" or "purchased"
		$dibStatus = $dibStatus->set("dibStatusId", $dib->get("dibStatusId"))->find(1);
		$dibStatusSlug = $dibStatus->get("slug");
		if ($dibStatusSlug === "pending" || $dibStatusSlug === "delivered") {
			$validator->addError("You cannot modify the dib's status at this time.");
			
		} else {
		
			// Dib has been marked delivered, 
			// but we need to verify it with the gift owner first
			$dibStatus = new DibStatus($db);
			$dibStatus = $dibStatus->set("dibStatusId", $dibStatusId)->find(1);
			$dibStatusSlug = $dibStatus->get("slug");
			if ($dibStatusSlug === "delivered") {
				$dibStatusSlug = "pending";
			}
		}
		
		// User owns dib?
		if ($dib->get("userId") !== $me->get("userId")) {
			$validator->addError("You cannot modify someone else's dib.");
		}
		
	} else {
	
		// Dib doesn't exist
		$validator->addError("The dib you are attempting to modify does not exist.");
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($dibId) !== $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	// Errors?
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
		
		// Notification to gift owner
		if ($dibStatusSlug === "pending") {
			
			// Get the ID based on the slug
			$dibStatus = new DibStatus($db);
			$dibStatus = $dibStatus->set("slug", $dibStatusSlug)->find(1);
			
			// Update the dib
			$dib->set(array(
				"dibStatusId" => $dibStatus->get("dibStatusId"),
				"timestamp" => $dateCreated,
				"dateDelivered" => $dateCreated
			))->update();
			
			// Send notification to gift owner
			$notification = new Notification($db);
			$nt = new NotificationType($db);
			if ($notificationType = $nt->set("slug", "gift-dibbed")->find(1)) {
				$gift = new Gift($db);
				if ($gift = $gift->set("giftId", $dib->get("giftId"))->find(1)) {
					$giftOwner = new User($db);
					if ($giftOwner = $giftOwner->set("userId", $gift->get("userId"))->find(1)) {
						
						// In-app
						$notification->set(array(
							"notificationTypeId" => $notificationType->get("notificationTypeId"),
							"userId" => $giftOwner->get("userId"),
							"giftId" => $gift->get("giftId"),
							"dibId" => $dibId,
							"followerId" => $me->get("userId"),
							"dateCreated" => $dateUpdated
						))->create();
						
						// Email
						if ($giftOwner->acceptsEmailFor("gift-dibbed")) {
							$email = new Email($app, array(
								"title" => "Dib confirmation",
								"subject" => "Did {$me->fullName()} deliver your gift \"{$gift->get('name')}\"?",
								"body" => "<p><a href=\"{$app->config('page','profile',array('userId'=>$me->get('userId')))}\">{$me->fullName()}</a> has marked your gift <strong><a href=\"{$app->config('page','gift',array('giftId'=>$giftId))}\">{$gift->get('name')}</a></strong> as delivered.</p><p><a href=\"\">Yes, {$me->get('firstName')} delivered this gift to me.</a>&nbsp;<a href=\"{}\">No, {$me->pronoun('he')} didn't deliver this gift.</a></p>",
								"recipients" => array(
									$giftOwner->fullName() => $giftOwner->get("emailAddress")
								)
							));
							$email->create();
						}
					}
				}
			}
			
		} else {
			
			// If it's not a delivery confirmation, 
			// just update the dib blindly
			$dib->set(array(
				"dibStatusId" => $dibStatusId,
				"timestamp" => $dateCreated
			))->update();
		}
		
		$successMessage = "Dib updated.";
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
