<?php 
require "../resources/config.php";
require "../resources/initialize.php";

$page = new Page($app);
$page->setSlug("reset-password")
	->setTitle("Reset Your Password")
	->setContent(FORM_PATH . "reset-password.form.php", "primary")
	->setTemplate("main");

include $page->rendering();