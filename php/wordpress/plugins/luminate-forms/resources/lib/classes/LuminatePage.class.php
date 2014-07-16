<?php
/*
	This class interacts with WordPress, 
	initializing shortcodes and the admin interface
*/
class LuminatePage {

	private $app;
	private $cons;
	private $doLoadScripts = false;
	
	public function __construct(LuminateApplication $app, LuminateConstituent $cons) {
		$this->app = $app;
		$this->cons = $cons;
	}
	
	public function shortcodeLogin() {
		$loApp = $this->app;
		$this->doLoadScripts = true;
		ob_start();
			require LO_FORM_PATH."login.form.php";
			$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function shortcodeRegister() {
		$loApp = $this->app;
		$this->doLoadScripts = true;
		ob_start();
			require LO_FORM_PATH."register.form.php";
			$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function shortcodeDonate($atts) {
		extract(shortcode_atts(array(
			"form_id" => "0",
			"heading" => "General Donation",
			"giving_levels_heading" => "Giving Levels",
			"donor_information_heading" => "Donor Information",
			"billing_information_heading" => "Billing Information",
			"payment_information_heading" => "Payment Information"
		), $atts));
		$loApp = $this->app;
		$loConst = $this->cons;
		$this->doLoadScripts = true;
		ob_start();
			if (isset($_GET["transaction_id"])) {
				$confirmation_code = $_GET["confirmation_code"];
				$amount = $_GET["amount"];
				require LO_PAGES_PATH."donate-confirmation.page.php";
			} else {
				require LO_FORM_PATH."donate.form.php";
			}
			$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function shortcodeResetPassword() {
		$loApp = $this->app;
		$this->doLoadScripts = true;
		ob_start();
			require LO_FORM_PATH."reset-password.form.php";
			$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function shortcodeProfile() {
		$loApp = $this->app;
		$loConst = $this->cons;
		$loConst->checkLogin();
		$this->doLoadScripts = true;
		ob_start();
			require LO_FORM_PATH."profile.form.php";
			$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function shortcodeSurvey($atts) {
		//return "Survey form not available.";
		extract(shortcode_atts(array(
			"form_id" => "0",
			"form_title" => "Survey"
		), $atts));
		if (isset($this->cons)) {
			$loApp = $this->app;
			$loConst = $this->cons;
			$this->doLoadScripts = true;
			ob_start();
				require LO_FORM_PATH."survey.form.php";
				$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		} else {
			return "";
		}
	}
	
	public function createMenuOptions() {
		global $_nav_menu_placeholder, 
			$nav_menu_selected_id;
		$_nav_menu_placeholder = (0 > $_nav_menu_placeholder) ? $_nav_menu_placeholder - 1 : -1;
		$loApp = $this->app;
		ob_start();
		require LO_ADMIN_PATH."menu-widget.admin.php";
		$contents = ob_get_contents();
		ob_end_clean();
		echo $contents;
	}
	
	public function registerScripts() {
		//wp_register_script("luminate-forms", $this->app->config("dir","js")."luminate-forms.js");
		//wp_register_script("luminate-form-plugin", $this->app->config("dir","js")."jquery.luminateForm.js");
		//wp_register_script("luminate-safe-console", $this->app->config("dir","js")."safeConsole.js");
		//wp_register_script("luminate-inputMask-plugin", $this->app->config("dir","js")."jquery.inputMask.js");
		wp_register_script("luminate-extend", $this->app->config("dir","js")."luminateExtend/luminateExtend.js");
		wp_register_script("luminate-extend-helpers", $this->app->config("dir","js")."luminateExtend-helpers.js");
	}
	
	public function registerStyles() {
		wp_register_style("luminate-form-styles", $this->app->config("dir","css")."form.css");
	}
	
	public function printScripts() {
		$this->surfaceApiInfo();
		//wp_enqueue_script("luminate-forms");
		if ($this->doLoadScripts) {
			//wp_enqueue_script("luminate-form-plugin");
			//wp_enqueue_script("luminate-safe-console");
			//wp_enqueue_script("luminate-inputMask-plugin");
			wp_enqueue_script("luminate-extend");
			wp_enqueue_script("luminate-extend-helpers");
		}
	}
	
	public function printStyles() {
		wp_enqueue_style("luminate-form-styles");
	}
	
	public function registerWidgets() {
		register_widget("LuminateSurveyWidget");
		register_widget("LuminateProfileMenuWidget");
	}
	
	public function pluginMenu() {
		add_menu_page("Luminate Forms Configuration", "Luminate Forms", "manage_options", "luminate-forms-config", array($this,"pluginOptions"));
		add_submenu_page("luminate-forms-config", "API Settings", "API Settings", "manage_options", "luminate-forms-config-api", array($this,"pluginOptionsApi"));
		add_submenu_page("luminate-forms-config", "Permalinks", "Permalinks", "manage_options", "luminate-forms-config-permalinks", array($this,"pluginOptionsPermalinks"));
	}
	
	public function pluginOptions() {
		if (!current_user_can("manage_options")) wp_die(__("You do not have sufficient permissions to access this page."));
		$loApp = $this->app;
		ob_start();
			require LO_ADMIN_PATH."plugin-options-landing.admin.php";
			$contents = ob_get_contents();
		ob_end_clean();
		echo $contents;
	}
	
	public function pluginOptionsApi() {
		if (!current_user_can("manage_options")) wp_die(__("You do not have sufficient permissions to access this page."));
		$apiKey_name 		= "lo_api_key";
		$apiKey_val 		= get_option($apiKey_name);
		
		$apiSecret_name 	= "lo_api_secret";
		$apiSecret_val 		= get_option($apiSecret_name);
		
		$apiUser_name 		= "lo_api_user";
		$apiUser_val 		= get_option($apiUser_name);
		
		$apiPassword_name 	= "lo_api_password";
		$apiPassword_val 	= get_option($apiPassword_name);
		
		$apiVersion_name 	= "lo_api_version";
		$apiVersion_val 	= get_option($apiVersion_name);
		
		$apiUrl_name 		= "lo_api_url";
		$apiUrl_val 		= get_option($apiUrl_name);
		
		$apiUrlSecure_name 	= "lo_api_url_secure";
		$apiUrlSecure_val 	= get_option($apiUrlSecure_name);
		
		if (isset($_POST[$apiKey_name])) {
		
			$apiKey_val = $_POST[$apiKey_name];
			update_option($apiKey_name, $apiKey_val);
			
			if ($_POST[$apiSecret_name] != "123abc$@%") {
				$apiSecret_val = $_POST[$apiSecret_name];
				update_option($apiSecret_name, $apiSecret_val);
			}
			
			$apiUser_val = $_POST[$apiUser_name];
			update_option($apiUser_name, $apiUser_val);
			
			if ($_POST[$apiPassword_name] != "123abc$@%") {
				$apiPassword_val = $_POST[$apiPassword_name];
				update_option($apiPassword_name, $apiPassword_val);
			}
			
			$apiVersion_val = $_POST[$apiVersion_name];
			update_option($apiVersion_name, $apiVersion_val);
			
			$apiUrl_val = $_POST[$apiUrl_name];
			update_option($apiUrl_name, $apiUrl_val);
			
			$apiUrlSecure_val = $_POST[$apiUrlSecure_name];
			update_option($apiUrlSecure_name, $apiUrlSecure_val);
			
			print("<div class=\"updated\"><p><strong>Settings saved.</strong></p></div>");
		}
		$loApp = $this->app;
		ob_start();
			require LO_ADMIN_PATH."plugin-options-api.admin.php";
			$contents = ob_get_contents();
		ob_end_clean();
		echo $contents;
	}
	
	public function pluginOptionsPermalinks() {
		if (!current_user_can("manage_options")) wp_die(__("You do not have sufficient permissions to access this page."));
		
		$loginPageId_name = "lo_page_id_login";
		$loginPagePermalink_name = "lo_page_permalink_login";
		$loginPageId_val = get_option($loginPageId_name);
		
		$logoutPageId_name = "lo_page_id_logout";
		$logoutPagePermalink_name = "lo_page_permalink_logout";
		$logoutPageId_val = get_option($logoutPageId_name);
		
		$returnUserPageId_name = "lo_page_id_return_user";
		$returnUserPagePermalink_name = "lo_page_permalink_return_user";
		$returnUserPageId_val = get_option($returnUserPageId_name);
		
		$registerPageId_name = "lo_page_id_register";
		$registerPagePermalink_name = "lo_page_permalink_register";
		$registerPageId_val = get_option($registerPageId_name);
		
		$resetPasswordPageId_name = "lo_page_id_reset_password";
		$resetPasswordPagePermalink_name = "lo_page_permalink_reset_password";
		$resetPasswordPageId_val = get_option($resetPasswordPageId_name);
		
		$profilePageId_name = "lo_page_id_profile";
		$profilePagePermalink_name = "lo_page_permalink_profile";
		$profilePageId_val = get_option($profilePageId_name);
		
		if (isset($_POST[$loginPageId_name])) {
		
			$loginPageId_val = $_POST[$loginPageId_name];
			$loginPagePermalink_val = get_permalink($loginPageId_val);
			update_option($loginPageId_name, $loginPageId_val);
			update_option($loginPagePermalink_name, $loginPagePermalink_val);
			
			$logoutPageId_val = $_POST[$logoutPageId_name];
			$logoutPagePermalink_val = get_permalink($logoutPageId_val);
			update_option($logoutPageId_name, $logoutPageId_val);
			update_option($logoutPagePermalink_name, $logoutPagePermalink_val);
			
			$returnUserPageId_val = $_POST[$returnUserPageId_name];
			$returnUserPagePermalink_val = get_permalink($returnUserPageId_val);
			update_option($returnUserPageId_name, $returnUserPageId_val);
			update_option($returnUserPagePermalink_name, $returnUserPagePermalink_val);
			
			$registerPageId_val = $_POST[$registerPageId_name];
			$registerPagePermalink_val = get_permalink($registerPageId_val);
			update_option($registerPageId_name, $registerPageId_val);
			update_option($registerPagePermalink_name, $registerPagePermalink_val);
			
			$resetPasswordPageId_val = $_POST[$resetPasswordPageId_name];
			$resetPasswordPagePermalink_val = get_permalink($resetPasswordPageId_val);
			update_option($resetPasswordPageId_name, $resetPasswordPageId_val);
			update_option($resetPasswordPagePermalink_name, $resetPasswordPagePermalink_val);
			
			$profilePageId_val = $_POST[$profilePageId_name];
			$profilePagePermalink_val = get_permalink($profilePageId_val);
			update_option($profilePageId_name, $profilePageId_val);
			update_option($profilePagePermalink_name, $profilePagePermalink_val);
			
			print("<div class=\"updated\"><p><strong>Settings saved.</strong></p></div>");
		}
		$loApp = $this->app;
		ob_start();
			require LO_ADMIN_PATH."plugin-options-permalinks.admin.php";
			$contents = ob_get_contents();
		ob_end_clean();
		echo $contents;
	}
	
	private function surfaceApiInfo() {
		echo "<script id=\"lo-api-object\">";
		echo "window.loApiData = { key: '{$this->app->config('api','key')}', user: '{$this->app->config('api','login-name')}', password: '{$this->app->config('api','login-password')}', version: '{$this->app->config('api','version')}', http: '{$this->app->config('api','http')}', https: '{$this->app->config('api','https')}' };";
		echo "loApiData.permalinks = { login: '{$this->app->config('url','login')}', resetPassword: '{$this->app->config('url','reset-password')}', register: '{$this->app->config('url','register')}', profile: '{$this->app->config('url','profile')}', returnUserLanding: '{$this->app->config('url','return-user-landing')}' };";
		echo "(function ($) {";
		echo "    $(function() {";
		echo "        luminateExtend.init({";
		echo "            apiKey: \"{$this->app->config('api', 'key')}\",";
		echo "            path: {";
		echo "                nonsecure: \"{$this->app->config('api', 'http')}\",";
		echo "                secure: \"{$this->app->config('api', 'https')}\"";
		echo "            }";
		echo "        });";
		echo "    });";
		echo "}(jQuery));";
		echo "</script>";
	}
}