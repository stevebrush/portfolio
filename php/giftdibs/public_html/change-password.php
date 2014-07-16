<?php 
require "../resources/config.php";
require "../resources/initialize.php";

if (!isset($_GET["token"]) && !$session->isLoggedIn()) {
	$app->redirectTo($app->config("page", "reset-password"));
}

if (isset($_GET["token"])) {
	$rpToken = $_GET["token"];
	$rp = new ResetPasswordToken($db);
	$rp = $rp->findByToken($rpToken);
	
	if (!$rp) {
		$session->setMessage("The token to reset your password has been used, or is expired. Please <a href=\"{$app->config('page','reset-password')}\">create a new one</a>.");
		$app->redirectTo($app->config("page", "home"));
	}
	
	$user = new User($db);
	$me = $user->set("userId", $rp->get("userId"))->find(1);
}

$page = new Page($app);
$page->setSlug("change-password")
	->setTitle("Change Your Password")
	->setContent(FORM_PATH."change-password.form.php", "primary")
	->setTemplate("main");

include $page->rendering();