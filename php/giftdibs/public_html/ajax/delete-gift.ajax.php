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

	$errors = "";
	
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$giftId = (int)$_POST["giftId"];
	
	$gift = new Gift($db);
	
	if (!$gift = $gift->set("giftId",$giftId)->find(1)) {
		$errors = "The gift doesn't exist.";
	}
	
	else if ($gift->get("userId") != $session->getUserId()) {
		$errors = "You do not have permission to delete this gift.";
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($giftId) != $signature) {
		$errors = "The signature was not valid.";
	}
	
	if (!$errors) {
		
		$gift->delete();
		
		$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","wish-list",array("wishListId"=>$gift->get("wishListId")));
		
		$successMessage = "Gift successfully deleted.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $redirect
		));
		
	} else {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
	}
	$response->sendIt();
}
