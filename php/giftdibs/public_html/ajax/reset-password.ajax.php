<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($_POST) {
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput($me->getInput("emailAddress"), $_POST["emailAddress"]);
	$validator->run();
	
	if (!$me->emailExists($validator->getInputValue("emailAddress"))) {
		$validator->addError("The email address you entered does not exist in our records.");
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
		
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
		
		$user = new User($db);
		$foundUser = $user->set("emailAddress", $validator->getInputValue("emailAddress"))->find(1);
		
		/* Create Reset Password Token */
		$rp = new ResetPasswordToken($db);
		$rp->set("userId", $foundUser->get("userId"))->delete(); // remove duplicates
		
		$resetToken = $rp->generateToken();
		$rp->setToken($resetToken);
		$rp->create();
		
		$rpLink = $app->config("page", "change-password", array("token" => $resetToken));
		
		
		/* Send Email */
		$email = new Email($app, array(
			"title" => "Reset your {$app->config('app','name')} password",
			"subject" => "Reset your {$app->config('app','name')} password",
			"body" => "<p>Hello {$foundUser->get('firstName')},</p><p>This email was sent in response to your request to change your password.</p><p><a href=\"{$rpLink}\">Reset your password now.</a></p><p>If you did not perform this action on your account, please <a href=\"{$app->config('page','contact')}\">let us know about it</a>.</p>",
			"recipients" => array(
				$foundUser->fullName() => $foundUser->get("emailAddress")
			)
		));
		
		// Update user's timestamp
		$foundUser->set("timestamp", $dateCreated)->update();
		
		if ($email->create()) {
			$successMessage = "Success! A link to reset your password was sent to the email address specified.<br>If you cannot find the email in the next few minutes, check your Spam folder.";
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $app->config("page","reset-password")
			));
		} else {
			$response = new Response($app, array(
				"status" => "error",
				"message" => "The email address you entered does not allow inbound messages."
			));
		}
	}
	
	$response->sendIt();
}
