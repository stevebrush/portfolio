<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($session->isLoggedIn()) {
	$response = new Response($app, array(
		"errorMessage" => "You are already logged in.",
		"errorRedirect" => $app->config("page", "home")
	));
	$response->sendIt();
	die;
}

if ($_POST) {

	$redirect = (isset($_POST['redirect'])) ? $_POST['redirect'] : null;
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput( $me->getInput("emailAddress"), $_POST["emailAddress"]);
	$validator->addInput( $me->getInput("password"), $_POST["password"]);
	$validator->run();
	
	if (!$me->emailExists( $validator->getInputValue("emailAddress") )) {
		$validator->addError("Your email address wasn't found in our records, or was formatted incorrectly.");
	}
	
	else if (!$errors = $validator->getErrors()) {
		$foundUser = $me->authenticate($validator->getInputValue("emailAddress"), $validator->getInputValue("password"));
		if (!$foundUser) {
			$validator->addError("The email address and password combination wasn't found in our records.<br><a href=\"{$app->config('page','reset-password')}\">Did you forget your password?</a>");
		}
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		$date = new DateTime(); 
		$dateLoggedIn = $date->format($app->config('date','format'));
		
		$loginUser = new User($db);
		$loginUser = $loginUser->set("userId", $foundUser->get("userId"))->find(1);
		$loginUser->set(array(
			"dateLastLoggedIn" => $dateLoggedIn,
			"timestamp" => $dateLoggedIn
		))->update();
		
		$session->login($loginUser);
		
		$rp = new ResetPasswordToken($db);
		$rp->set("userId", $loginUser->get("userId"))->delete();
		
		$rm = new RememberMe($db);
		$rm->set("userId", $loginUser->get("userId"));
		$cookieValue = $rm->generateCookieValue();
		$rm->setCookieValue($cookieValue);
		$rm->create();
		
		$cookie = new Cookie(array(
			"name" => "rememberMe",
			"value" => $cookieValue
		));
		$cookie->create();
		
		$redirect = !is_null($redirect) ? urldecode($redirect) : $app->config('page','home');
		$successMessage = "Welcome back, ".$loginUser->get("firstName")."!";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"redirect" => $redirect,
			"message" => $successMessage
		));
	}
	$response->sendIt();
}