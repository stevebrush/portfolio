<?php
class BlackbaudCarouselSettings {

	# Values to be used in the fields callbacks
	private $options;
	private $slug;

	public function __construct () {
		$this->slug = BLACKBAUD_CAROUSEL_POST_TYPE;
	    add_action ("admin_menu", array ($this, "add_plugin_page"));
	    add_action ("admin_init", array ($this, "page_init"));
	    add_filter ("plugin_action_links_" . BLACKBAUD_CAROUSEL_PLUGIN_MAIN_FILE, array ($this, "SettingsLink"));
	}

	public function add_plugin_page () {
		# add settings page
		add_submenu_page ("edit.php?post_type=" . $this->slug, __ ("Settings", $this->slug), __ ("Settings", $this->slug), "manage_options", $this->slug . "_settings", array ($this, "create_admin_page"));
	}

	public function create_admin_page () {

		// options page callback
	    $this->options = get_option ($this->slug . "_settings");

		if (! $this->options) {
			BlackbaudCarousel:: SetOptions ();
			$this->options = get_option ($this->slug . "_settings");
		}

		echo '<div class="wrap">';
			echo screen_icon ("edit");
			echo '<h2>' . __ ("Blackbaud Bootstrap Carousel Settings", $this->slug) . '</h2>';
			echo '<p>' . __ ("You can set the default behaviour of your carousels here. All of these settings can be overridden by using %s shortcode attributes %s.", $this->slug) . '</p>';
			echo '<form method="post" action="options.php">';
			    echo settings_fields ($this->slug . "_settings");
			    echo do_settings_sections ($this->slug);
			    echo submit_button ();
		    echo '</form>';
	    echo '</div>';
	}

