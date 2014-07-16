<?php
include "../../resources/config.php";
include "../../resources/initialize.php";

if ($_POST) {
	
	$results = array();
	
	$query = isset($_POST["query"]) ? strtolower($_POST["query"]) : "";
	//$doSearchUsers = (!isset($_POST["doSearchUsers"]) || (isset($_POST["doSearchUsers"]) && $_POST["doSearchUsers"] == "true")) ? true : false;
	$target = (isset($_POST["target"])) ? $_POST["target"] : null;
	
	//if (!isEmpty($query)) {
	
		//if ($doSearchUsers) {
		
			$user = new User($db);
			$users = $user->findByKeyword($query, 5, array("userId, firstName, lastName, imageId"));
			
			if ($users) {
				foreach ($users as $user) {
					$results[] = array(
						"resultType" => "user",
						"fullName" => $user->fullName(),
						"url" => $app->config("page", "profile", array("userId" => $user->get("userId"))),
						"thumbnail" => $user->getThumbnail()->size("sm")->get("src")
					);
				}
			}
			
		//}
		
		/*
		$product = new Product($app);
		if ($products = $product->findByKeyword($query, 5)) {
			foreach ($products as $product) {
				$results[] = array(
					"resultType" 		=> "product",
					"giftName" 			=> $product->get("name"),
					"thumbnail" 		=> $product->getThumbnail()->size("lg")->get("src"),
					"price" 			=> $app->formatPrice($product->get("price"), false, ""),
					"salePrice" 		=> $app->formatPrice($product->get("salePrice"), false, ""),
					"formattedPrice" 	=> $app->formatPrice($product->bestPrice()),
					"url" 				=> $product->get("url"),
					"signature" 		=> $me->createSignature("new-gift"),
					"productId" 		=> $product->get("productId"),
					"productIdType" 	=> $product->get("productIdType")
				);
			}
		}
		*/
	//}
	
	$response = new Response($app, array(
		"status" => "success",
		"package" => array(
			"list" => $results,
			"target" => $target
		)
	));
	$response->sendIt();
}








