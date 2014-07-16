<?php 
require "../resources/config.php";
require "../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must log in to view this page.");
	$app->redirectTo($app->config("page", "login", array("redirect"=> urlencode($app->currentUrl()))));
}

$page = new Page($app);
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : "";

switch ($tab) {

	case "create":
	$page->setSlug("new-message")
		->setTitle("New message")
		->setContent(FORM_PATH . "message.form.php", "primary")
		->setTemplate("form");
	break;
	
	default:
	if (isset($_GET["messageId"])) {
		$thisMessageId = $_GET["messageId"];
		$thisMessage = new Message($db);
		$thisMessage = $thisMessage->set("messageId", $thisMessageId)->find(1);
		if (!$thisMessage) {
			$session->setMessage("That message has been deleted or doesn't exist.");
			$session->setMessageType("error");
			$app->redirectTo($app->config("page", "messages"));
		}
		$user = new User($db);
		$users = $user->query("SELECT User.firstName FROM User, Message_User WHERE Message_User.messageId = {$thisMessage->get('messageId')} AND Message_User.userId = User.userId AND Message_User.userId != {$me->get('userId')}");
		$counter = count($users);
		$userString = ($counter > 1) ? "You, " : "You and ";
		while ($k = array_pop($users)) {
			$userString .= $k->get("firstName");
			$counter--;
			switch ($counter) {
				case 1:
					$userString .= ", and ";
				break;
				case 0:
				break;
				default:
					$userString .= ", ";
				break;
			}
		}
	} else {
		$session->setMessage("That message has been deleted or doesn't exist.");
		$session->setMessageType("error");
		$app->redirectTo($app->config("page", "messages"));
	}
	$page->setSlug("message")
		->setTitle($userString)
		->setContent(SNIPPET_PATH . "message.snippet.php", "primary")
		->setTemplate("profile");
	break;
}

include $page->rendering();