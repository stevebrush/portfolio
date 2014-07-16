<?php
/*
Plugin Name: Luminate Forms
Plugin URI: http://www.blackbaud.com/
Description: This plugin embeds Luminate Online forms in a WordPress page.
Version: 1.0.4
Author: Blackbaud Interactive
Author URI: http://www.blackbaud.com/
License: Property of Blackbaud, Inc.
*/

// Libraries...
require "config.php";
require LO_CLASS_PATH."LuminateApplication.class.php";
require LO_CLASS_PATH."LuminateConstituent.class.php";
require LO_CLASS_PATH."LuminatePage.class.php";
require LO_CLASS_PATH."LuminateForm.class.php";
require LO_CLASS_PATH."LuminateFormField.class.php";
require LO_WIDGET_PATH."LuminateSurveyWidget.class.php";
require LO_WIDGET_PATH."LuminateProfileMenuWidget.class.php";
require "functions.php";

// Globals...
$loApp = new LuminateApplication($loConfig);
$loConst = new LuminateConstituent($loApp);
$loPage = new LuminatePage($loApp, $loConst);
unset($loConfig);

// WordPress dashboard...
if (is_admin()) {

	add_action( "admin_menu", "lo_add_menu_options");
	add_action( "admin_menu", array($loPage, "pluginMenu"));
	add_action( "widgets_init", array($loPage, "registerWidgets"));

	/* Auto-updater */
	// set_site_transient("update_plugins", null); // enable update check on every request (for testing)
	add_filter("pre_set_site_transient_update_plugins", "check_for_plugin_update");
	add_filter("plugins_api", "plugin_api_call", 10, 3);
}

// WordPress frontend...
else if (!is_login_page()) {
	//add_action("template_redirect", "pre_process_shortcode", 1);
	add_action("init", array($loPage, "registerScripts"));
	add_action("init", array($loPage, "registerStyles"));
	add_action("wp_footer", array($loPage, "printScripts"));
	add_action("wp_print_styles", array($loPage, "printStyles"));
}

// Shortcodes...
add_shortcode("luminate-form-login", array($loPage, "shortcodeLogin"));
add_shortcode("luminate-form-register", array($loPage, "shortcodeRegister"));
add_shortcode("luminate-form-reset-password", array($loPage, "shortcodeResetPassword"));
add_shortcode("luminate-form-profile", array($loPage, "shortcodeProfile"));
add_shortcode("luminate-form-donate", array($loPage, "shortcodeDonate"));
add_shortcode("luminate-form-survey", array($loPage, "shortcodeSurvey"));
