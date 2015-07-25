<?php
defined ("DS") || define ("DS", DIRECTORY_SEPARATOR);

$helpers_config = array ();

$helpers_config ["dir"] = array ();
$helpers_config ["dir"] ["name"]  = "blackbaud-bootstrap-carousel";
$helpers_config ["dir"] ["root"]  = plugin_dir_path (__FILE__);
$helpers_config ["dir"] ["class"] = $helpers_config ["dir"] ["root"] . DS . "class" . DS;
$helpers_config ["dir"] ["view"]  = $helpers_config ["dir"] ["root"] . DS . "templates" . DS;

$helpers_config ["url"] = array ();
$helpers_config ["url"] ["root"] = plugins_url () . "/" . $helpers_config ["dir"] ["name"] . "/helpers/";
$helpers_config ["url"] ["css"]  = $helpers_config ["url"] ["root"] . "templates/css/";
