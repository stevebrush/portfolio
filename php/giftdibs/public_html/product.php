<?php
require "../resources/config.php";
require "../resources/initialize.php";

if (!isset($_GET["productId"]) || !isset($_GET["productIdType"])) {
	$session->setMessage("That item could not be found.");
	$session->setMessageType("error");
	$session->redirectTo($app->config("page", "home"));
}

$productId = $_GET["productId"];
$productIdType = $_GET["productIdType"];

$product = new Product($app);
$product = $product->set(array(
	"productId" => $productId,
	"productIdType" => $productIdType
))->find(1);

if (!$product) {
	$session->setMessage("That item could not be found.");
	$session->setMessageType("error");
	$app->redirectTo($app->config("page", "home"));
}

$page = new Page($app);
$page->setTitle($product->get("name"))
	->setSlug("product")
	->setContent(SNIPPET_PATH . "product-detail.snippet.php", "primary")
	->setTemplate("main");
	
include $page->rendering();