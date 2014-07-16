<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($_POST) {
	
	$signature = $_POST["signature"];
	$token = (isset($_POST["token"])) ? $_POST["token"] : null;
	$userId = $_POST["userId"];
	$password = (isset($_POST["password"])) ? $_POST["password"] : null;
	
	$validator = new Validator();
	$me->getInputs();
	
	if (isset($password)) {
		$validator->addInput( $me->getInput("password"), $password);
	}
	$inputPasswordNew = $me->getInput("password");
	$inputPasswordNew["field"]["name"] = "passwordNew";
	$inputPasswordNew["field"]["label"] = "New password";
	$validator->addInput( $inputPasswordNew, $_POST["passwordNew"]);
	
	$inputPasswordNewAgain = $me->getInput("password");
	$inputPasswordNewAgain["field"]["name"] = "passwordNewAgain";
	$inputPasswordNewAgain["field"]["label"] = "New password again";
	$validator->addInput( $inputPasswordNewAgain, $_POST["passwordNewAgain"]);
	
	$validator->run();
	
	$me = new User($db);
	$me = $me->set("userId", $userId)->find(1);
	
	if (!$me) {
		$validator->addError("User not found.");
		
	} else {
	
		if (isset($password) && !isEmpty($password)) {
			$foundUser = $me->authenticate($me->get("emailAddress"), $validator->getInputValue("password"));
		} else if (isset($token)) {
			$rp = new ResetPasswordToken($db);
			$foundUser = $rp->findByToken($token);
		} else {
			$foundUser = false;
		}
		
		if (!$foundUser) {
			$validator->addError("The password you entered did not match our records. You can <a href=\"{$app->config('page','reset-password')}\">reset your password</a> instead.");
		}
		else if ($validator->getInputValue("passwordNew") !== $validator->getInputValue("passwordNewAgain")) {
			$validator->addError("Your new passwords do not match.");
		}
		else if (!isset($signature)) {
			$validator->addError("A signature must be provided.");
		}
		else if ($me->createSignature("change-password") != $signature) {
			$validator->addError("The signature was not valid.");
		}
	}
	
	if (!$errors = $validator->getErrors()) {
	
		$date = new DateTime(); 
		$dateUpdated = $date->format($app->config("date","format"));
	
		$hasher = new PasswordHash(8, false);
		$encryptedPasswordNew = $hasher->HashPassword( $validator->getInputValue("passwordNew") );
	
		$me->set(array(
			"password" => $encryptedPasswordNew,
			"timestamp" => $dateUpdated
		))->update();
		
		
		// Delete resetPasswordToken
		$rp = new ResetPasswordToken($db);
		$rp->set("userId", $me->get("userId"))->find();
		$rp->delete();
		
		
		$rm = new RememberMe($db);
		$rm->set("userId", $userId);
		$cookieValue = $rm->generateCookieValue();
		$rm->setCookieValue($cookieValue);
		$rm->create();
		
		$cookie = new Cookie(array(
			"name" => "rememberMe",
			"value" => $cookieValue
		));
		$cookie->create();
		
		$email = new Email($app, array(
			"title" => "You changed your {$app->config('app','name')} password",
			"subject" => "You changed your {$app->config('app','name')} password",
			"body" => "<p>Hello {$me->get('firstName')},</p><p>This email is to inform you that your <a href=\"{$app->config('page','home')}\">{$app->config('app','name')}</a> password was changed. If you did not perform this action on your account, please <a href=\"{$app->config('page','contact')}\">let us know about it</a>.</p>",
			"recipients" => array(
				$me->fullName() => $me->get("emailAddress")
			)
		));
		$email->create();
		
		$successMessage = "Your password was successfully changed.";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config("page","profile")
		));
		
	} else {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	}
	$response->sendIt();
}
