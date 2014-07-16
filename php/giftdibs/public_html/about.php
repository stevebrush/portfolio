<?php
require "../resources/config.php";
require "../resources/initialize.php";

$page = new Page($app);
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : "";

switch ($tab) {
	
	case "privacy":
	$page->setSlug("privacy")
		->setTitle($app->config("app", "name") . " Privacy Policy")
		->setContent(SNIPPET_PATH . "privacy.snippet.php", "primary")
		->setTemplate("main");
	break;
	
	case "terms":
	$page->setSlug("terms")
		->setTitle($app->config("app", "name") . " Terms of Service")
		->setContent(SNIPPET_PATH . "terms.snippet.php", "primary")
		->setTemplate("main");
	break;
	
	case "contact":
	$page->setSlug("contact")
		->setTitle("Contact " . $app->config("app", "name"))
		->setContent(FORM_PATH . "contact.form.php", "primary")
		->setTemplate("main");
	break;
	
	default:
	$page->setSlug("about")
		->setTitle("About " . $app->config("app", "name"))
		->setContent(SNIPPET_PATH . "about.snippet.php", "primary")
		->setTemplate("main");
	break;
	
}

include $page->rendering();