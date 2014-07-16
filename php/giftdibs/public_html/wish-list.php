<?php 
require "../resources/config.php";
require "../resources/initialize.php";

// If wishListId provided, verify if it exists
if (isset($_GET["wishListId"])) {
	$wishList = new WishList($db);
	if ($wishList = $wishList->set("wishListId", $_GET["wishListId"])->find(1)) {
		$they = new User($db);
		$they = $they->set("userId", $wishList->get("userId"))->find(1);
	} else {
		unset($wishList);
	}
}

$page = new Page($app);
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : "";

switch ($tab) {

	case "edit":
	if (!$session->isLoggedIn()) { // User not logged in
		$session->setMessage("Please log in to edit that wish list.");
		$session->setMessageType("info");
		$app->redirectTo($app->config("page", "login", array("redirect" => urlencode($app->currentUrl()))));
	}
	if (!isset($wishList)) { // Wish list doesn't exist
		$session->setMessage("That wish list could not be found.");
		$app->redirectTo($app->config("page", "home"));
	}
	if (!$me->isAlso($they)) { // User doesn't own the wish list
		$session->setMessage("You don't have the necessary permissions to edit that wish list.");
		$session->setMessageType("error");
		$app->redirectTo($app->config("page", "home"));
	}
	$page->setTitle("Edit wish list")
		->setSlug("edit-wish-list")
		->setContent(FORM_PATH . "wish-list.form.php", "primary")
		->setTemplate("main");
	break;


	case "create":
	if (!$session->isLoggedIn()) { // user isn't logged in
		$app->redirectTo($app->config("page", "home"));
	} 
	if (isset($wishList)) { // wish list information exists in the URL, redirect to clean it up
		$app->redirectTo($app->config("page", "new-wish-list"));
	}
	$page->setTitle("New wish list")
		->setSlug("new-wish-list")
		->setContent(FORM_PATH . "wish-list.form.php", "primary")
		->setTemplate("main");
	break;

	default:
	if (!isset($wishList)) { 
		// wish list doesn't exist
		$session->setMessage("That wish list could not be found.");
		$app->redirectTo($app->config("page", "home"));
	}
	if (!$wishList->userCanView($me)) {
		if (!$session->isLoggedIn()) { 
			if ($wishList->get("privacyId") != 1) { // not public
				// wish list is private and not logged in?
				$session->setMessage("<strong>{$they->get('firstName')} has only made that wish list available to {$they->pronoun('his')} followers.</strong><br>You must either log in to GiftDibs (below) or request that {$they->get('firstName')} make the wish list public.");
				$session->setMessageType("warning");
				$app->redirectTo($app->config("page", "login", array("redirect" => $app->currentUrl())));
			}
		} else {
			// wish list is private and user logged in
			$session->setMessage("{$they->get('firstName')} has only made that wish list available to {$they->pronoun('his')} followers. You must either follow {$they->get('firstName')} or request that {$they->pronoun('he')} make the wish list public.");
			$session->setMessageType("error");
			$app->redirectTo($app->config("page", "profile", array("userId" => $they->get("userId"))));
		}
	}
	$page->setTitle($wishList->get("name"))
		->setSlug("wish-list")
		->setContent(SNIPPET_PATH . "wish-list-title.snippet.php", "page-heading")
		->setContent(SNIPPET_PATH . "wish-list-nav.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "wish-list-detail.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "wish-list-info.snippet.php", "secondary")
		->setTemplate("main");
	break;

}
include $page->rendering();