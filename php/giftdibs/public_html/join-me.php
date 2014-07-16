<?php
require "../resources/config.php";
require "../resources/initialize.php";

$leaderId = (isset($_GET["leaderId"])) ? $_GET["leaderId"] : 0;
$user = new User($db);
$they = $user->set("userId", $leaderId)->find(1);

if (!isset($leaderId) || !$they) {
	$session->setMessage("The link you followed has expired.");
	$session->setMessageType("error");
	$app->redirectTo($app->config("page", "profile"));
}

$page = new Page($app);
$page->setTitle("Join me on {$app->config('app', 'name')}")
	->setSlug("join-me")
	->setMeta(array(
		"title" => "Join {$they->fullName()} on {$app->config('app', 'name')}!",
		"description" => "{$they->fullName()} is on {$app->config('app', 'name')}, a social gift registry that let's your friends see exactly what you want for any occassion.",
		"image" => "{$they->getThumbnail()->size('md')->get('src')}"
	))
	->setContent(SNIPPET_PATH . "join-me.snippet.php", "primary")
	->setTemplate("main");
	
include $page->rendering();