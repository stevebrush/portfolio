<?php
class WP_BlackbaudFactory extends WP_BlackbaudPlugin {

	private $config;

	public function __construct (Array $options = array ()) {

		$this-> config = $options;

		$this-> CheckAdmin ();

		# WordPress dashboard, only.
		if ($this-> isAdmin) {

			add_action ("init", array ($this, "RegisterDashboardAssets"));
			add_action ("admin_enqueue_scripts", array ($this, "PrintDashboardAssets"));

		}
	}

	public function Config ($key, $value) {
		return $this-> config [$key] [$value];
	}

	public function Create ($className, $options) {
		return new $className ($options, $this);
	}

	public function RegisterDashboardAssets () {
		wp_register_style ("bbi_helpers_dashboard_styles", $this-> Config ("url", "assets") . "css/dashboard-styles.css");
		wp_register_script ("bbi_helpers_dashboard_scripts", $this-> Config ("url", "assets") . "js/dashboard-scripts.js", array ("jquery"));
	}

	public function PrintDashboardAssets () {

		# Load all assets required for the Media Gallery picker.
		wp_enqueue_media ();

		# Load our own, custom assets.
		wp_enqueue_style ("bbi_helpers_dashboard_styles");
		wp_enqueue_script ("bbi_helpers_dashboard_scripts");
	}

}
