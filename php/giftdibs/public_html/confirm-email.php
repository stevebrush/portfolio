<?php
require "../resources/config.php";
require "../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must login to view this page.");
	$app->redirectTo($app->config("page", "login", array("redirect" => urlencode($app->currentUrl()))));
}

if (!isset($_GET["token"])) {
	$session->setMessage("<strong>Something Went Wrong</strong><br />The link to confirm your email wasn't formatted correctly. You could try clicking on it again, or send a new request.");
	$session->setMessageType("error");
	$app->redirectTo($app->config("page", "home"));
}
else {
	$ceToken = $_GET["token"];
	$ce = new ConfirmEmailToken($db);
	$ce = $ce->set("confirmEmailToken", $ce->encryptToken($ceToken))->find(1);
	if (!$ce) {
		$session->setMessage("<strong>Something Went Wrong</strong><br />We couldn't rectify your email validation request. Please try sending another verification email.");
		$session->setMessageType("error");
		$app->redirectTo($app->config("page", "home"));
	} else {
		$ce->delete();
		$me->set("emailConfirmed", "1")->update();
		$session->setMessage("<strong>Email Confirmed!</strong><br />Thanks for doing that for us. All features of the site have now been unlocked on your account.");
		$session->setMessageType("success");
		$app->redirectTo($app->config("page", "home"));
	}
}