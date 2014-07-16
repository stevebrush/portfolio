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
	$signature = (isset($_POST["signature"])) ? (string)$_POST["signature"] : null;
	$choices = isset($_POST["emailAlerts"]) ? $_POST["emailAlerts"] : false;

	// Signature
	if (!isset($signature) || $me->createSignature("email-preferences") != $signature) {
		$errors .= "The signature was not valid.<br>";
	}

	if ($errors) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {

		// Delete all rows to prevent duplicates
		$nt_u = new NotificationType_User($db);
		$nt_u->set("userId", $me->get("userId"))->delete();
		
		if ($choices) {
			foreach ($choices as $choice) {
				$nt_u = new NotificationType_User($db);
				$nt_u->set(array(
					"userId" => $me->get("userId"),
					"notificationTypeId" => $choice
				))->create();
			}
		}
	
		$successMessage = "Settings saved.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config('page','email-preferences')
		));
	}
	$response->sendIt();
}
