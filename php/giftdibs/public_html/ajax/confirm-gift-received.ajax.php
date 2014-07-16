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

	/**
	 * Did the current user receive a gift that someone else marked as delivered?
	 **/
	
	$userDidReceive = isset($_POST['userDidReceive']) ? 1 : 0;
	$followerId = isset($_POST['followerId']) ? $_POST['followerId'] : null;
	$notificationId = isset($_POST['notificationId']) ? $_POST['notificationId'] : null;
	$dibId = isset($_POST['dibId']) ? $_POST['dibId'] : null;
	$giftId = isset($_POST['giftId']) ? $_POST['giftId'] : null;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","dibs");
	
	$follower = new User($db);
	$follower = $follower->set("userId", $followerId)->find(1);
	
	$dib = new Dib($db);
	$dib = $dib->set("dibId", $dibId)->find(1);
	
	$gift = new Gift($db);
	$gift = $gift->set(array(
		"giftId" => $giftId,
		"userId" => $me->get("userId")
	))->find(1);
	
	$validator = new Validator($app);
	$validator->run();
	
	// Gift and dib exists
	if (!$gift || !$dib) {
		$validator->addError("Either the gift or the dib does not exist.");
	} else if ($dib->get("giftId") !== $giftId) {
		$validator->addError("The dib does not belong to the gift.");
	}
	
	// Follower exists?
	if (!$follower) {
		$validator->addError("The user who dibbed this gift doesn't exist.");
	} else if ($me->get("userId") !== $gift->get("userId")) {
		$validator->addError("You don't own this gift, so don't have permission to modify it.");
	}
	
	// Notification exists?
	$notification = new Notification($db);
	$notification = $notification->set("notificationId", $notificationId)->find(1);
	if (!$notification) {
		$validator->addError("The notification ID was not specified.");
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($dibId) != $signature) {
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
		
		if ($userDidReceive) {
	
			// Make dib complete
			$dib->set("dibStatusId", "4")->update();
			
			// Mark gift as received
			if ($gift->get("isReceived") === "0") {
				$gift->set(array(
					"isReceived" => "1",
					"timestamp" => $dateCreated
				))->update();
			}
			
			// Send notification/email to dibber of the gift, 
			// informing them the gift owner confirmed.
			
			// in-app notification
			$newNotification = new Notification($db);
			$nt = new NotificationType($db);			
			if ($notificationType = $nt->set("slug", "gift-received-confirmed")->find(1)) {
				$newNotification->set(array(
					"notificationTypeId" => $notificationType->get("notificationTypeId"),
					"userId" => $followerId,
					"followerId" => $me->get("userId"),
					"giftId" => $giftId,
					"dateCreated" => $dateCreated
				))->create();
			}
			// email
			if ($follower->acceptsEmailFor("gift-received-confirmed")) {
				$email = new Email($app, array(
					"title" => "{$follower->get('firstName')} confirmed the delivery of {$follower->pronoun('his')} gift, {$gift->get('name')}",
					"subject" => "{$follower->get('firstName')} confirmed the delivery of {$follower->pronoun('his')} gift, {$gift->get('name')}",
					"body" => "<p>This message was sent to let you know that <a href=\"{$app->config('page','profile',array('userId'=>$followerId))}\">{$follower->fullName()}</a> confirmed the delivery of {$follower->pronoun('his')} gift, <a href=\"{$app->config('page','gift',array('giftId'=>$giftId))}\">{$gift->get('name')}</a>.</p><p><a href=\"{$app->config('page','dibs-complete')}\">See all gifts you've delivered &gt;</a></p>",
					"recipients" => array($follower->fullName() => $follower->get("emailAddress"))
				));
				$email->create();
			}
			
			// send message to gift owner, telling them who dibbed their gift
			$newNotification = new Notification($db);
			$nt = new NotificationType($db);			
			if ($notificationType = $nt->set("slug", "gift-dibbed-confirmed")->find(1)) {
				$newNotification->set(array(
					"notificationTypeId" => $notificationType->get("notificationTypeId"),
					"userId" => $me->get("userId"),
					"followerId" => $followerId,
					"giftId" => $giftId,
					"dateCreated" => $dateCreated
				))->create();
			}
			if ($me->acceptsEmailFor("gift-dibbed-confirmed")) {
				$email = new Email($app, array(
					"title" => "{$follower->get('firstName')} dibbed your gift, {$gift->get('name')}",
					"subject" => "{$follower->get('firstName')} dibbed your gift, {$gift->get('name')}",
					"body" => "<p>Thanks to {$follower->fullName()}, you received exactly what you wanted: <strong>{$gift->get('name')}</strong>.</p><p><a href=\"" . $app->config("page", "message", array("userId" => $followerId, "content" => "{$follower->get('firstName')},\nThanks for getting me exactly what I wanted: {$gift->get('name')}.")) . "\">Send a thank you note</a></p>",
					"recipients" => array($me->fullName() => $me->get("emailAddress"))
				));
				$email->create();
			}
			
			$successMessage = "Gift delivery confirmed!";
		
		} else {
		
			// Owner claims that dibber did not deliver the gift.
			// Change dib status to "Reserved"
			// Inform dibber that owner didn't receive the gift.
			
			$dib->set("dibStatusId", "1")->update();
			$successMessage = "Gift delivery denied. {$follower->get('firstName')} will be informed of the confusion.";
			
			// ...
			
		}
	
		// Delete notification
		$notification->delete();
		
		// Send success notification
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