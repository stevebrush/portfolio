<?php
include "config.php";

if (! class_exists ("WP_BlackbaudFactory")) {
	include $helpers_config ["dir"] ["class"] . "WP_BlackbaudPlugin.php";
	include $helpers_config ["dir"] ["class"] . "WP_BlackbaudFactory.php";
	include $helpers_config ["dir"] ["class"] . "CustomPostType.php";
	include $helpers_config ["dir"] ["class"] . "PostMetaBox.php";
	include $helpers_config ["dir"] ["class"] . "PostMetaField.php";
}
