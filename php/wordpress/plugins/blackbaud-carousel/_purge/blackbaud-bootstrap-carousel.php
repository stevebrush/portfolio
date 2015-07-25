<?php
/*
Plugin Name: Blackbaud Bootstrap Carousel
Plugin URI: http://www.blackbaud.com/
Description: A plugin based heavily off of the plugin <a href="http://wordpress.org/plugins/cpt-bootstrap-carousel/" target="_blank">CPT Bootstrap Carousel</a>, this version utilizes the rich-text editor for the caption, instead of the post excerpt.
Version: 2.0.0
Author: Blackbaud Interactive
Text Domain: bb-bootstrap-carousel
*/

# Set some stuff.
define ("BLACKBAUD_CAROUSEL_PLUGIN_MAIN_FILE", __FILE__);
define ("BLACKBAUD_CAROUSEL_POST_TYPE", "blackbaud_carousel");

include "helpers/all.php";
include "class/BlackbaudCarousel.php";
include "class/BlackbaudCarouselSettings.php";
include "functions.php";

BlackbaudCarousel:: Start (new BlackbaudCPT ($helpers_config));

if (is_admin ()) {
    $BlackbaudCarouselSettings = new BlackbaudCarouselSettings ();
}
