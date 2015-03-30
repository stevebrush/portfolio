<?php
/*
Plugin Name: Blackbaud: Online Express Forms
Description: An easier way to embed Online Express forms on your WordPress site.
Author: Blackbaud Interactive Services
Version: 1.0
Text Domain: olx_forms
*/


# Don't let anyone get here directly.
if (! defined ('ABSPATH')) exit;

# Include the Custom Post Type helpers.
include "resources/helpers/all.php";

# Add the Online Express libraries.
include "config.php";
include "functions.php";
include "resources/class/BlackbaudOnlineExpress.php";

# Set some stuff.
define ("OLXFORMS_PLUGIN_MAIN_FILE", __FILE__);

# Run with it!
$BlackbaudOnlineExpress = new BlackbaudOnlineExpress (new WP_BlackbaudFactory ($helpers_config));
