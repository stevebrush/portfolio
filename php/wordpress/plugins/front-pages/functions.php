<?php

function print_f($arr) {
	echo "<pre style=\"height:200px;overflow:auto;border:1px solid #ccc;background:#f2f1f0;margin:0 0 15px;\">";
	print_r($arr);
	echo "</pre>";
}

function add_query_vars($aVars) {
	$aVars[] = "tfp_id";
	$aVars[] = "tfp_page";
	$aVars[] = "tfp_show";
	$aVars[] = "tfp_display";
	$aVars[] = "tfp_region";
	$aVars[] = "tfp_title_letter";
	$aVars[] = "tfp_country_letter";
	$aVars[] = "tfp_state_letter";
	$aVars[] = "tfp_sort_by";
	$aVars[] = "tfp_archive_id";
	return $aVars;
}