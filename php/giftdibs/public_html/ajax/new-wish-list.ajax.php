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

	$userId 		= $session->getUserId();
	$wishListId 	= (isset($_POST["wishListId"])) ? $_POST["wishListId"] : null;
	$signature 		= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$isRegistry 	= (isset($_POST["typeOfList"])) ? true : false;
	$showAddress 	= (isset($_POST["showAddress"])) ? true : false;
	$dateOfEvent 	= (isset($_POST["dateOfEvent"]) && !isEmpty($_POST["dateOfEvent"])) ? $_POST["dateOfEvent"] : null;
	$userIds 		= (isset($_POST["userIds"])) ? $_POST["userIds"] : null;
	
	$wishList = new WishList($db);
	$wishList->getInputs();
	
	$validator = new Validator();
	$validator->addInput( $wishList->getInput("name"), $_POST["name"]);
	$validator->addInput( $wishList->getInput("description"), $_POST["description"]);
	$validator->addInput( $wishList->getInput("privacyId"), $_POST["privacyId"]);
	$validator->run();
	
	$privacyId = $validator->getInputValue("privacyId");
	
	if (isset($dateOfEvent)) {
		$strDate = strtotime($dateOfEvent);
		if (!is_numeric($strDate) || $strDate == 0 && !isEmpty($strDate)) {
			$validator->addError("Please format your date like this: <em>mm/dd/yyyy</em>");
		}
	}
	
	// Signature
	if (!isset($signature)) {
		$validator->addError("A signature must be provided.");
	} 
	else if (isset($wishListId)) {
		if ($me->createSignature($wishListId) != $signature) {
			$validator->addError("The signature was not valid.");
		}
	} 
	else {
		if ($me->createSignature("new-wish-list") != $signature) {
			$validator->addError("The signature was not valid.");
		}
	}
	
	// Custom privacy, but no users selected
	if ($privacyId == 4 && !count($userIds)) {
		$validator->addError("Under Privacy, you selected \"Custom\" but didn't select anyone.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));

	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
		$successMessage = "";
	
		// clear date of event if not a registry
		if ($isRegistry) {
			if (isset($dateOfEvent)) {
				$doe = new DateTime($dateOfEvent);
				$dateOfEvent = $doe->format($app->config("date","format"));
			} else {
				$dateOfEvent = null;
			}
		} else {
			$dateOfEvent = null;
		}
		
		$wishList = new WishList($db);
		
		
		if (isset($wishListId)) {
			$wishList = $wishList->set("wishListId", $wishListId)->find(1);
			$wishList->set(array(
				"wishListId" => $wishListId,
				"privacyId" => $privacyId,
				"name" => $validator->getInputValue("name"),
				"description" => $validator->getInputValue("description"),
				"isRegistry" => $isRegistry,
				"dateOfEvent" => $dateOfEvent,
				"showAddress" =>$showAddress,
				"timestamp" => $dateCreated
			));
			$wishList->update();
			$successMessage = "Wish list updated.";
			
		} else {
			$wishList->set(array(
				"userId" => $userId,
				"wishListId" => $wishListId,
				"privacyId" => $privacyId,
				"name" => $validator->getInputValue("name"),
				"description" => $validator->getInputValue("description"),
				"isRegistry" => $isRegistry,
				"showAddress" =>$showAddress,
				"dateOfEvent" => $dateOfEvent,
				"dateCreated" => $dateCreated,
				"timestamp" => $dateCreated
			));
			$wishList = $wishList->create();
			$wishListId = $wishList->get("wishListId");
			$successMessage = "Wish list created.";
		}
		
		// Delete all WishList_User
		$wl_u = new WishList_User($db);
		$wl_u->set("wishListId", $wishListId)->delete();
		
		// Add Users to WishList_User if Privacy Set to Followers Only
		if ($privacyId == 4) {
			for ($i = 0, $len = count($userIds); $i < $len; $i++) {
				$wl_u->set("userId", $userIds[$i]);
				$wl_u->create();
			}
		}
		
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config("page", "wish-list", array("wishListId"=>$wishListId))
		));
		
	}
	$response->sendIt();
}