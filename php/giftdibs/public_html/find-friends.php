<?php 
require "../resources/config.php";
require "../resources/initialize.php";

$page = new Page($app);
$page->setTitle("Find Friends")
	->setSlug("find-friends")
	->setContent(SNIPPET_PATH . "find-friends.snippet.php", "primary")
	->setTemplate("main");
	
include $page->rendering();