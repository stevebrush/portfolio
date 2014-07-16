<?php
define("DS", 				DIRECTORY_SEPARATOR);
define("LO_PLUGIN_SLUG", 	basename(dirname(__FILE__)));
define("LO_SITE_ROOT", 		plugin_dir_path(__FILE__));
define("LO_WEB_ROOT", 		plugins_url()."/".LO_PLUGIN_SLUG."/public_html/");
define("LO_CLASS_PATH", 	LO_SITE_ROOT."resources".DS."lib".DS."classes".DS);
define("LO_WIDGET_PATH", 	LO_SITE_ROOT."resources".DS."lib".DS."widgets".DS);
define("LO_FORM_PATH", 		LO_SITE_ROOT."resources".DS."views".DS."forms".DS);
define("LO_ADMIN_PATH", 	LO_SITE_ROOT."resources".DS."views".DS."admin".DS);
define("LO_UPDATER_URL", 	"https://api.blackbaud.com/services/wordpress/updater/");

$loConfig 				= array();
$loConfig["api"] 		= array(
	"key" 				=> get_option("lo_api_key"),
	"login-name" 		=> get_option("lo_api_user"),
	"login-password" 	=> get_option("lo_api_password"),
	"version" 			=> get_option("lo_api_version"),
	"secret" 			=> get_option("lo_api_secret"),
	"http" 				=> get_option("lo_api_url"),
	"https" 			=> get_option("lo_api_url_secure")
);
$loConfig["url"] 		= array(
	"login" 			=> get_option("lo_page_permalink_login"),
	"logout" 			=> get_option("lo_page_permalink_logout"),
	"register"			=> get_option("lo_page_permalink_register"),
	"reset-password"	=> get_option("lo_page_permalink_reset_password"),
	"profile"			=> get_option("lo_page_permalink_profile"),
	"return-user"		=> get_option("lo_page_permalink_return_user")
);
$loConfig["dir"] 		= array(
	"js" 				=> LO_WEB_ROOT."js/",
	"img" 				=> LO_WEB_ROOT."img/",
	"css" 				=> LO_WEB_ROOT."css/",
);
$loConfig["action"] 	= array(
	"login" 			=> "{$loConfig['api']['http']}CRConsAPI",
	"register" 			=> "{$loConfig['api']['http']}CRConsAPI",
	"profile" 			=> "{$loConfig['api']['https']}CRConsAPI",
	"survey" 			=> "{$loConfig['api']['https']}CRSurveyAPI",
	"donate" 			=> "{$loConfig['api']['https']}CRDonationAPI",
);