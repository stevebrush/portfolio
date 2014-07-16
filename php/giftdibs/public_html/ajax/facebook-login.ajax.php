<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($_POST) {
	
	$redirect = (isset($_POST["redirect"])) ? $_POST["redirect"] : $app->config("page","home");
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput( $me->getInput("emailAddress"), $_POST["facebookEmailAddress"]);
	$validator->addInput( $me->getInput("facebookUserId"), $_POST["facebookUserId"]);
	$validator->addInput( $me->getInput("facebookAccessToken"), $_POST["facebookAccessToken"]);
	$validator->run();
	
	$date = new DateTime(); 
	$dateLoggedIn = $date->format($app->config("date", "format"));
	
	// Attempt to find a user that shares the Facebook User ID
	$user = new User($db);
	$foundUser = $user->set("facebookUserId", $validator->getInputValue("facebookUserId"))->find(1);
	
	// If a user wasn't found, try using the Facebook Email Address instead
	if (!$foundUser) {
		$foundUser = $user->set("emailAddress", $validator->getInputValue("emailAddress"))->find(1);
	}
	
	// A user was found that either had the Facebook ID or Email Address.
	// Let's update the user with the Facebook information.
	if ($foundUser) {
		$foundUser->set(array(
			"facebookUserId" => $validator->getInputValue("facebookUserId"),
			"facebookAccessToken" => $validator->getInputValue("facebookAccessToken"),
			"dateLastLoggedIn" => $dateLoggedIn,
			"timestamp" => $dateLoggedIn
		))->update();
		
		$rmUserId = $foundUser->get("userId");
		
		$session->login($foundUser);
		
		$rp = new ResetPasswordToken($db);
		$rp->set("userId", $rmUserId)->delete();
		
		$rm = new RememberMe($db);
		$rm->set("userId", $rmUserId);
		$cookieValue = $rm->generateCookieValue();
		$rm->setCookieValue($cookieValue);
		$rm->create();
		
		$cookie = new Cookie(array(
			"name" => "rememberMe",
			"value" => $cookieValue
		));
		$cookie->create();
		
		$successMessage = "Welcome back, ".$foundUser->get("firstName")."!";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $redirect
		));
		
	} else {
	
		// No users exist with the Facebook ID or Email Address provided.
		$errorMessage = "<p><strong>We couldn't find a {$app->config('app','name')} account linked to your Facebook login.</strong></p>".
			"<p>If you're a current {$app->config('app','name')} member, this can occur when your {$app->config('app','name')} email address <em>doesn't match</em> your Facebook email address. ".
			"Make sure that your {$app->config('app','name')} email address matches your Facebook email address, and then try again.</p>".
			"<p>If you need further assistance, please don't hesitate to <a href=\"{$app->config('page','contact')}\">contact us</a>.</p>";
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errorMessage
		));
	}
	$response->sendIt();
}
