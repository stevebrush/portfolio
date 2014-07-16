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
	
	$signature 	= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$userId 	= $_POST["userId"];
	$blockedId = $_POST["blockedId"];
	$redirect 	= (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","home");
	
	$user = new User($db);
	$blocker = $user->set("userId", $userId)->find(1);
	$blockee = $user->set("userId", $blockedId)->find(1);
	
	if (!$blocker || !$blockee) {
		$errors = "The appropriate users where not found.";
	}
	
	if ($userId === $blockedId) {
		$errors = "You can't block yourself.";
	}
	
	// Signature
	if (!isset($signature) || $me->createSignature($blockedId) != $signature) {
		$errors .= "The signature was not valid.<br>";
	}
	
	if ($errors) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config('date','format'));
		
		// Remove follows
		$follow = new Follow($db);
		$follow->set(array(
			"userId" => $userId,
			"leaderId" => $blockedId
		))->delete();
		$follow->set(array(
			"userId" => $blockedId,
			"leaderId" => $userId
		))->delete();
		
		$u_b = new User_Blocked($db);
		$u_b->set(array(
			"userId" => $userId,
			"blockedId" => $blockedId
		));
		
		if ($blocker->hasBlocked($blockee)) {
			$u_b->delete(); // unblock
			$successMessage = "{$blockee->get('firstName')} has been successfully <em>un-</em>blocked. <a href=\"{$app->config('page','profile',array('userId'=>$blockee->get('userId')))}\">View {$blockee->pronoun('his')} profile&nbsp;&rarr;</a>";
		} else {
			$u_b->delete(); // prevent duplicates
			$u_b->set("dateCreated", $dateCreated)->create(); // block user
			$successMessage = "{$blockee->get('firstName')} has been successfully blocked. <a href=\"{$app->config('page','privacy-settings')}\">View blocked users...</a>";
		}
		
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"redirect" => $redirect
		));
	}
	
	$response->sendIt();
}
