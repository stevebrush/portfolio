<?php
defined ("DS") || define ("DS", DIRECTORY_SEPARATOR);
$helpers_config = array ();
$helpers_config ["dir"] = array ();
$helpers_config ["url"] = array ();

# System paths.
$helpers_config ["dir"] ["root"]  = plugin_dir_path (__FILE__);
$helpers_config ["dir"] ["class"] = $helpers_config ["dir"] ["root"] . "class" . DS;
$helpers_config ["dir"] ["view"]  = $helpers_config ["dir"] ["root"] . "views" . DS;

# URL strings.
$helpers_config ["url"] ["assets"]  = plugins_url ("assets/", __FILE__);
