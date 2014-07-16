<?php
$packageOptions = array(
	"userId" => $they->get("userId"),
	"follower" => $me
);
include SNIPPET_PATH . "list-gifts.snippet.php";
?>