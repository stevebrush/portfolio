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

	$dibId = (isset($_POST["dibId"])) ? $_POST["dibId"] : 0;
	$signature = (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page", "dibs");
	
	$sql = "SELECT * FROM Dib AS d, DibStatus AS ds WHERE d.dibId = {$dibId} AND d.dibStatusId = ds.dibStatusId AND ds.slug != 'pending' AND ds.slug != 'complete'";
	
	$dib = new Dib($db);
	$dib = $dib->query($sql);
	
	$validator = new Validator();
	$validator->run();
	
	if (!$dib) {
		$validator->addError("The dib doesn't exist, or cannot be modified at this time.");
		
	} else {
		
		// unpack the dib array
		$dib = array_shift($dib);
		
		// User owns dib?
		if ($dib->get("userId") !== $session->getUserId()) {
			$validator->addError("You do not have permission to delete this dib.");
		}
		
		// Signature
		if (!isset($signature) || $me->createSignature($dibId) !== $signature) {
			$validator->addError("The signature was not valid.");
		}
		
	}
	
	if (!$errors = $validator->getErrors()) {
		
		$dib->delete();
		
		$successMessage = "Dib successfully removed.";
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
