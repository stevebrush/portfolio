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
	 * Did the current user deliver a gift that someone else marked as received?
	 **/
	
	$userDidDeliver = isset($_POST['userDidDeliver']) ? 1 : 0;
	$followerId = isset($_POST['followerId']) ? $_POST['followerId'] : null;
	$notificationId = isset($_POST['notificationId']) ? $_POST['notificationId'] : null;
	$dibId = isset($_POST['dibId']) ? $_POST['dibId'] : null;
	$giftId = isset($_POST['giftId']) ? $_POST['giftId'] : null;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","dibs");
	
	$follower = new User($db);
	$follower = $follower->set("userId", $followerId)->find(1);
	
	$dib = new Dib($db);
	$dib = $dib->set("dibId",$dibId)->find(1);
	
	$gift = new Gift($db);
	$gift = $gift->set(array(
		"giftId" => $giftId,
		"userId" => $followerId
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
		$validator->addError("The gift owner doesn't exist.");
	} else if ($followerId !== $gift->get("userId")) {
		$validator->addError("The gift does not belong to the person you gave it to.");
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
		
		if ($userDidDeliver) {
	
			// Make dib complete
			$dib->set("dibStatusId", "4")->update();
			
			// Make sure gift is marked as received
			if ($gift->get("isReceived") === "0") {
				$gift->set(array(
					"isReceived" => "1",
					"timestamp" => $dateCreated
				))->update();
				
				// Update the delivery date
				$dib->set(array(
					"dateDelivered" => $dateCreated
				))->update();
			}
			
			// Send notification/email to owner of gift, informing them who dibbed it.
			
			// in-app notification
			$newNotification = new Notification($db);
			$nt = new NotificationType($db);			
			if ($notificationType = $nt->set("slug", "gift-dibbed-confirmed")->find(1)) {
				$newNotification->set(array(
					"notificationTypeId" => $notificationType->get("notificationTypeId"),
					"userId" => $followerId,
					"followerId" => $me->get("userId"),
					"giftId" => $giftId,
					"dateCreated" => $dateCreated
				))->create();
			}
			
			if ($follower->acceptsEmailFor("gift-dibbed-confirmed")) {
				$email = new Email($app, array(
					"title" => "{$me->get('firstName')} dibbed your gift, {$gift->get('name')}",
					"subject" => "{$me->get('firstName')} dibbed your gift, {$gift->get('name')}",
					"body" => "<p>Thanks to {$me->fullName()}, you received exactly what you wanted: <strong>{$gift->get('name')}</strong>.</p><p><a href=\"" . $app->config("page", "message", array("userId" => $session->getUserId(), "content" => "{$me->get('firstName')},\nThanks for getting me exactly what I wanted: {$gift->get('name')}.")) . "\">Send a thank you note</a></p>",
					"recipients" => array($follower->fullName() => $follower->get("emailAddress"))
				));
				$email->create();
			}
			
			$successMessage = "Dib complete!";
		
		} else {
		
			// Owner claims that they did not deliver this gift.
			// Leave dib as it is (gift will be received, but without a known dibber).
			$successMessage = "Gift delivery denied.";
			
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