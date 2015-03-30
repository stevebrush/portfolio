<?php
class BlackbaudOnlineExpress {

	private $slug = "olx_forms";
	private $defaults = array (
		"active" => "false",
		"activateOnLoad" => "true",
		"buttonLabel" => "Share",
		"buttonIcon" => "share-alt",
		"includeDefaultStyles" => "1",
		"includeBootstrap" => "1",
		"includeFontAwesome" => "1",
		"introductionTitle" => "Share Your Contribution",
		"introductionBody" => "Please take some time to share with your friends and family how you supported this organization.",
		"shareTitle" => "",
		"shareSummary" => "",
		"shareUrl" => "",
		"shareImage" => "",
		"shareThisPublisherId" => ""
	);
	private $factory;
	private $MCE_ID = "OLXFormsMCEButton";
	private $settingsSlug = "olx_forms_settings";

	public function __construct (WP_BlackbaudFactory $factory) {

		$this->factory = $factory;

		$this->CreatePostTypes ();

		# WordPress dashboard, only.
		if (is_admin ()) {
			add_action ("admin_init", array ($this, "RegisterDashboardAssets"));
			add_action ("admin_init", array ($this, "EditorButton"));
			add_action ("admin_enqueue_scripts", array ($this, "PrintDashboardAssets"));
			add_action ("admin_footer", array ($this, "PrintGlobalAssets"));
			add_action ("admin_menu", array ($this, "DashboardMenu"));
			add_action ("admin_init", array ($this, "DashboardMenu_RegisterFields"));
		} else {
			add_shortcode ("olx_form", array ($this, "AddShortcode"));
			add_action ("init", array ($this, "RegisterFrontEndAssets"));
			add_action ("wp_print_styles", array ($this, "PrintFrontEndStyles"));
			add_action ("wp_head", array ($this, "PrintMetaTags"));
			add_action ("wp_footer", array ($this, "PrintGlobalAssets"));
			add_action ("wp_footer", array ($this, "PrintFrontEndScripts"));
		}

	}

	public function EditorButton () {
		if (current_user_can ("edit_posts") && current_user_can ("edit_pages")) {
			add_filter ("mce_buttons", array ($this, "MCE_AddButton"));
			add_filter ("mce_external_plugins", array ($this, "MCE_AddPlugin"));
		}
	}

		public function MCE_AddButton ($buttons) {
			# Add a separation before our button.
			array_push ($buttons, "|", $this->MCE_ID);
			return $buttons;
		}

		public function MCE_AddPlugin ($plugins) {

			$data = array ();

			$args = array (
				'post_type' => $this->slug,
				'post_status' => 'publish',
				'posts_per_page' => '-1',
				'ignore_sticky_posts'=> 1,
				'orderby' => 'title',
				'order' => 'ASC'
			);

			$query = new WP_Query ($args);

			if ($query->have_posts ()) {
				while ($query->have_posts ()) {
					$query->the_post ();
					$data [] = array (
						"title" => get_the_title(),
						"shortcode" => '<!-- ' . get_the_title() . ' -->[olx_form form_id="' . get_the_ID() . '"]'
					);
				}
			}

			echo '<script>window.OnlineExpressFormsData = window.OnlineExpressFormsData || {}; window.OnlineExpressFormsData.forms = ' . json_encode($data) . ';</script>';

			//echo '<div data-bbi-app="OnlineExpressForms" data-bbi-action="MCEButton" data-id="' . $this->MCE_ID . '"></div>';
			// This plugin file will work the magic of our button.
			$plugins [$this->MCE_ID] = OLXFORMS_JS_URL . "mce-button.js";
			return $plugins;
		}

	public function AddShortcode ($atts) {
		extract (shortcode_atts (array ("form_id" => "0"), $atts));
		ob_start ();
		the_olx_form ($form_id);
		return ob_get_clean ();
	}