	public function page_init () {

		# register and add settings
	    register_setting ($this->slug . "_settings", $this->slug . "_settings", array ($this, "sanitize"));

	    add_settings_section (
	        $this->slug . "_settings_options", // ID
	        "", // Title - nothing to say here.
	        array($this, $this->slug . "_settings_options_header"), // Callback
	        $this->slug // Page
	    );
	    add_settings_field (
	        "twbs", // ID
	        __("Twitter Bootstrap Version", $this->slug), // Title
	        array( $this, "twbs_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	    add_settings_field(
	        "interval", // ID
	        __("Slide Interval (milliseconds)", $this->slug), // Title
	        array( $this, "interval_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	    add_settings_field(
	        "showcaption", // ID
	        __("Show Slide Captions?", $this->slug), // Title
	        array( $this, "showcaption_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	    add_settings_field(
	        "showcontrols", // ID
	        __("Show Slide Controls?", $this->slug), // Title
	        array( $this, "showcontrols_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	    add_settings_field(
	        "orderby", // ID
	        __("Order Slides By", $this->slug), // Title
	        array( $this, "orderby_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	    add_settings_field(
	        "order", // ID
	        __("Ordering Direction", $this->slug), // Title
	        array( $this, "order_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	    add_settings_field(
	        "category", // ID
	        __("Restrict to Category", $this->slug), // Title
	        array( $this, "category_callback" ), // Callback
	        $this->slug, // Page
	        $this->slug . "_settings_options" // Section
	    );
	}

	public function sanitize ($input) {
		// Sanitize each setting field as needed
		// @param array $input contains all settings fields as array keys
	    $new_input = array();
		foreach($input as $key => $var) {
			if ($key == "twbs" || $key == "interval") {
				$new_input[$key] = absint($input[$key]);
				if ($key == "interval" && $new_input[$key] == 0) {
					$new_input[$key] = 5000;
				}
			} else {
				$new_input[$key] = sanitize_text_field($input[$key]);
			}
		}
	    return $new_input;
	}

	public function twbs_callback () {
		if (isset($this->options["twbs"]) && $this->options["twbs"] == "3") {
			$blackbaudbc_twbs3 = ' selected="selected"';
			$blackbaudbc_twbs2 = '';
		} else {
			$blackbaudbc_twbs3 = '';
			$blackbaudbc_twbs2 = ' selected="selected"';
		}
		print
		'<select id="twbs" name="' . $this->slug . '_settings[twbs]">
			<option value="2"' . $blackbaudbc_twbs2 . '>2.x</option>
			<option value="3"' . $blackbaudbc_twbs3 . '>3.x</option>
		</select>';
	}

	public function interval_callback () {
	    printf('<input type="text" id="interval" name="' . $this->slug . '_settings[interval]" value="%s" size="6" />',
	        isset($this->options['interval']) ? esc_attr($this->options['interval']) : '');
	}

	public function showcaption_callback () {
		if (isset($this->options["showcaption"]) && $this->options["showcaption"] == "false") {
			$blackbaudbc_showcaption_t = '';
			$blackbaudbc_showcaption_f = ' selected="selected"';
		} else {
			$blackbaudbc_showcaption_t = ' selected="selected"';
			$blackbaudbc_showcaption_f = '';
		}
		print '<select id="showcaption" name="' . $this->slug . '_settings[showcaption]">
			<option value="true"' . $blackbaudbc_showcaption_t . '>' . __('Show', 'bb-bootstrap-carousel') . '</option>
			<option value="false"' . $blackbaudbc_showcaption_f . '>' . __('Hide', 'bb-bootstrap-carousel') . '</option>
		</select>';
	}

	public function showcontrols_callback () {
		if (isset($this->options["showcontrols"]) && $this->options["showcontrols"] == "false") {
			$blackbaudbc_showcontrols_t = '';
			$blackbaudbc_showcontrols_f = ' selected="selected"';
		} else {
			$blackbaudbc_showcontrols_t = ' selected="selected"';
			$blackbaudbc_showcontrols_f = '';
		}
		print '<select id="showcontrols" name="' . $this->slug . '_settings[showcontrols]">
			<option value="true"' . $blackbaudbc_showcontrols_t . '>' . __('Show', 'bb-bootstrap-carousel') . '</option>
			<option value="false"' . $blackbaudbc_showcontrols_f . '>' . __('Hide', 'bb-bootstrap-carousel') . '</option>
		</select>';
	}

	public function orderby_callback () {
		$orderby_options = array(
			"menu_order" => __("Menu order, as set in Carousel overview page", $this->slug),
			"date" => __("Date slide was published", $this->slug),
			"rand" => __("Random ordering", $this->slug),
			"title" => __("Slide title", $this->slug)
		);
		print '<select id="orderby" name="' . $this->slug . '_settings[orderby]">';
		foreach ($orderby_options as $val => $option) {
			print '<option value="' . $val . '"';
			if (isset($this->options['orderby']) && $this->options['orderby'] == $val) {
				print ' selected="selected"';
			}
			print ">$option</option>";
		}
		print '</select>';
	}

	public function order_callback () {
		if (isset($this->options["order"]) && $this->options["order"] == "DESC") {
			$blackbaudbc_showcontrols_a = '';
			$blackbaudbc_showcontrols_d = ' selected="selected"';
		} else {
			$blackbaudbc_showcontrols_a = ' selected="selected"';
			$blackbaudbc_showcontrols_d = '';
		}
		print '<select id="order" name="' . $this->slug . '_settings[order]">
			<option value="ASC"' . $blackbaudbc_showcontrols_a . '>' . __('Ascending', 'bb-bootstrap-carousel') . '</option>
			<option value="DESC"' . $blackbaudbc_showcontrols_d . '>' . __('Decending', 'bb-bootstrap-carousel') . '</option>
		</select>';
	}

	public function category_callback () {
		$cats = get_terms("bb_carousel_category");
		print '<select id="orderby" name="' . $this->slug . '_settings[category]"><option value="">' . __('All Categories', 'bb-bootstrap-carousel') . '</option>';
		foreach ($cats as $cat) {
			print '<option value="' . $cat->term_id . '"';
			if (isset($this->options['category']) && $this->options['category'] == $cat->term_id) {
				print ' selected="selected"';
			}
			print ">" . $cat->name . "</option>";
		}
		print '</select>';
	}

	public function SettingsLink ($links) {
		$settings_link = '<a href="edit.php?post_type=' . $this->slug . '&page=settings">' . __ ('Settings', $this->slug) . '</a>';
		array_unshift ($links, $settings_link);
		return $links;
	}

	public function blackbaud_carousel_settings_options_header () {
		// Print the Section text
	    // nothing to say here.
	}

}
