<?php
require "../resources/config.php";
require "../resources/initialize.php";

if ($session->isLoggedIn()) {

	$cookie = new Cookie();
	$cookie->setName("rememberMe");
	$cookie->destroy();
	
	$rm = new RememberMe($db);
	$rm->set(array("userId" => $session->getUserId()));
	$rm->delete();
	
	$session->logout();
	
	unset($cookie);
	unset($rm);
	unset($session);
}
$app->redirectTo($app->config("page", "home"));