	public function CreatePostTypes () {

		# First, create the custom post type.
		$cpt = $this->factory->Create("CustomPostType", array (

			"slug" => $this->slug,
			"public" => true,
			"labels" => array (
				"name" => __ ('Online Express Forms', $this->slug),
				"singular_name" => __ ("Form", $this->slug),
				"menu_name" => _x ("Online Express Forms", "admin menu", $this->slug),
				"menu_icon" => "dashicons-slides",
				"name_admin_bar" => _x ("Online Express Form", "add new on admin bar", $this->slug),
				"add_new" => _x ("Add New", "form", $this->slug),
				"add_new_item" => __ ("Add New Form", $this->slug),
				"new_item" => __ ("New Form", $this->slug),
				"edit_item" => __ ("Edit Form", $this->slug),
				"view_item" => __ ("View Form", $this->slug),
				"all_items" => __ ("All Forms", $this->slug),
				"search_items" => __ ("Search Online Express Forms", $this->slug),
				"parent_item_colon" => __ ("Parent Forms:", $this->slug),
				"not_found" => __ ("No forms found.", $this->slug),
				"not_found_in_trash" => __ ("No Online Express Forms found in Trash.", $this->slug)
			),
			"description" => __ ("Stores the embed code for your various Online Express forms.", $this->slug),
			"supports" => array ("title")

		));

		//$cpt->RemoveSlugFromPermalink();

		# Provide the path to this plugin's main file.
		# We do this so we can refresh any friendly URLs associated with our post types,
		# once this plugin is activated.
		$cpt->Set ("pluginMainFile", OLXFORMS_PLUGIN_MAIN_FILE);

		# Let's register a new meta box for this post type.
		$metaBox = $this->factory-> Create ("PostMetaBox", array (
			"postType" => $cpt->slug,
			"slug"     => "settings",
			"label"    => "Form Settings"
		));

			# Add some fields to it.
			$metaBox->AddField (array (
				"slug"  => "embed_code",
				"label" => __ ("Embed Code:", $this->slug),
				"type"  => "textarea",
				"attr"  => array (
					"class" => "form-control accepts-code",
					"maxlength" => "5000"
				)
			));

			$metaBox->AddField (array (
				"slug" => "html_after",
				"label" => __ ("HTML After:", $this->slug),
				"type" => "textarea",
				"attr"  => array (
					"class" => "form-control accepts-code",
					"maxlength" => "5000"
				)
			));

			# Build it to the page!
			$metaBox->Build ();

		# Let's register a new meta box for this post type.
		$metaBox2 = $this->factory-> Create ("PostMetaBox", array (
			"postType" => $cpt->slug,
			"slug"     => "social_sharing",
			"label"    => "Social Sharing",
			"context"  => "side"
		));

			# Add some fields to it.
			$metaBox2->AddField (array (
				"slug"  => "social_sharing",
				"label" => __ ("Allow social sharing on confirmation screen", $this->slug),
				"type"  => "checkbox",
				"attr"  => array (
					"class" => "form-control"
				),
				"parentAttr" => array (
					"data-checkbox-group-selector" => ".blackbaud-confirmation-social-sharing"
				)
			));

			$metaBox2->AddField (array (
				"slug"  => "social_sharing_activate_on_load",
				"label" => __ ("Auto-display lightbox.", $this->slug),
				"type"  => "checkbox",
				"attr"  => array (
					"class" => "form-control"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));

			$metaBox2->AddField (array (
				"slug"  => "social_sharing_intro_title",
				"label" => __ ("Lightbox: Title", $this->slug),
				"type"  => "text",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "500"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));

			$metaBox2->AddField (array (
				"slug"  => "social_sharing_intro_description",
				"label" => __ ("Lightbox: Body Text (accepts HTML)", $this->slug),
				"type"  => "textarea",
				"attr"  => array (
					"class" => "form-control accepts-code",
					"maxlength" => "5000"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));

			$metaBox2->AddField (array (
				"slug"  => "social_sharing_share_title",
				"label" => __ ("Sharing Title (default)", $this->slug),
				"type"  => "text",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "500"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));

			$metaBox2->AddField (array (
				"slug"  => "social_sharing_share_summary",
				"label" => __ ("Sharing Summary (default)", $this->slug),
				"type"  => "textarea",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "1500"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));
			$metaBox2->AddField (array (
				"slug"  => "social_sharing_share_url",
				"label" => __ ("Sharing URL", $this->slug),
				"type"  => "text",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "1500",
					"placeholder" => "http://"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));
			$metaBox2->AddField (array (
				"slug"  => "social_sharing_share_image",
				"label" => __ ("Sharing Image", $this->slug),
				"type"  => "media-gallery-picker",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "1500",
					"placeholder" => "http://"
				),
				"parentAttr" => array (
					"class" => "form-group blackbaud-confirmation-social-sharing"
				)
			));

			# Build it to the page!
			$metaBox2->Build ();

		# Add columns to the dashboard view:
		$cpt->Columns (array (
			"olx_form_id" => array (
				"label" => __ ("Form ID", $this->slug),
				"value" => '$post_id'
			),
			"olx_form_shortcode" => array (
				"label" => __ ("Shortcode", $this->slug),
				"value" => '\'<code class="olx-forms-selectable" title="Click to select the shortcode. Ctrl+C (or Cmd+C) to copy to your clipboard.">[olx_form form_id="\' . $post_id . \'"]</code>\''
			)
		));
	}

	public function DashboardMenu () {
		add_submenu_page ("edit.php?post_type={$this->slug}", "Settings", "Settings", "manage_options", $this->settingsSlug, array ($this, "DashboardMenu_Settings"));
	}

		public function DashboardMenu_Settings () {
			if (! current_user_can ("manage_options")) {
				wp_die (__ ("You do not have sufficient permissions to access this page."));
			}
			$data = array (
				"pageId" => $this->settingsSlug
			);
			ob_start();
			require OLXFORMS_RESOURCE_PATH . "view/settings.php";
			echo ob_get_clean();
		}

			public function DashboardMenu_SettingsValidation ($plugin_options) {
				return $plugin_options;
			}

			public function DashboardMenu_SettingsSection_SocialSharing () {}

			public function DashboardMenu_SettingsSection_Optimizations () {}

			public function DashboardMenu_SettingsField ($args) {

				$options = get_option ($this->settingsSlug);
				$html = "";

				if (isset($options[$args["slug"]])) {
					$value = $options[$args["slug"]];
				} else if (isset($this->defaults[$args["slug"]])) {
					$value = $this->defaults[$args["slug"]];
				} else {
					$value = "";
				}

				switch ($args ["type"]) {
					case "text":
					default:
						$html = '<input name="' . $this->settingsSlug . '[' . $args["slug"] . ']" id="' . $args["label_for"] . '" type="text" value="' . $value . '">';
						break;
					case "checkbox":
						$optionChecked = isset ($options[$args["slug"]]) ? $options[$args["slug"]] : "0";
						$html = '<label><input name="' . $this->settingsSlug . '[' . $args["slug"] . ']" id="' . $args["label_for"] . '" type="checkbox" value="1" ' . checked ($optionChecked, 1, false) . '> ' . $args["label"] . '</label>';
						break;
				}

				echo $html;

			}

		public function DashboardMenu_RegisterFields () {

			// Un-comment to reset the options.
			// delete_option ($this->settingsSlug);

			register_setting ($this->settingsSlug, $this->settingsSlug, array ($this, "DashboardMenu_SettingsValidation"));

				if (false === ($options = get_option ($this->settingsSlug))) {
		            $options = $this->defaults;
		        }

		        update_option ($this->settingsSlug, $options);

				add_settings_section ($this->slug . "_social_sharing", "Social Sharing", array ($this, "DashboardMenu_SettingsSection_SocialSharing"), $this->settingsSlug);
					add_settings_field ("shareThisPublisherId", "ShareThis Publisher ID:", array ($this, "DashboardMenu_SettingsField"), $this->settingsSlug, $this->slug . "_social_sharing", array (
						"label_for" => "shareThisPublisherId",
						"type" => "text",
						"slug" => "shareThisPublisherId"
					));
					add_settings_field ("buttonLabel", "Share Button Label:", array ($this, "DashboardMenu_SettingsField"), $this->settingsSlug, $this->slug . "_social_sharing", array (
						"label_for" => "buttonLabel",
						"type" => "text",
						"slug" => "buttonLabel"
					));
					add_settings_field ("buttonIcon", "Share Button Icon Suffix (Font Awesome):", array ($this, "DashboardMenu_SettingsField"), $this->settingsSlug, $this->slug . "_social_sharing", array (
						"label_for" => "buttonIcon",
						"type" => "text",
						"slug" => "buttonIcon"
					));
				add_settings_section ($this->slug . "_optimizations", "Optimizations", array ($this, "DashboardMenu_SettingsSection_Optimizations"), $this->settingsSlug);
					add_settings_field ("includeDefaultStyles", "", array ($this, "DashboardMenu_SettingsField"), $this->settingsSlug, $this->slug . "_optimizations", array (
						"label_for" => "includeDefaultStyles",
						"type" => "checkbox",
						"slug" => "includeDefaultStyles",
						"label" => "Include default styles"
					));
					add_settings_field ("includeBootstrap", "", array ($this, "DashboardMenu_SettingsField"), $this->settingsSlug, $this->slug . "_optimizations", array (
						"label_for" => "includeBootstrap",
						"type" => "checkbox",
						"slug" => "includeBootstrap",
						"label" => "Include Twitter Bootstrap (v.3.3.2) styles and scripts"
					));
					add_settings_field ("includeFontAwesome", "", array ($this, "DashboardMenu_SettingsField"), $this->settingsSlug, $this->slug . "_optimizations", array (
						"label_for" => "includeFontAwesome",
						"type" => "checkbox",
						"slug" => "includeFontAwesome",
						"label" => "Include Font Awesome icons"
					));

		}

	public function GetOptionsData () {
		return get_option($this->settingsSlug);
	}

	public function GetSocialSharingData ($postId) {

		$settings = get_option ($this->settingsSlug);

		$data = array (
			"active" => get_post_meta ($postId, "olx_forms_social_sharing_field", true),
			"activateOnLoad" => get_post_meta ($postId, "olx_forms_social_sharing_activate_on_load_field", true),
			"buttonLabel" => (isset ($settings ["buttonLabel"])) ? $settings ["buttonLabel"] : $this->defaults["buttonLabel"],
			"buttonIcon" => (isset ($settings ["buttonIcon"])) ? $settings ["buttonIcon"] : $this->defaults["buttonIcon"],
			"shareTitle" => htmlentities (get_post_meta ($postId, "olx_forms_social_sharing_share_title_field", true), ENT_QUOTES, 'UTF-8'),
			"shareSummary" => htmlentities (get_post_meta ($postId, "olx_forms_social_sharing_share_summary_field", true), ENT_QUOTES, 'UTF-8'),
			"shareUrl" => get_post_meta ($postId, "olx_forms_social_sharing_share_url_field", true),
			"shareImage" => get_post_meta ($postId, "olx_forms_social_sharing_share_image_field", true),
			"shareThisPublisherId" => (isset ($settings ["shareThisPublisherId"])) ? $settings ["shareThisPublisherId"] : $this->defaults["shareThisPublisherId"],
			"introductionTitle" => htmlentities (get_post_meta ($postId, "olx_forms_social_sharing_intro_title_field", true), ENT_QUOTES, 'UTF-8'),
			"introductionBody" => htmlentities (get_post_meta ($postId, "olx_forms_social_sharing_intro_description_field", true), ENT_QUOTES, 'UTF-8')
		);

		foreach ($data as $k => $v) {
			if (! empty ($v)) {
				$data [$k] = $v;
			} else {
				$data [$k] = $this->defaults[$k];
			}
		}

		return $data;

	}

	public function RegisterDashboardAssets () {
		wp_register_style ("olx_forms_dashboard_styles", OLXFORMS_CSS_URL . "dashboard-styles.css");
	}

		public function PrintDashboardAssets () {
			wp_enqueue_style ("olx_forms_dashboard_styles");
			echo '<div data-bbi-app="OnlineExpressForms" data-bbi-action="SelectTextOnClick"></div>';
		}

	public function RegisterFrontEndAssets () {
		wp_register_style ("olx_front_end_styles", OLXFORMS_CSS_URL . "front-end-styles.php");
		wp_register_script ("olx_front_end_scripts", OLXFORMS_JS_URL . "bootstrap.min.js");
	}

		public function PrintFrontEndStyles () {
			wp_enqueue_style ("olx_front_end_styles");
		}

		public function PrintFrontEndScripts () {
			$settings = get_option($this->settingsSlug);
			if ($settings && isset($settings["includeBootstrap"])) {
				wp_enqueue_script ("olx_front_end_scripts");
			}
		}

	public function PrintGlobalAssets () {
		$data = array (
			"mceId" => $this->MCE_ID
		);
		ob_start ();
		include OLXFORMS_RESOURCE_PATH . "view/bbi-namespace.php";
		echo ob_get_clean ();
	}

	public function PrintMetaTags () {

		global $wp_query;

		$postType = $wp_query->post->post_type;

		if ($postType == "page") {
			$content = $wp_query->post->post_content;
			$content = explode("[olx_form ", $content);
			if (count ($content) > 1) {
				$content = explode("form_id=", $content[1]);
				$content = explode("]", $content[1]);
				$postId = str_replace("'", "", str_replace('"','',$content[0]));
			} else {
				return false;
			}
		} else if ($postType == $this->slug) {
			$postId = $wp_query->post->ID;
		}

		$data = $this->GetSocialSharingData($postId);

		if ($data ["active"] == "true") {
			ob_start ();
			include OLXFORMS_RESOURCE_PATH . "view/meta-tags.php";
			echo ob_get_clean ();
		}
	}

}
