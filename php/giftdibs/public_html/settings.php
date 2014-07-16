<?php 
require "../resources/config.php";
require "../resources/initialize.php";

if (!$session->isLoggedIn()) {
	$session->setMessage("You must log in to view this page.");
	$app->redirectTo($app->config("page", "login", array("redirect" => urlencode($app->currentUrl()))));
}

$page = new Page($app);
$tab = (isset($_GET["tab"])) ? $_GET["tab"] : "";

switch ($tab) {

	default:
	case "edit-profile":
	$page->setSlug("edit-profile")
		->setTitle("Edit profile")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "edit-profile.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-account":
	$page->setSlug("edit-account")
		->setTitle("Account settings")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "account-details.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-shipping-address":
	$page->setSlug("edit-shipping-address")
		->setTitle("Shipping address")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "shipping-address.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-email-preferences":
	$page->setSlug("edit-email-preferences")
		->setTitle("Email preferences")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "email-preferences.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-reminders":
	$page->setSlug("edit-reminders")
		->setTitle("Reminders")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "reminders.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-holidays":
	$page->setSlug("edit-holidays")
		->setTitle("Holidays")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "holidays.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-privacy":
	$page->setSlug("edit-privacy")
		->setTitle("Privacy settings")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "privacy-settings.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "edit-gift-guide":
	$page->setSlug("edit-gift-guide")
		->setTitle("Gift guide")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "gift-guide.form.php", "primary")
		->setTemplate("wiki");
	break;
	
	case "delete-account":
	$page->setSlug("delete-account")
		->setTitle("Delete Account")
		->setContent(SNIPPET_PATH . "settings-navigation.snippet.php", "secondary")
		->setContent(FORM_PATH . "delete-account.form.php", "primary")
		->setTemplate("wiki");
	break;
	
}

include $page->rendering();