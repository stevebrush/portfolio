<?php
function getLoApp() {
	global $loApp;
	return $loApp;
}

function getLoConst() {
	global $loConst;
	return $loConst;
}

function getLoPage() {
	global $loPage;
	return $loPage;
}

function print_f($arr) {
	echo "<pre style=\"height:200px;overflow:auto;border:1px solid #ccc;background:#f2f1f0;margin:0 0 15px;\">";
	print_r($arr);
	echo "</pre>";
}

function lo_add_menu_options() {
	global $loPage;
	add_meta_box("lo_menu_box", __("Luminate Forms Menu", "lo_form"), array($loPage, "createMenuOptions"), "nav-menus", "side", "high");
}

function pre_process_shortcode() {

	if (!is_singular()) {
		return;
	}
	
	global $post;
	global $loApp;
	global $loConst;
	
	if (!empty($post->post_content)) {
	
		$regex = get_shortcode_regex();
		$loConst->checkLogin();
		$isLoggedIn = $loConst->isLoggedIn;
		
		preg_match_all("/".$regex."/", $post->post_content, $matches);
		
		if (!empty($matches[2])) {
			
			if ($isLoggedIn) {
			
				// Login
				if (in_array("luminate-form-login", $matches[2])) {
					$loApp->redirectTo($loApp->config("url","return-user"));
				} 
				
				// Registration
				else if (in_array("luminate-form-register", $matches[2])) {
					$loApp->redirectTo($loApp->config("url","return-user"));
				}
				
			} else {
				// Profile
				if (in_array("luminate-form-profile", $matches[2])) {
					$loApp->redirectTo($loApp->config("url","login"));
				}
			}
		}
	}
}

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function check_for_plugin_update($checked_data) {
	
	global $wp_version;
	
	// [ ! ] Comment this block out during testing
	if (empty($checked_data->checked)) {
		return $checked_data;
	}
	
	$args = array(
		"slug" => LO_PLUGIN_SLUG,
		"version" => $checked_data->checked[ LO_PLUGIN_SLUG . "/" . LO_PLUGIN_SLUG . ".php" ]
	);
	
	$request_string = array(
		"body" => array(
			"action" => "basic_check", 
			"request" => serialize($args),
			"api-key" => md5(get_bloginfo("url"))
		),
		"user-agent" => "WordPress/" . $wp_version . "; " . get_bloginfo("url")
	);
	
	// Start checking for an update
	$raw_response = wp_remote_post(LO_UPDATER_URL, $request_string);
	
	if (!is_wp_error($raw_response) && ($raw_response["response"]["code"] == 200)) {
		$response = unserialize($raw_response["body"]);
	}
	
	if (is_object($response) && !empty($response)) {
		// Feed the update data into WP Updater
		$checked_data->response[ LO_PLUGIN_SLUG . "/" . LO_PLUGIN_SLUG . ".php" ] = $response;
	}
	
	return $checked_data;
}

function plugin_api_call($def, $action, $args) {

	global $wp_version;
	
	if (!isset($args->slug) || ($args->slug != LO_PLUGIN_SLUG)) {
		return false;
	}
	
	// Get the current version
	$plugin_info = get_site_transient("update_plugins");
	$current_version = $plugin_info->checked[ LO_PLUGIN_SLUG . "/" . LO_PLUGIN_SLUG . ".php" ];
	$args->version = $current_version;
	
	$request_string = array(
		"body" => array(
			"action" => $action, 
			"request" => serialize($args),
			"api-key" => md5(get_bloginfo("url"))
		),
		"user-agent" => "WordPress/" . $wp_version . "; " . get_bloginfo("url")
	);
	
	$request = wp_remote_post(LO_UPDATER_URL, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error("plugins_api_failed", __("An Unexpected HTTP Error occurred during the API request.</p> <p><a href=\"?\" onclick=\"document.location.reload(); return false;\">Try again</a>"), $request->get_error_message());
	} else {
		$res = unserialize($request["body"]);
		if ($res === false) {
			$res = new WP_Error("plugins_api_failed", __("An unknown error occurred"), $request["body"]);
		}
	}
	
	return $res;
}
