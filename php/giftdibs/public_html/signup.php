<?php
require "../resources/config.php";
require "../resources/initialize.php";

if ($session->isLoggedIn()) {
	$session->setMessage("You are already signed up for {$app->config('app','name')}. <a href=\"{$app->config('page','logout')}\">Not {$me->fullName()}</a>?");
	$app->redirectTo($app->config("page", "home"));
}

$page = new Page($app);
$page->setSlug("signup")
	->setTitle("Sign Up for {$app->config('app', 'name')}")
	->setContent(FORM_PATH . "signup.form.php", "primary")
	->setTemplate("form");

include $page->rendering();