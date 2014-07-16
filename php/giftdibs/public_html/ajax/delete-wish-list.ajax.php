<?php
require "../../resources/config.php";
require "../../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must be logged in to complete this action.");
	$session->setMessageType("error");
	$session->redirectTo($app->config("page","home"));
	die;
}

if ($_POST) {

	$wishListId = $_POST["wishListId"];
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	
	$validator = new Validator();
	$wishList = new WishList($db);
	$validator->run();
	
	if (!$wishList = $wishList->set("wishListId", $wishListId)->find(1)) {
		$validator->addError("The wish list doesn't exist.");
	}
	
	if ($wishList->get("userId") != $session->getUserId()) {
		$validator->addError("You do not have permission to delete this wish list.");
	}
	
	if (!isset($signature) || $me->createSignature($wishList->get("wishListId")) != $signature) {
		$validator->addError("The signature was not valid.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		
		$gift = new Gift($db);
		$package = $gift->set("wishListId", $wishListId)->find();
		if ($package) {
			foreach ($package["gifts"] as $gift) {
				$gift->delete();
			}
		}
		$wishList->delete();
		
		$successMessage = "Wish list successfully deleted.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config("page", "profile", array("userId" => $session->getUserId()))
		));
	}
	$response->sendIt();
}
