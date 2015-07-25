<?php
/*
Plugin Name: eTapestry DIY Forms
Description: Allows you to easily add eTapestry DIY iFrame code to your posts and pages. (Includes the iFrame resizer script.)
Author: Blackbaud, Inc.
Version: 0.1
*/

add_shortcode('etap_iframe', 'etap_iframe_shortcode');
function etap_iframe_shortcode($atts)
{
    /*
    Usage: [etap_iframe src="//app.etapestry.com/onlineforms/Vinfen/Donate.html"]
    */
	$txt = '<iframe class="etap-iframe" style="width: 100%; border: none;" src="' . $atts["src"] . '"></iframe>';
	$txt .= '<script>;(function($){$(function(){$(\'.etap-iframe\').responsiveIframe({xdomain:\'*\'});});})(jQuery);</script>';
	return $txt;
}

add_action("wp_footer", 'etap_enqueue_scripts');
function etap_enqueue_scripts()
{
    wp_enqueue_script('etap_responsive_iframe', plugins_url('responsive-iframe.jquery.js', __FILE__), array('jquery'));
}
