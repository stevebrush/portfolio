<?php 
require "../resources/config.php";
require "../resources/initialize.php";

if ($session->isLoggedIn()) {
	$session->setMessage("You are already logged in. <a href=\"{$app->config('page','logout')}\">Not {$me->fullName()}</a>?");
	$app->redirectTo($app->config("page", "home"));
}

$page = new Page($app);
$page->setSlug("login")
	->setTitle($app->config("app", "name") . " Log In")
	->setContent(FORM_PATH . "login.form.php", "primary")
	->setTemplate("form");

include $page->rendering();