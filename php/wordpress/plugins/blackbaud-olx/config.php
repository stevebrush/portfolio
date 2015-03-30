<?php
# No one gets here expect me.
if (! defined ("ABSPATH")) exit;

# Error reporting.
ini_set ("display_errors", 1);
ini_set ("display_startup_errors", 1);
error_reporting (-1);

# Various.
date_default_timezone_set ("America/New_York");

# Keys.
defined ("DS") || define ("DS", DIRECTORY_SEPARATOR);

# Define paths.
define ("OLXFORMS_RESOURCE_PATH", plugin_dir_path (__FILE__) . "resources" . DS);
define ("OLXFORMS_CLASS_PATH", OLXFORMS_RESOURCE_PATH . "class" . DS);

# Define URL's.
define ("OLXFORMS_WEB_ROOT", plugins_url () . "/" . basename (dirname (__FILE__)) . "/public_html/");
define ("OLXFORMS_CSS_URL", OLXFORMS_WEB_ROOT . "css/");
define ("OLXFORMS_JS_URL", OLXFORMS_WEB_ROOT . "js/");
