<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($_POST) {
	
	$userId 				= (isset($_POST["userId"])) ? $_POST["userId"] : null; // update member with facebook info
	$leaderId 				= (isset($_POST["leaderId"])) ? $_POST["leaderId"] : null; // automatically follow
	$redirect 				= (isset($_POST["redirect"])) ? $_POST["redirect"] : null;
	$facebookBirthday 		= $_POST["facebookBirthday"]; // validated via age gate
	$facebookThumbnail 		= $_POST["facebookThumbnail"];
	
	$validator = new Validator();
	$me->getInputs();
	$validator->addInput( $me->getInput("firstName"), $_POST["facebookFirstName"]);
	$validator->addInput( $me->getInput("lastName"), $_POST["facebookLastName"]);
	$validator->addInput( $me->getInput("emailAddress"), $_POST["facebookEmailAddress"]);
	$validator->addInput( $me->getInput("gender"), $_POST["facebookGender"]);
	$validator->addInput( $me->getInput("facebookUserId"), $_POST["facebookUserId"]);
	$validator->addInput( $me->getInput("facebookAccessToken"), $_POST["facebookAccessToken"]);
	$validator->run();
	
	if (!strpos($facebookThumbnail,"facebook.com")) {
		$validator->addError("Your Facebook thumbnail is corrupted.");
	}
	
	// Validations for new registrations:
	if (!isset($userId)) {
	
		if ($me->emailExists( $validator->getInputValue("emailAddress") )) {
			$validator->addError("Your email address already exists in our records. Wanna <a href=\"{$app->config('page','login')}\">login</a>?");
		}
		
		if (!$app->validateAge($facebookBirthday)) {
			$validator->addError("You must be at least {$app->getMinAgeAllowed()} years old to use {$app->config('app','name')}.");
		}
	}
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));
		
	} else {
	
		$date = new DateTime(); 
		$dateLoggedIn = $date->format($app->config("date","format"));
		
		if (!isset($userId) || is_null($userId)) {
		
			$member = new User($db);
			$member->set(array(
				"firstName" => $validator->getInputValue("firstName"),
				"lastName" => $validator->getInputValue("lastName"),
				"emailAddress" => $validator->getInputValue("emailAddress"),
				"gender" => $validator->getInputValue("gender"),
				"birthday" => $facebookBirthday,
				"facebookUserId" => $validator->getInputValue("facebookUserId"),
				"facebookAccessToken" => $validator->getInputValue("facebookAccessToken"),
				"dateCreated" => $dateLoggedIn,
				"secret" => 0,
				"roleId" => 2,
				"currencyId" => 1,
				"dateLastLoggedIn" => $dateLoggedIn,
				"timestamp" => $dateLoggedIn
			));
			$member->create();
			$newUserId = $member->get("userId");
			$successMessage = "Welcome to GiftDibs, ".$member->get("firstName")."!";
			
			
			/* Create secret key */
			$secret = $member->generateSecret();
			$member->set("secret", $secret)->update();
			
			
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
			
			
			// automatically follow?
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
			
			
			// Email Alerts
			$ea = new EmailAlert($db);
			$alerts = $ea->find();
			if ($alerts) {
				foreach ($alerts as $alert) {
					$ea_u = new EmailAlert_User($db);
					$ea_u->set(array(
						"userId" => $newUserId,
						"emailAlertId" => $alert->get("emailAlertId")
					))->create();
				}
			}
			
			
			// Automatically follow Facebook friends
			$my_access_token = $facebook->getAccessToken();
			try {
			
				$friends = $facebook->api("/me/friends", array("access_token" => $my_access_token));
				$friendsData = $friends["data"];
				$fbUserIds = array();
				if (count($friendsData) > 0) {
					foreach ($friendsData as $friend) {
						$fbUserIds[] = $friend["id"];
					}
				}
				
				$sql = "SELECT facebookUserId FROM User WHERE facebookUserId != '0'";
				$stmt = $db->query($sql);
				$gdUserIds = array();
				if ($rowCount = $stmt->rowCount()) {
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
					foreach($result as $r) {
						$gdUserIds[] = $r["facebookUserId"];
					}
				}
				
				$matchedIds = array_intersect($fbUserIds, $gdUserIds);
				if (count($matchedIds) > 0) {					
					foreach ($matchedIds as $id) {
					
						$user = new User($db);
						$leader = $user->set("facebookUserId", $id)->find(1);
						
						if ($leader) {
							$follow = new Follow($db);
							$follow->set(array(
								"userId" => $newUserId,
								"leaderId" => $leader->get("userId")
							))->delete(); // ensure we don't create duplicate entries
							$follow->set(array(
								"dateCreated" => $dateLoggedIn,
								"timestamp" => $dateLoggedIn
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
						}
					}
				}
				
			} catch (FacebookApiException $e) {}
			
			
			$email = new Email($app, array(
				"title" => "Welcome to {$app->config('app','name')}!",
				"subject" => "Welcome to {$app->config('app','name')}!",
				"body" => "<p>It's good to meet you, {$member->get('firstName')}. If for any reason you need assistance, please <a href=\"{$app->config('page','contact')}\">send us a message</a>.</p><p><a href=\"{$app->config('page','profile')}\">View your profile</a> or <a href=\"{$app->config('page','new-list')}\">Create a new wish list or registry.</a></p>",
				"recipients" => array($member->fullName() => $member->get('emailAddress'))
			));
			$email->create();
			
		} else {
		
			$member = new User($db);
			$member = $member->set("userId", $userId)->find(1);
			$member->set(array(
				"firstName" => $validator->getInputValue("firstName"),
				"lastName" => $validator->getInputValue("lastName"),
				"emailAddress" => $validator->getInputValue("emailAddress"),
				"gender" => $validator->getInputValue("gender"),
				"birthday" => $facebookBirthday,
				"facebookUserId" => $validator->getInputValue("facebookUserId"),
				"facebookAccessToken" => $validator->getInputValue("facebookAccessToken"),
				"timestamp" => $dateLoggedIn
			))->update();
			$member->deleteThumbnail();
			$newUserId = $userId;
			$successMessage = "Profile updated with your Facebook account.";
		}
		
		if (isset($facebookThumbnail)) {
			$img = $member->createThumbnail( $facebookThumbnail );
			if ($img->isCreated()) {
				$member->set("imageId", $img->get("imageId"))->update();
			} else {
				$imageErrors = $img->getErrors();
			}
		}
		
		$redirect = (isset($redirect)) ? $redirect : $app->config("page","home");
		
		if (isset($imageErrors)) {
			$session->setMessage($imageErrors);
			$session->setMessageType("error");
			$response = new Response($app, array(
				"status" => "error",
				"message" => $imageErrors
			));
		} else {
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $redirect
			));
		}
		
	}
	$response->sendIt();
}
