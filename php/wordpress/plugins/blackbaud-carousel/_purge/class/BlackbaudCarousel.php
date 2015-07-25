<?php
class BlackbaudCarousel {

	private static $className = "BlackbaudCarousel";
	private static $slug = "blackbaud_carousel";

	public static function Start (BlackbaudCPT $factory) {

		# First, create the custom post type.

		$labels = array (
			"name" 				 => __ ("Carousel Slides", self:: $slug),
			"singular_name" 	 => __ ("Slide", self:: $slug),
			"menu_name" 		 => _x ("BB Carousel 2.0", "admin menu", self:: $slug),
			"name_admin_bar" 	 => _x ("Slide", "add new on admin bar", self:: $slug),
			"add_new" 			 => _x ("Add New", "slide", self:: $slug),
			"add_new_item" 		 => __ ("Add New Slide", self:: $slug),
			"new_item" 			 => __ ("New Slide", self:: $slug),
			"edit_item" 		 => __ ("Edit Slide", self:: $slug),
			"view_item" 		 => __ ("View Slide", self:: $slug),
			"all_items" 		 => __ ("All Slides", self:: $slug),
			"search_items" 		 => __ ("Search Slides", self:: $slug),
			"parent_item_colon"  => __ ("Parent Slides:", self:: $slug),
			"not_found" 		 => __ ("No slides found.", self:: $slug),
			"not_found_in_trash" => __ ("No slides found in Trash.", self:: $slug)
		);

		$cpt = $factory->Create ("CustomPostType", array (
			"slug" 		  => self:: $slug,
			"public" 	  => true,
			"labels" 	  => $labels,
			"description" => __ ("Allows you to easily create slides for your various Carousels.", self:: $slug),
			"supports" 	  => array ("title", "editor", "thumbnail", "page-attributes")
		));

		//$cpt->RemoveSlugFromPermalink ();

		# Provide the path to this plugin's main file.
		# We do this so we can refresh any friendly URLs associated with our post types,
		# once this plugin is activated.
		$cpt->Set ("pluginMainFile", BLACKBAUD_CAROUSEL_PLUGIN_MAIN_FILE);

		# Create categories for the slides.
		$cpt->Taxonomy ($cpt->slug . "_category", array (
			"hierarchical" => true
		));

		# Let's register a new meta box for this post type.
		$metaBox = $factory->Create ("PostMetaBox", array (
			"postType" => $cpt->slug,
			"slug"     => "settings",
			"label"    => "Slide Settings"
		));

			# Add some fields to it.
			$metaBox->AddField (array (
				"slug"  => "button_label",
				"label" => __ ("Button Label:", self:: $slug),
				"type"  => "text",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "250"
				)
			));

			$metaBox->AddField (array (
				"slug" => "button_link",
				"label" => __ ("Button Link:", self:: $slug),
				"type" => "text",
				"attr"  => array (
					"class" => "form-control",
					"maxlength" => "500"
				)
			));

			# Build it to the page!
			$metaBox->Build ();


		# Add columns to the dashboard view:
		$cpt->Columns (array (
			"bb_carousel_image" => array (
				"label" => __ ("Image", self:: $slug),
				"value" => '$img = BlackbaudCarousel:: GetFeaturedImage ($post_id);if ($img) {$href = get_edit_post_link ($post_id);echo "<a href=\"{$href}\"><img src=\"{$img}\" /></a>";}'
			)
		));

		add_action ("plugins_loaded", array (get_called_class (), "LoadTranslations"));
		add_action ("after_setup_theme", array (get_called_class (), "AddFeaturedImageSupport"));
		add_shortcode ("blackbaud_carousel", array (self::$className, "AddShortcode"));

	}

	public static function AddFeaturedImageSupport () {

		$supportedTypes = get_theme_support ("post-thumbnails");

		if ($supportedTypes === false) {

			add_theme_support ("post-thumbnails", array (self:: $slug));
			add_image_size ("featured_preview", 100, 55, true);

		} elseif (is_array ($supportedTypes)) {

			$supportedTypes [0][] = self:: $slug;
			add_theme_support ("post-thumbnails", $supportedTypes [0]);
			add_image_size ("featured_preview", 100, 55, true);

		}
	}

	public static function AddShortcode ($atts, $content = null) {

		$options = get_option (self:: $slug . "_settings");

		if (! $options) {
			self:: SetOptions ();
			$options = get_option (self:: $slug . "_settings");
		}

		$options ["id"] = "";

		# Parse incomming $atts into an array and merge it with $defaults
		$atts = shortcode_atts ($options, $atts);
		$atts ["post_type"] = self:: $slug;

		return the_blackbaud_carousel ($atts);
	}

	public static function GetFeaturedImage ($post_id) {

		$post_thumbnail_id = get_post_thumbnail_id ($post_id);

		if ($post_thumbnail_id) {
			$post_thumbnail_img = wp_get_attachment_image_src ($post_thumbnail_id, "featured_preview");
			return $post_thumbnail_img [0];
		}
	}

	public static function LoadTranslations () {
		$lang_dir = "../" . basename (dirname (__FILE__)) . "/languages";
		load_plugin_textdomain (self:: $slug, false, $lang_dir);
	}

	public static function SetOptions () {

		$defaults = array (
			"interval" => "5000",
			"showcaption" => "true",
			"showcontrols" => "true",
			"orderby" => "menu_order",
			"order" => "ASC",
			"category" => "",
			"id" => "",
			"twbs" => "3"
		);

		add_option (self:: $slug . "_settings", $defaults);

	}

}
