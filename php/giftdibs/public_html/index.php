<?php
require "../resources/config.php";
require "../resources/initialize.php";

$app->check();
$page = new Page($app);

if ($session->isLoggedIn()) {
	if (!$me->emailConfirmed()) {
		$ce = new ConfirmEmailToken($db);
		$ce = $ce->set("userId", $me->get("userId"))->find(1);
		if ($ce) {
			$page->addAnnouncement(array(
				"html" => "<p><strong>Verify your email address</strong></p><p>{$me->get('emailAddress')} - <a href=\"{$app->config('page','account-details')}\">edit</a></p><p>A link to verify your email address was sent to your email inbox. Click on the link provided in the body to complete the verification process.</p><p><a href=\"#\" class=\"btn btn-primary btn-resend-confirmation-email\" data-url-post=\"{$app->config('ajax','new-confirm-email')}\">Resend verification email</a></p>"
			));
		} else {
			$page->addAnnouncement(array(
				"html" => "<p><strong>Verify your email address</strong></p><p>{$me->get('emailAddress')} - <a href=\"{$app->config('page','account-details')}\">edit</a></p><p>Click the button below to send an email confirmation request. Once it arrives in your email's inbox, click on the link provided in the body to complete the verification process.</p><p><a href=\"#\" class=\"btn btn-primary btn-resend-confirmation-email\" data-url-post=\"{$app->config('ajax','new-confirm-email')}\">Send verification email</a></p>"
			));
		}
	}
	$page->setSlug("home")
		->setTitle($app->config("app", "name"))
		->setContent(SNIPPET_PATH . "newsfeed.snippet.php", "primary")
		->setTemplate("main");
} else {
	$page->setSlug("welcome")
		->setTitle("Welcome to {$app->config('app', 'name')}")
		->setContent(SNIPPET_PATH . "welcome.snippet.php", "primary")
		->setTemplate("main");
}

include $page->rendering();