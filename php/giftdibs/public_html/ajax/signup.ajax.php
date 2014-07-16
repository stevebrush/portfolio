<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($session->isLoggedIn()) {
	$response = new Response($app, array(
		"errorMessage" => "You are already logged in.",
		"errorRedirect" => $app->config("page","home")
	));
	$response->sendIt();
	die;
}

if ($_POST) {

	$nickname = $_POST["nickname"];
	$leaderId = (isset($_POST["leaderId"])) ? $_POST["leaderId"] : null;
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput( $me->getInput("firstName"), $_POST["firstName"]);
	$validator->addInput( $me->getInput("lastName"), $_POST["lastName"]);
	$validator->addInput( $me->getInput("emailAddress"), $_POST["emailAddress"]);
	$validator->addInput( $me->getInput("password"), $_POST["password"]);
	$validator->addInput( $me->getInput("gender"), (isset($_POST["gender"])) ? $_POST["gender"] : null);
	$validator->addInput( $me->getInput("birthdayMonth"), $_POST["birthdayMonth"]);
	$validator->addInput( $me->getInput("birthdayDay"), $_POST["birthdayDay"]);
	$validator->addInput( $me->getInput("birthdayYear"), $_POST["birthdayYear"]);
	$validator->run();
	
	$formattedBirthday = $validator->getInputValue("birthdayYear") . "-" . $validator->getInputValue("birthdayMonth") . "-" . $validator->getInputValue("birthdayDay");
	
	if ($nickname != "") {
		die("You're a spam bot.");
	}
	
	if ($me->emailExists( $validator->getInputValue("emailAddress") )) {
		$validator->addError("Your email address already exists in our records. Wanna <a href=\"{$app->config('page','login')}\">login</a>?");
	}
	
	if (!$app->validateAge($formattedBirthday)) {
		$validator->addError("You must be at least {$app->getMinAgeAllowed()} years old to use {$app->config('app','name')}.");
	}

	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateCreated = $date->format( $app->config("date", "format") );
		
		$hasher = new PasswordHash(8, false);
		$encryptedPassword = $hasher->HashPassword( $validator->getInputValue("password") );
		
		$user = new User($db);
		$member = $user->set(array(
			"firstName" 		=> $validator->getInputValue("firstName"),
			"lastName" 			=> $validator->getInputValue("lastName"),
			"emailAddress" 		=> $validator->getInputValue("emailAddress"),
			"emailConfirmed"	=> 0,
			"password" 			=> $encryptedPassword,
			"gender" 			=> $validator->getInputValue("gender"),
			"birthday" 			=> $formattedBirthday,
			"birthdayPrivate" 	=> 0,
			"secret"			=> 0, // create this later
			"roleId" 			=> 2,
			"currencyId" 		=> 1,
			"dateCreated" 		=> $dateCreated,
			"dateLastLoggedIn" 	=> $dateCreated,
			"timestamp" 		=> $dateCreated
		))->create();
		$newUserId = $member->get("userId");
		
		
		/* Create secret key */
		$secret = $member->generateSecret();
		$member->set("secret", $secret)->update();
		
		
		/* Create Confirm Email Token */
		$ce = new ConfirmEmailToken($db);
		$ce->set("userId", $newUserId)->delete(); // no duplicates
		$rawToken = $ce->generateToken(); // generate user-facing token
		$ce->setToken($rawToken)->create(); // encrypt token and save to database
		$ceLink = $app->config("page", "confirm-email", array( "token" => $rawToken )); // create clickable link
		
		
		/* Create Remember Me in database */
		$rm = new RememberMe($db);
		$rm->set("userId", $newUserId);
		$cookieValue = $rm->generateCookieValue();
		$rm->setCookieValue($cookieValue);
		$rm->create();
		
		
		/* Generate cookie for the browser */
		$cookie = new Cookie(array(
			"name" => "rememberMe",
			"value" => $cookieValue
		));
		$cookie->create();
		
		
		// Automatically follow?
		if (!is_null($leaderId)) {
			$user = new User($db);
			$leader = $user->set("userId", $leaderId)->find(1);
			if ($leader) {
				
				// follow from follower
				$follow = new Follow($db);
				$follow->set(array(
					"userId" => $newUserId,
					"leaderId" => $leader->get("userId")
				))->delete(); // make sure there aren't duplicates
				$follow->set(array(
					"dateCreated" => $dateCreated,
					"timestamp" => $dateCreated
				))->create();
				unset($follow);
				
				
				// follow from the leader, too
				$follow = new Follow($db);
				$follow->set(array(
					"userId" => $leader->get("userId"),
					"leaderId" => $newUserId
				))->delete(); // make sure there aren't duplicates
				$follow->set(array(
					"dateCreated" => $dateCreated,
					"timestamp" => $dateCreated
				))->create();
				
				
				// email notification
				if ($leader->acceptsEmailFor($app->config("email-alert", "new-follower"))) {
					$email = new Email($app, array(
						"title" => "{$member->fullName()} has started following you on {$app->config('app','name')}.",
						"subject" => "Someone is following you on {$app->config('app','name')}",
						"body" => "<p><a href=\"{$app->config('page','profile',array('userId'=>$member->get('userId')))}\">View {$member->firstNamePossessive()} profile&nbsp;&#155;</a></p><p>If this is a problem and you need assistance, please <a href=\"{$app->config('page','contact')}\">send us a message</a>.</p>",
						"recipients" => array(
							$leader->fullName() => $leader->get("emailAddress")
						)
					));
					$email->create();
				}
				
				// account notification
				$follower = $member;
				$note = new Notification($db);
				$note->set(array(
					"userId" => $leader->get("userId"),
					"html" => "<a href=\"{$app->config('page','profile',array('userId'=>$follower->get('userId')))}\">{$follower->fullName()}</a> has started following you.",
					"dateCreated" => $dateCreated
				))->create();
				
				// account notification for the follower
				$note = new Notification($db);
				$note->set(array(
					"userId" => $follower->get("userId"),
					"html" => "<a href=\"{$app->config('page','profile',array('userId'=>$leader->get('userId')))}\">{$leader->fullName()}</a> has started following you.",
					"dateCreated" => $dateCreated
				))->create();
			}
		}
		
		
		// Preselect email notification preferences
		$nts = new NotificationType($db);
		$nts = $nts->find();
		if ($nts) {
			foreach ($nts as $nt) {
				$nt_u = new NotificationType_User($db);
				$nt_u->set(array(
					"notificationTypeId" => $nt->get("notificationTypeId"),
					"userId" => $newUserId
				))->create();
			}
		}
		
		
		// Email Confirmation
		$ceEmail = new Email($app, array(
			"title" => "Please Verify Your Email Address",
			"subject" => "Verify your email address",
			"body" => "<p><a href=\"{$ceLink}\">Verify My Email Address</a></p>",
			"recipients" => array(
				$member->fullName() => $member->get("emailAddress")
			)
		));
		$ceEmail->create();
		
		
		// Welcome email
		$email = new Email($app, array(
			"title" => "Welcome to {$app->config('app','name')}!",
			"subject" => "Welcome to {$app->config('app','name')}!",
			"body" => "<p>It's good to meet you, {$member->get('firstName')}. If for any reason you need assistance, please <a href=\"{$app->config('page','contact')}\">send us a message</a>.</p><p><a href=\"{$app->config('page','profile')}\">View your profile</a> or <a href=\"{$app->config('page','new-list')}\">Create a new wish list or registry.</a></p>",
			"recipients" => array(
				$member->fullName() => $member->get("emailAddress")
			)
		));
		$email->create();
		
		
		// Send response
		$successMessage = "Welcome to GiftDibs, " . $member->get("firstName") . "!";
		$session->setMessage($successMessage);
		$session->setMessageType("success");
		$response = new Response($app, array(
			"status" => "success",
			"message" => $successMessage,
			"redirect" => $app->config('page','home')
		));
	}
	$response->sendIt();
}