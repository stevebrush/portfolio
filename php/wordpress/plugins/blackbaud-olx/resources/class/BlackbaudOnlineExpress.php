<?php
class BlackbaudOnlineExpress {

	private static $className = "BlackbaudOnlineExpress";
	private static $slug = "olx_forms";

	public static function Start () {

		# First, create the custom post type.
		$cpt = new CustomPostType (self:: $slug, array (
			"public" => true,
			"labels" => array (
				"name" => __ ("OLX Forms", self:: $slug),
				"singular_name" => __ ("Form", self:: $slug),
				"menu_name" => _x ("OLX Forms", "admin menu", self:: $slug),
				"name_admin_bar" => _x ("OLX Form", "add new on admin bar", self:: $slug),
				"add_new" => _x ("Add New", "form", self:: $slug),
				"add_new_item" => __ ("Add New Form", self:: $slug),
				"new_item" => __ ("New Form", self:: $slug),
				"edit_item" => __ ("Edit Form", self:: $slug),
				"view_item" => __ ("View Form", self:: $slug),
				"all_items" => __ ("All Forms", self:: $slug),
				"search_items" => __ ("Search OLX Forms", self:: $slug),
				"parent_item_colon" => __ ("Parent Forms:", self:: $slug),
				"not_found" => __ ("No forms found.", self:: $slug),
				"not_found_in_trash" => __ ("No OLX forms found in Trash.", self:: $slug)
			),
			"description" => __ ("Stores the embed code for your various Online Express forms.", self:: $slug),
			"supports" => array ("title", "editor")
		));

		# Provide the path to this plugin's main file.
		# We do this so we can refresh any friendly URLs associated with our post types,
		# once this plugin is activated.
		$cpt->Set ("pluginMainFile", OLXFORMS_PLUGIN_MAIN_FILE);

		# Let's register a new meta box for this post type.
		$metaBox = new PostMetaBox (array (
			"postType" => $cpt->slug,
			"slug"     => "settings",
			"label"    => "Form Settings"
		));

			# Add some fields to it.
			$metaBox->AddField (array (
				"slug"  => "embed_code",
				"label" => __ ("Embed Code:", self:: $slug),
				"type"  => "textarea",
				"attr"  => array (
					"class" => "form-control accepts-code",
					"maxlength" => "5000"
				)
			));

			$metaBox->AddField (array (
				"slug" => "html_after",
				"label" => __ ("HTML After:", self:: $slug),
				"type" => "textarea",
				"attr"  => array (
					"class" => "form-control accepts-code",
					"maxlength" => "5000"
				)
			));

			# Build it to the page!
			$metaBox->Build ();

		# Add columns to the dashboard view:
		$cpt->Columns (array (
			"olx_form_id" => array (
				"label" => __ ("Form ID", self:: $slug),
				"value" => '$post_id'
			),
			"olx_form_shortcode" => array (
				"label" => __ ("Shortcode", self:: $slug),
				"value" => '\'<code>[olx_form form_id="\' . $post_id . \'"]</code>\''
			)
		));


		# WordPress dashboard, only.
		if (is_admin ()) {
			add_action ("init", array (self::$className, "RegisterDashboardAssets"));
			add_action ("admin_enqueue_scripts", array (self::$className, "PrintDashboardAssets"));
		} else {
			add_shortcode ("olx_form", array (self::$className, "AddShortcode"));
			//add_action ("init", array (self::$className, "RegisterFrontEndAssets"));
			//add_action ("wp_print_styles", array (self::$className, "PrintFrontEndStyles"));
			add_action ("wp_footer", array (self:: $className, "PrintFrontEndScripts"));
		}
	}

	public static function AddShortcode ($atts) {
		extract (shortcode_atts (array ("form_id" => "0"), $atts));
		ob_start ();
		the_olx_form ($form_id);
		return ob_get_clean ();
	}

	public static function RegisterDashboardAssets () {
		wp_register_style ("olx_forms_dashboard_styles", OLXFORMS_CSS_URL . "dashboard-styles.css");
	}

	public static function PrintDashboardAssets () {
		wp_enqueue_style ("olx_forms_dashboard_styles");
	}

	public static function RegisterFrontEndAssets () {
		//wp_register_style ("olol_front_end_styles", OLOL_WEB_ROOT . "css/style.css");
	}

	public static function PrintFrontEndStyles () {
		//wp_enqueue_style ("olol_front_end_styles");
	}

	public static function PrintFrontEndScripts () {
		ob_start ();
		include OLXFORMS_RESOURCE_PATH . "view/bbi-namespace.php";
		echo ob_get_clean ();
	}

}
