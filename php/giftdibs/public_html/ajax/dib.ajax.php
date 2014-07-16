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
	$giftId 		= (isset($_POST["giftId"])) ? $_POST["giftId"] : null;
	$signature 		= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$quantity 		= (isset($_POST["quantity"])) ? round($_POST["quantity"]) : 1;
	$isPrivate 		= (isset($_POST["isPrivate"])) ? 1 : 0;
	$redirect 		= (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","dibs");
	$dateProjected 	= (isset($_POST["dateProjected"])) ? $_POST["dateProjected"] : "";
	
	$validator = new Validator($app);
	$validator->run();
	
	// Editing a current dib
	if (isset($dibId)) {
	
		// Signature
		if (!isset($signature) || $me->createSignature($dibId) !== $signature) {
			$validator->addError("The signature was not valid.");
		}
		
		// Only allow edit if status is "reserved" or "purchased"
		$dib = new Dib($db);
		$dib = $dib->query("SELECT * FROM Dib AS d, DibStatus AS ds WHERE d.dibId = {$dibId} AND d.dibStatusId = ds.dibStatusId AND ds.slug != 'pending' AND ds.slug != 'delivered'");
		if (!$dib) {
			$validator->addError("You cannot modify the dib at this time.");
		}
	}
	
	$dateProjectedTime = strtotime($dateProjected);
	$oneYearLater = time() + 31536000; // number of seconds in 1 year
	
	// Check if date projected is after current date...
	if ($dateProjectedTime < time()) {
		$validator->addError("The delivery date must be after today.");
	}
	
	// Check if date projected is within one year of current date...
	if ($dateProjectedTime > $oneYearLater) {
		$validator->addError("The delivery date must be less than one year from today.");
	}
	
	// Check gift exists
	$gift = new Gift($db);
	if (!$gift = $gift->set("giftId", $giftId)->find(1)) {
		$validator->addError("The gift does not exist.");
	}
	
	// Get gift owner
	$owner = new User($db);
	if ($ownerId = $gift->get("userId")) {
		$owner = $owner->set("userId", $ownerId)->find(1);
		
		// Check if dibber is not the owner...
		if ($owner->isAlso($me)) {
			$validator->addError("You can't dib your own gifts.");
		}
		
		// Check if dibber is a follower of the owner...
		else if (!$me->isFollowing($owner)) {
			$validator->addError("You must be a follower of {$owner->get('firstName')} to dib {$owner->pronoun('his')} gifts.");
		}
		
		// Check if dibber has permission to dib...
		else if (!$gift->userCanView($me)) {
			$validator->addError("You do not have permission to dib that gift.");
		}
	}
	
	// Check if number of dibs is available; 
	// (be sure to account for any dibs that are currently held by the user, if editing)
	if ($ownerId) {
		
		// If editing a dib, don't account for its total
		if (isset($dibId)) {
		
			$dibsCommitted = 0;
			$dib = new Dib($db);
			$dibs = $dib->set("giftId", $giftId)->find();
			
			if ($dibs) {
				foreach ($dibs as $dib) {
					if ($dib->get("userId") == $session->getUserId()) {
						continue; 
					}
					$dibsCommitted += $dib->get("quantity");
				}
			}
			$dibsAvailable = $gift->get("quantity") - $dibsCommitted;
		} else {
			$dibsAvailable = $gift->dibs()->numAvailable();
		}
		if ($dibsAvailable < $quantity) {
			$validator->addError("The number of dibs available is less than the quantity you specified. Please select a quantity no greater than {$dibsAvailable}.");
		} else if ($quantity <= 0) {
			$validator->addError("The quantity must be greater than zero (0).");
		}
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
		
		$date = new DateTime($dateProjected);
		$dateTimeProjected = $date->format($app->config("date","format"));
		
		if (isset($dibId)) {
			// Update
			$dib = new Dib($db);
			if ($dib = $dib->set("dibId", $dibId)->find(1)) {
				$dib = $dib->set(array(
					"quantity" => $quantity,
					"dateProjected" => $dateTimeProjected,
					"isPrivate" => $isPrivate,
					"timestamp" => $dateCreated
				))->update();
			}
			$successMessage = "Dib updated.";
			
		} else {
		
			// Get the dibStatusId based on the slug
			$dibStatus = new DibStatus($db);
			$dibStatus = $dibStatus->set("slug", "reserved")->find(1);
			$dibStatusId = $dibStatus->get("dibStatusId");
		
			// Create
			$dib = new Dib($db);
			$dib = $dib->set(array(
				"userId" => $session->getUserId(),
				"giftId" => $giftId,
				"dibStatusId" => $dibStatusId,
				"quantity" => $quantity,
				"isPrivate" => $isPrivate,
				"dateProjected" => $dateTimeProjected,
				"dateCreated" => $dateCreated,
				"timestamp" => $dateCreated
			))->create();
			$successMessage = "You successfully dibbed this gift.";
		}
		
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