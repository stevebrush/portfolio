<?php
defined ("DS") || define ("DS", DIRECTORY_SEPARATOR);
$helpers_config = array ();
$helpers_config ["dir"] = array ();
$helpers_config ["url"] = array ();


# Set the name of the parent plugin's directory.
$helpers_config ["dir"] ["name"]  = "blackbaud-olx";


# System paths.
$helpers_config ["dir"] ["root"]  = plugin_dir_path (__FILE__);
$helpers_config ["dir"] ["class"] = $helpers_config ["dir"] ["root"] . DS . "class" . DS;
$helpers_config ["dir"] ["view"]  = $helpers_config ["dir"] ["root"] . DS . "templates" . DS;


# URL strings.
$helpers_config ["url"] ["root"] = plugins_url () . "/" . $helpers_config ["dir"] ["name"] . "/resources/helpers/";
$helpers_config ["url"] ["css"]  = $helpers_config ["url"] ["root"] . "templates/css/";
