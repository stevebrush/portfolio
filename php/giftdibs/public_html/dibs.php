<?php
require "../resources/config.php";
require "../resources/initialize.php";

$page = new Page($app);

if ($session->isLoggedIn()) {
	$page->setTitle("My dibs")
		->setSlug("dibs")
		->setContent(SNIPPET_PATH . "dibs.snippet.php", "primary")
		->setTemplate("profile");
} else {
	$session->setMessage("You need to log in to view your dibs.");
	$session->setMessageType("info");
	$app->redirectTo($app->config("page", "login", array("redirect" => $app->currentUrl())));
}

include $page->rendering();
