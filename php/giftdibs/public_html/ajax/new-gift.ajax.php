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

	$userId 			= $session->getUserId();
	$signature 			= (isset($_POST["signature"])) ? $_POST["signature"] : null;
	$redirect 			= (isset($_POST["redirect"])) ? $_POST["redirect"] : null;
	
	/* THUMBNAIL */
	$thumbnail 			= (isset($_FILES["thumbnail"])) ? $_FILES["thumbnail"] : null;
	$thumbnailUrl 		= (isset($_POST["thumbnailUrl"])) ? $_POST["thumbnailUrl"] : null;
	$deleteThumbnail 	= (isset($_POST["removeThumbnailCheckbox"])) ? true : false;
	
	/* FROM ITEM SEARCH */
	$giftId				= (isset($_POST["giftId"])) ? $_POST["giftId"] : null;
	$productId 			= (isset($_POST["productId"])) ? $_POST["productId"] : null;
	$productIdType 		= (isset($_POST["productIdType"])) ? $_POST["productIdType"] : null;
	$createNewList 		= (isset($_POST["wishListSwitch"]) && $_POST["wishListSwitch"] == "new") ? true : false;
	$newWishListName 	= (isset($_POST["newWishListName"])) ? $_POST["newWishListName"] : "";

	$gift = new Gift($db);
	$gift->getInputs();
	
	$wishList = new WishList($db);
	$wishList->getInputs();
	
	$validator = new Validator();
	$validator->addInput( $gift->getInput("name"), 			$_POST["name"] );
	$validator->addInput( $gift->getInput("notes"), 		isset($_POST["notes"]) ? $_POST["notes"] : "" );
	$validator->addInput( $gift->getInput("url"), 			$_POST["url"] );
	$validator->addInput( $gift->getInput("price"), 		$_POST["price"] );
	$validator->addInput( $gift->getInput("priorityId"), 	isset($_POST["priorityId"]) ? $_POST["priorityId"] : null );
	$validator->addInput( $gift->getInput("gradeId"), 		isset($_POST["gradeId"]) ? $_POST["gradeId"] : null );
	$validator->addInput( $gift->getInput("quantity"), 		isset($_POST["quantity"]) ? $_POST["quantity"] : null);
	
	if (!$createNewList) {
		$validator->addInput($gift->getInput("wishListId"), $_POST["wishListId"]);
		
	} else {
		$inputNewWishListName = $wishList->getInput("name");
		$inputNewWishListName["field"]["name"] = "newWishListName";
		$inputNewWishListName["field"]["label"] = "New wish list name";
		$validator->addInput( $inputNewWishListName, $_POST["newWishListName"] );
	}
	$validator->run();
	
	// set clean values
	$name 			= $validator->getInputValue("name");
	$notes 			= $validator->getInputValue("notes");
	$url 			= $validator->getInputValue("url");
	$price 			= $validator->getInputValue("price");
	$priorityId 	= $validator->getInputValue("priorityId");
	$gradeId 		= $validator->getInputValue("gradeId");
	$quantity 		= $validator->getInputValue("quantity");
	
	if (!$createNewList) {
		$wishListId = $validator->getInputValue("wishListId");
	} else {
		$newWishListName = $validator->getInputValue("newWishListName");
	}
	
	
	
	/* GENERAL VALIDATIONS */
	
	// Check if 'url' includes 'http://' or 'https://'
	if (stristr($url, "http://") === FALSE && stristr($url, "https://") === FALSE && !empty($url)) {
		$url = "http://".$url;
	}
	
	// Signature
	if (!isset($signature)) {
		$validator->addError("A signature must be provided.");
	}
	
	// Price
	if ($price != "0" && $price != "") {
	
		// Remove commas and dollar signs
		$price = str_replace(",", "", str_replace("$", "", $price));
		
		// Add decimal place for storage
		if (strpos($price, ".") === false) {
			$price .= "00";
		
		// Decimal exists, so just remove it
		} else {
			$price = str_replace(".", "", $price);
		}
		
		// Convert to string
		$price = (string) $price;
		
	} else {
		$price = "";
	}
	
	/* Make sure user owns wish list */
	
	if (isset($wishListId) && !isEmpty($wishListId)) {
		$wishList = new WishList($db);
		$wishList = $wishList->set("wishListId", $wishListId)->find(1);
		if (!$wishList || $wishList->get("userId") != $session->getUserId()) {
			$validator->addError("You don't have permission to add a gift to this wish list.");
		}
	}
	
	
	
	/* EDIT GIFT VALIDATIONS */
	
	if (isset($giftId)) {
		if ($gift = $gift->set("giftId", $giftId)->find(1)) {
			if ($me->createSignature($giftId) !== $signature) {
				$validator->addError("The signature was not valid.");
			}
		}
	} 
	
	
	
	/* NEW GIFT VALIDATIONS */
	
	else {
	
		// Signature
		if ($me->createSignature("new-gift") !== $signature) {
			$validator->addError("The signature was not valid.");
		}
		
		/*
		else {
			// We're not creating a new wish list, so do some general validations.
			// Already exists in a wish list?
			if (isset($wishListId)) {
				if (isset($productId) && $productId) {
					$sql = "SELECT giftId FROM Gift WHERE wishListId = '{$wishListId}' AND productId = '{$productId}' LIMIT 1";
					$result = $db->runQuery($sql);
					$numRows = $db->numRows($result);
					if ($numRows > 0) {
					
						// (?) Instead of throwing an error, just increase the quantity by (1)
						
						$validator->addError("This item already exists in <strong>{$wishList->get('name')}</strong>.");
					}
				}
			} 
		}
		*/
		
		
		if (isset($productId) && isset($productIdType) && !empty($productId)) {
			if ($productIdType != "asin" && $productIdType != "szpid") {
				$validator->addError("Incorrect product ID type specified.");
			}
		} 
		
	}
	
	
	
	
	if ($errors = $validator->getErrors()) {
		$response = new Response($app, array(
			"status" => "error",
			"message" => $errors
		));

	} else {
		
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
		
		
		/* NEW WISH LIST */
		
		if ($createNewList) {
			$wishList = new WishList($db);
			$wishList = $wishList->set(array(
				"userId" => $session->getUserId(),
				"name" => $newWishListName,
				"privacyId" => 3,
				"dateCreated" => $dateCreated,
				"timestamp" => $dateCreated
			))->create();
			$wishListId = $wishList->get("wishListId");
			
		} else {
		
			/* UPDATE WISH LIST TIMESTAMP */
			$wishList->set("timestamp", $dateCreated);
			$wishList->update();
		}
		
		
		
		/* EDIT GIFT */
		
		if (isset($giftId)) {
			$gift = new Gift($db);
			$gift = $gift->set("giftId", $giftId)->find(1);
			$gift->set(array(
				"userId" => $userId,
				"wishListId" => $wishListId,
				"gradeId" => $gradeId,
				"priorityId" => $priorityId,
				"productId" => $productId,
				"productIdType" => $productIdType,
				"name" => $name,
				"notes" => $notes,
				"url" => $url,
				"price" => $price,
				"quantity" => $quantity,
				"timestamp" => $dateCreated
			))->update();
			
			$redirect = (isset($redirect)) ? $redirect : $app->config("page", "gift", array("giftId" => $giftId));
			$successMessage = "Gift successfully updated.";
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $redirect
			));
		} 
		
		
		
		/* NEW GIFT */
		
		else {
			$gift = new Gift($db);
			$gift = $gift->set(array(
				"userId" => $userId,
				"wishListId" => $wishListId,
				"productId" => $productId,
				"productIdType" => $productIdType,
				"gradeId" => $gradeId,
				"priorityId" => $priorityId,
				"name" => $name,
				"notes" => $notes,
				"url" => $url,
				"price" => $price,
				"quantity" => $quantity,
				"dateCreated" => $dateCreated,
				"timestamp" => $dateCreated
			))->create();
			$giftId = $gift->get("giftId");
			
			$redirect = (isset($redirect)) ? $redirect : $app->config("page", "gift", array("giftId" => $giftId));
			$successMessage = "<p><a href=\"{$app->config('page','gift',array('giftId'=>$gift->get('giftId')))}\"><strong>{$gift->get('name')}</strong></a> was successfully added to your wish list <a href=\"{$app->config('page','wish-list',array('wishListId'=>$wishList->get('wishListId')))}\"><strong>{$wishList->get('name')}</strong></a>.</p><p><a href=\"{$app->config('page','gift',array('giftId'=>$gift->get('giftId')))}\" class=\"btn btn-default btn-sm\">View</a> <a href=\"{$app->config('page','edit-gift',array('giftId'=>$gift->get('giftId')))}\" class=\"btn btn-default btn-sm\">Edit</a></p>";
			$session->setMessage($successMessage);
			$session->setMessageType("success");
			$response = new Response($app, array(
				"status" => "success",
				"message" => $successMessage,
				"redirect" => $redirect
			));
		}
		
		
		
		// Upload image...
		$imageErrors = "";
		if ($deleteThumbnail) {
			$gift->deleteThumbnail();
		} else {
			if (isset($thumbnail)) {
				$file = $thumbnail;
			} else if (isset($thumbnailUrl) && !empty($thumbnailUrl)) {
				$file = $thumbnailUrl;
			}
			if (isset($file)) {
				$img = $gift->createThumbnail( $file );
				if ($img->isCreated()) {
					$gift->set( "imageId", $img->get("imageId"))->update();
				} else {
					$imageErrors = $img->getErrors();
				}
			}
		}
		
		if ($imageErrors) {
			$session->setMessage($imageErrors);
			$session->setMessageType("error");
			$response = new Response($app, array(
				"status" => "error",
				"message" => $imageErrors,
				"redirect" => $redirect
			));
		}
	}
	
	$response->sendIt();
}