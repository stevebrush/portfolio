<?php
/*
Plugin Name: Newseum - Today's Front Pages
Description: Displays newspaper front pages from Newseum feeds.
Version: 1.0.0
Author: Blackbaud, Inc.
Text Domain: newseum-front-pages
*/

require "config.php";
require TFP_VENDOR_PATH . "SimpleCache.php";
require TFP_CLASS_PATH . "TFP_Application.php";
require "functions.php";

$tfp_app = new TFP_Application($tfp_config);

// WordPress dashboard...
if (is_admin()) {
	add_action("admin_menu", array($tfp_app, "plugin_menu"));

// WordPress frontend...
} else {
	add_action("init", array($tfp_app, "registerScripts"));
	add_action("init", array($tfp_app, "registerStyles"));
	add_action("wp_footer", array($tfp_app, "printScripts"));
	add_action("wp_print_styles", array($tfp_app, "printStyles"));
	add_filter('query_vars', 'add_query_vars');
}

// Shortcodes...
add_shortcode("front-pages", array($tfp_app, "shortcode_front_pages"));
add_shortcode("front-pages-preview", array($tfp_app, "shortcode_front_pages_preview"));