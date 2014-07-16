<?php 
require "../resources/config.php";
require "../resources/initialize.php";

if (!isset($_GET["userId"])) {
	if ($session->isLoggedIn()) {
		$they = $me;
	} else {
		$session->setMessage("That page no longer exists.");
		$app->redirectTo($app->config("page", "home"));
	}
} else {
	$they = new User($db);
	if (!$they = $they->set("userId", $_GET["userId"])->find(1)) {
		$session->setMessage("That page no longer exists.");
		$app->redirectTo($app->config("page", "home"));
	}
}

if ($me->hasBlocked($they)) {
	$session->setMessage("You have blocked {$they->fullName()} and so cannot view {$they->pronoun('his')} profile.&nbsp;&nbsp; <a href=\"{$app->config('page','privacy-settings')}\">Manage blocked users&nbsp;&rarr;</a>");
	$session->setMessageType("error");
	$app->redirectTo($app->config("page", "home"));
}

if ($they->hasBlocked($me)) {
	$session->setMessage("You do not have permission to view {$they->firstNamePossessive()} profile.");
	$session->setMessageType("error");
	$app->redirectTo($app->config("page", "home"));
}

$page = new Page($app);
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : "";

switch ($tab) {

	case "notifications":
	$page->setTitle("Notifications")
		->setSlug("notifications")
		->setContent(SNIPPET_PATH . "notifications.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;
	
	case "follow-me":
	if (!isset($_GET["leaderId"])) {
		$session->setMessage("The link you followed has expired.");
		$session->setMessageType("error");
		$app->redirectTo($app->config("page", "profile"));
	}
	$page->setTitle("Follow me")
		->setSlug("dibs")
		->setContent(SNIPPET_PATH . "follow-me.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;

	case "followers":
	$page->setTitle($they->firstNamePossessive()." followers")
		->setSlug("followers")
		->setContent(SNIPPET_PATH . "followers.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;
	
	case "following":
	$page->setTitle("People following ".$they->get("firstName"))
		->setSlug("following")
		->setContent(SNIPPET_PATH . "followers.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;

	case "find-friends":
	$page->setTitle("Find friends")
		->setSlug("find-friends")
		->setContent(SNIPPET_PATH . "profile-title.snippet.php", "primary")
		->setTemplate("profile");
	break;
	
	case "messages":
	$page->setTitle("Messages")
		->setSlug("messages")
		->setContent(SNIPPET_PATH . "messages.snippet.php", "primary")
		->setTemplate("profile");
	break;

	case "about":
	$page->setTitle($they->fullName())
		->setSlug("profile-about")
		->setContent(SNIPPET_PATH . "profile-about.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;
	
	case "most-wanted":
	$page->setTitle($they->fullName())
		->setSlug("most-wanted")
		->setContent(SNIPPET_PATH . "most-wanted.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;

	case "wish-lists":
	$page->setTitle($they->fullName())
		->setSlug("wish-lists")
		->setContent(SNIPPET_PATH . "wish-lists.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;
	
	case "gifts":
	default:
	$page->setTitle($they->fullName())
		->setSlug("profile")
		->setContent(SNIPPET_PATH . "profile-gifts.snippet.php", "primary")
		->setContent(SNIPPET_PATH . "profile-summary.snippet.php", "secondary")
		->setTemplate("profile");
	break;
	
}

include $page->rendering();