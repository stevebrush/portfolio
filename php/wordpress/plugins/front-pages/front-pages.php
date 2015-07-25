<?php
/*
Plugin Name: Newseum - Today's Front Pages
Description: Displays newspaper front pages from Newseum feeds.
Version: 1.0.0
Author: Blackbaud, Inc.
Text Domain: newseum-front-pages
*/

require "config.php";
require TFP_CLASS_PATH . "TFP_Application.php";
require "functions.php";

$tfp_app = new TFP_Application ($tfp_config);
unset ($tfp_config);

# Dashboard.
if (is_admin()) {
	add_action ("admin_menu", array ($tfp_app, "DashboardMenu"));

# Frontend.
} else {
	add_action ("init",            array ($tfp_app, "RegisterScripts"));
	add_action ("init",            array ($tfp_app, "RegisterStyles"));
	add_action ("wp_head",         array ($tfp_app, "Start"));
	add_action ("wp_head",         array ($tfp_app, "PageTitle"));
	add_action ("wp_footer",       array ($tfp_app, "PrintScripts"));
	add_action ("wp_print_styles", array ($tfp_app, "PrintStyles"));
	add_filter ("query_vars",      array ($tfp_app, "AddQueryVariables"));
	add_shortcode ("front-pages",  array ($tfp_app, "Shortcode"));
	add_shortcode ("front-pages-preview", array ($tfp_app, "ShortcodePreview"));
}
