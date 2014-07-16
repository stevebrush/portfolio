<?php
function randomString($length = 30) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function isEmpty($string) {
	return ($string === "" || ctype_space($string) || is_null($string)) ? true : false;
}

function print_f($arr=array()) {
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function getSession() {
	global $session;
	return $session;
}

function getApp() {
	global $app;
	return $app;
}

function getDB() {
	global $db;
	return $db;
}