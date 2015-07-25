<?php
/*
ini_set ("display_errors",1);
ini_set ("display_startup_errors",1);
error_reporting (-1);
*/
date_default_timezone_set ("America/New_York");

define ("DS",              DIRECTORY_SEPARATOR);
define ("TFP_DIR_NAME",    basename (dirname (__FILE__)));
define ("TFP_SITE_ROOT",   plugin_dir_path (__FILE__));
define ("TFP_WEB_ROOT",    plugins_url () . "/" . TFP_DIR_NAME . "/public_html/");
define ("TFP_VIEW_PATH",   TFP_SITE_ROOT . "resources" . DS . "view" . DS);
define ("TFP_CLASS_PATH",  TFP_SITE_ROOT . "resources" . DS . "class" . DS);

$tfp_config = array ();

$tfp_config ["feed"] = array (
    "archive-date"      => get_option ("tfp_archive_date"),
    "archive-summary"   => get_option ("tfp_archive_summary"),
    "daily-papers"      => get_option ("tfp_daily_papers"),
    "daily-status"      => get_option ("tfp_daily_status"),
    "top-ten"           => get_option ("tfp_top_ten")
);

$tfp_config ["rss"] = array (
    "top-ten"           => get_option ("tfp_top_ten_rss")
);

$tfp_config ["path"] = array (
    "cache"             => TFP_SITE_ROOT . "cache/"
);

$tfp_config ["url"] = array (
    "css"               => TFP_WEB_ROOT . "assets/css/",
    "js"                => TFP_WEB_ROOT . "assets/js/",
    "default-preview-thumbnail" => TFP_WEB_ROOT . "assets/img/default-thumbnail.jpg"
);

$tfp_config ["map"] = array (
    "key"               => get_option ("tfp_microsoft_bing_map_app_key")
);


/*
$tfp_config["feed"] = array(
    "archive-date"      => "http://www1.newseum.org/TFPAPI/JSON/GetArchive",
    "archive-summary"   => "http://www1.newseum.org/TFPAPI/JSON/GetArchiveSummary",
    "daily-papers"      => "http://www1.newseum.org/TFPAPI/JSON/GetPapersFP",
    "daily-status"      => "http://www1.newseum.org/TFPAPI/JSON/GetStatusFP",
    "top-ten"           => "http://www1.newseum.org/TFPAPI/JSON/GetTop10FP"
);
$tfp_config["bing"] = array(
    "key" => "AhqxplkQBqlfHX38_g7PQFoXG4u41rtg-vbEK92-FL9e7YrEevEE3QJ6jWDSGcoq"
);
*/
