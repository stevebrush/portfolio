<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($_POST) {
	
	$validator = new Validator();
	$feedback = new Feedback($db);
	$inputs = $feedback->getInputs();
	
	$nickname = $_POST["nickname"];
	$requestFollowUp = (isset($_POST["requestFollowUp"])) ? true : false;
	
	$validator->addInput( $inputs["feedbackReasonId"], $_POST["feedbackReasonId"]);
	$validator->addInput( $inputs["message"], $_POST["message"]);
	$validator->addInput( $inputs["referrer"], $_POST["referrer"]);
	
	if ($nickname != "") {
		die("You're a spam bot.");
	}
	
	if ($requestFollowUp) {
		$validator->addInput( $inputs["emailAddress"], $_POST["emailAddress"]);
	}
	
	$validator->run();
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
	
		$reasonId = $validator->getInputValue("feedbackReasonId");
		if ($requestFollowUp) {
			$emailAddress = $validator->getInputValue("emailAddress");
		} else {
			$emailAddress = "";
		}
		$message = $validator->getInputValue("message");
		$referrer = $validator->getInputValue("referrer");
	
		/* Create Feedback */
		$feedback->set(array(
			"feedbackReasonId" => $reasonId,
			"requestFollowUp" => $requestFollowUp,
			"emailAddress" => $emailAddress,
			"message" => $message,
			"referrer" => $referrer,
			"dateCreated" => $dateCreated
		))->create();
		
		
		/* Get FeedbackReason Label */
		$fr = new FeedbackReason($db);
		$fr = $fr->set("feedbackReasonId", $reasonId)->find(1);
		$reasonLabel = $fr->get("label");
		
		
		/* Format Email */
		$emailBody = "<p><strong>Reason: </strong>".$reasonLabel."</p>";
		$emailBody .= "<p><strong>Message: </strong>".$message."</p>";
		$emailBody .= "<p><strong>Referrer: </strong>".$referrer."</p>";
		$emailBody .= "<p><strong>Request Follow-up: </strong>".$requestFollowUp."</p>";
		$emailBody .= "<p><strong>Reply To: </strong>".$emailAddress."</p>";
		
		$emailAddress = ($emailAddress == "") ? $app->config("email", "from-address") : $emailAddress;
		
		
		/* Send Email */
		$email = new Email($app, array(
			"title" => $reasonLabel,
			"subject" => $reasonLabel,
			"body" => $emailBody,
			"fromName" => "{$app->config('app','name')} User",
			"fromAddress" => $emailAddress,
			"recipients" => array(
				"{$app->config('app','support-name')}" => "{$app->config('app','support-email')}"
			)
		));
		
		
		if (!$email->create()) {
			$errors = "The email address you entered doesn't appear to be valid. Try entering a different address.";
			$response = new Response($app, array(
				"status" => "error",
				"message" => $errors
			));
			
		} else {
			$successMessage = ($requestFollowUp) ? "Your message has been sent. We very much appreciate your feedback, and will work diligently to get back to you within 5 business days." : "Your message has been sent. We very much value your input.";
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $app->config("page","contact")
			));
		}
	}
	$response->sendIt();
}