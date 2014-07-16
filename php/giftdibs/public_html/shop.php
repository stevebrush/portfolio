<?php
require "../resources/config.php";
require "../resources/initialize.php";

$page = new Page($app);
$page->setSlug("welcome")
	->setTitle("Welcome to {$app->config('app','name')}")
	->setContent(SNIPPET_PATH . "shop.snippet.php", "primary")
	->setTemplate("main");

include $page->rendering();
