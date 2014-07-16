<?php
require "../resources/config.php";
require "../resources/initialize.php";

$gift = new Gift($db);
$page = new Page($app);
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : "";

// Get gift information...
if (isset($_GET["giftId"])) {

	$gift = new Gift($db);
	$gift = $gift->set("giftId", $_GET["giftId"])->find(1);
	
	if ($gift) {
		
		$wishList = new WishList($db);
		$wishList = $wishList->set("wishListId", $gift->get("wishListId"))->find(1);
		
		$privateUsers = $wishList->getPrivateUsers();
		
		$user = new User($db);
		$they = $user->set("userId", $gift->get("userId"))->find(1);
		
	} else {
	
		$session->setMessage("That gift doesn't exist anymore.");
		$app->redirectTo($app->config("page", "home"));
		
	}
}

// Wish list information
$giftId = $gift->get("giftId");
if ( !empty($giftId) || isset($_GET["wishListId"]) ) {

	$wishListId = (!empty($giftId)) ? $gift->get("wishListId") : $_GET["wishListId"];

	$wishList = new WishList($db);
	$wishList = $wishList->set("wishListId", $wishListId)->find(1);

	if (!$wishList) {
		$session->setMessage("The wish list for the gift doesn't exist.");
		$app->redirectTo($app->config("page", "home"));
	}

	if (!$wishList->userCanView($me)) {
		$session->setMessage("You do not have permssion to view that gift.");
		$session->setMessageType("error");
		$app->redirectTo($app->config("page", "home"));
	}
}


switch ($tab) {

	case "edit":
	$page->setTitle("Edit gift")
		->setSlug("edit-gift")
		->setContent(FORM_PATH . "gift.form.php", "primary")
		->setTemplate("main");
	break;

	case "create":
	$page->setTitle("New gift")
		->setSlug("new-gift")
		->setContent(FORM_PATH . "gift.form.php", "primary")
		->setTemplate("form");
	break;
	
	default:
	$page->setTitle($gift->get("name"))
		->setSlug("gift-detail")
		->setContent(SNIPPET_PATH . "gift-detail.snippet.php", "primary")
		->setTemplate("main");
	break;
}

include $page->rendering();