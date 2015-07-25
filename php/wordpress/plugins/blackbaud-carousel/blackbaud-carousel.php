<?php
/*
Plugin Name: Blackbaud: Carousel
Description: Twitter Bootstrap Carousels for your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.5.0
Text Domain: blackbaud_carousel
*/

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_carousel_init($blackbaud) {



    # REGISTER.
    $app = $blackbaud->register(function($blackbaud) {
        return array(
            'alias'               => 'BlackbaudCarousel',
            'shortcode'           => 'blackbaud_carousel',
            'text_domain'         => 'blackbaud_carousel',
            'post_type'           => 'bb_carousel_slide',
            'taxonomy_slug'       => 'bb_carousel',
            'plugin_file'         => __FILE__,
            'plugin_basename'     => plugin_basename(__FILE__),
            'url_root'            => plugins_url('assets/', __FILE__),
            'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
        );
    });



    # CUSTOM POST TYPE.
    $app->forge('custom_post_type', function($app, $blackbaud) {
        $domain = $app->get('text_domain');
        return array(
            'slug'        => $app->get('post_type'),
            'description' => __('Create any number of Bootstrap carousel slides.', $domain),
            'supports'    => array("title", "editor", "thumbnail", "page-attributes"),
            'labels'      => array (
    			"name" 				 => __ ("Carousel Slides", $domain),
    			"singular_name" 	 => __ ("Slide", $domain),
    			"menu_name" 		 => _x ("Slides", "admin menu", $domain),
    			"name_admin_bar" 	 => _x ("Slide", "add new on admin bar", $domain),
    			"add_new" 			 => _x ("Add New", "slide", $domain),
    			"add_new_item" 		 => __ ("Add New Slide", $domain),
    			"new_item" 			 => __ ("New Slide", $domain),
    			"edit_item" 		 => __ ("Edit Slide", $domain),
    			"view_item" 		 => __ ("View Slide", $domain),
    			"all_items" 		 => __ ("All Slides", $domain),
    			"search_items" 		 => __ ("Search Slides", $domain),
    			"parent_item_colon"  => __ ("Parent Slides:", $domain),
    			"not_found" 		 => __ ("No slides found.", $domain),
    			"not_found_in_trash" => __ ("No slides found in Trash.", $domain)
    		),
            'title_placeholder' => 'Enter slide title',
            'register_taxonomies'  => array(
                $app->get('taxonomy_slug') => array(
        			"labels" => array(
                        'name' => __("Carousels", $domain),
                        'singular_name' => __("Carousel", $domain),
                        'menu_name' => __("Carousels", $domain),
                        'all_items' => __("All Carousels", $domain),
                        'edit_item' => __("Edit Carousel", $domain),
                        'view_item' => __("View Carousel", $domain),
                        'update_item' => __("Update Carousel", $domain),
                        'add_new_item' => __("Add New Carousel", $domain),
                        'new_item_name' => __("New Carousel Name", $domain),
                        'parent_item' => __("Parent Carousel", $domain),
                        'parent_item_colon' => __("Parent Carousel:", $domain),
                        'search_items' => __("Search Carousels", $domain),
                        'popular_items' => __("Popular Carousels", $domain),
                        'not_found' => __("No carousels found.", $domain)
                    ),
        			'public' => true,
        			'show_ui' => true,
        			'show_tagcloud' => false,
        			'hierarchical' => true
    			)
            )
        );
    });



    /**
     * Additional content.
     */
    $app->forge('meta_box', function($app, $blackbaud) {
        $domain = $app->get('text_domain');
        return array(
            'label' => __('Additional Content', $domain),
            'slug' => 'additional_content',
            'post_type' => $app->get('post_type'),
            'fields' => array(
				array(
					'slug' => 'subtitle',
					'label' => __('Subtitle:', $domain),
					'type' => 'text',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '1500'
					)
				),
				array(
					'slug' => 'blurb',
					'label' => __('Blurb:', $domain),
					'type' => 'textarea',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '2500'
					)
				)
			)
        );
    });



    /**
     * Buttons.
     */
    $app->forge('meta_box', function($app, $blackbaud) {
        $domain = $app->get('text_domain');
        return array(
            'label' => __('Buttons', $domain),
            'slug' => 'buttons',
            'post_type' => $app->get('post_type'),
            'fields' => array(
				array(
					'slug' => 'primary_button_label',
					'label' => __('Primary Button Label:', $domain),
					'type' => 'text',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '1500'
					)
				),
				array(
					'slug' => 'primary_button_link',
					'label' => __('Primary Button Link:', $domain),
					'type' => 'text',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '1500',
						'placeholder' => 'http://'
					)
				),
				array(
					'slug' => 'secondary_button_label',
					'label' => __('Secondary Button Label:', $domain),
					'type' => 'text',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '1500'
					)
				),
				array(
					'slug' => 'secondary_button_link',
					'label' => __('Secondary Button Link:', $domain),
					'type' => 'text',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '1500',
						'placeholder' => 'http://'
					)
				)
			)
        );
    });



    /**
     * Slides: Advanced content.
     */
    $app->forge('meta_box', function($app, $blackbaud) {
        $domain = $app->get('text_domain');
        return array(
            'label' => __('Advanced', $domain),
            'slug' => 'advanced_content',
            'post_type' => $app->get('post_type'),
            'fields' => array(
				array(
					'slug' => 'css_class',
					'label' => __('CSS class:', $domain),
					'type' => 'text',
					'attributes' => array(
						'class' => 'form-control',
						'maxlength' => '500',
						'spellcheck' => 'false'
					)
				),
				array(
					'slug' => 'html_after',
					'label' => __('HTML after:', $domain),
					'type' => 'textarea',
					'attributes'  => array(
						'class' => 'form-control accepts-code',
						'maxlength' => '5000',
						'spellcheck' => 'false'
					)
				)
			)
        );
    });



    /**
     * Carousel: Attributes.
     */
    $app->forge('taxonomy_fields', function ($app, $blackbaud) {
        $domain = $app->get('text_domain');
        return array(
            'taxonomy' => $app->get('taxonomy_slug'),
			'fields' => array(
    			array(
        			'slug' => 'transition_type',
        			'label' => __('Transition type:', $domain),
        			'type' => 'select',
        			'default' => 'slide',
        			'options' => array(
            			"Slide" => "slide",
            			"Fade" => "fade"
        			)
                ),
                array(
        			'slug' => 'transition_speed',
        			'label' => __('Transition speed:', $domain),
        			'type' => 'text',
        			'helplet' => "In milliseconds.",
        			'default' => '1000',
        			'attributes' => array(
        				'maxlength' => '500'
        			)
                ),
                array(
        			'slug' => 'interval',
        			'label' => __('Duration between slides:', $domain),
        			'type' => 'text',
        			'helplet' => "In milliseconds.",
        			'default' => '3000',
        			'attributes' => array(
        				'maxlength' => '500'
        			)
                ),
                array(
        			'slug' => 'navigation_previous',
        			'label' => __('Navigation button label (previous):', $domain),
        			'type' => 'text',
        			'helplet' => "Accepts HTML",
        			'default' => '<i class="glyphicon glyphicon-chevron-left"></i>',
        			'attributes' => array(
        				'maxlength' => '500',
        				'placeholder' => 'e.g., &amp;larr;',
        			)
                ),
                array(
        			'slug' => 'navigation_next',
        			'label' => __('Navigation button label (next):', $domain),
        			'type' => 'text',
        			'helplet' => "Accepts HTML",
        			'default' => '<i class="glyphicon glyphicon-chevron-right"></i>',
        			'attributes' => array(
        				'maxlength' => '500',
        				'placeholder' => 'e.g., &amp;rarr;',
        			)
                ),
                array(
        			'slug' => 'css_class',
        			'label' => __('CSS class:', $domain),
        			'type' => 'text',
        			'attributes' => array(
        				'maxlength' => '500'
        			),
        			'parent_attributes' => array(
            			'class' => 'my-class'
        			)
                ),
                array(
        			'slug' => 'auto_play',
        			'label' => __('Auto play', $domain),
        			'type' => 'checkbox',
        			'default' => 'on'
                ),
                array(
        			'slug' => 'loop',
        			'label' => __('Loop the presentation', $domain),
        			'type' => 'checkbox',
        			'default' => 'on'
                ),
                array(
        			'slug' => 'pause',
        			'label' => __('Pause on-hover', $domain),
        			'type' => 'checkbox',
        			'default' => 'off'
                ),
                array(
        			'slug' => 'image_backgrounds',
        			'label' => __('Slide images are backgrounds', $domain),
        			'type' => 'checkbox',
        			'default' => 'off'
                ),
                array(
        			'slug' => 'random_start',
        			'label' => __('First slide is random', $domain),
        			'type' => 'checkbox',
        			'default' => 'off'
                )
			)
        );
    });



    /**
     * [blackbaud_carousel]
     */
    $app->forge('shortcode', function ($app) {
        return array(
            'slug' => $app->get('shortcode'),
            'output' => function($data) use ($app) {

                $term_slug = $data['slug'];
                $carousel = get_term_by('slug', $term_slug, $app->get('taxonomy_slug'));
                $data = get_option("taxonomy_" . $carousel->term_id);
                $data['id'] = uniqid();
                $data['slides'] = get_posts(array(
                    'post_type' => $app->get('post_type'),
                    'showposts' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => $app->get('taxonomy_slug'),
                            'field' => 'slug',
                            'terms' => $term_slug
                        )
                    )
                ));
                $data['starting_index'] = 0;
                if (isset($data['random_start']) && $data['random_start'] == "on")
                {
                    $data['starting_index'] = array_rand($data['slides']);
                }

                foreach ($data['slides'] as $k => $post)
                {
                    $post->fields = get_post_custom($post->ID);
                }

                $app->print_tidy($data);

                ob_start();
                echo $app->get_template('carousel.blackbaud-carousel.php', $data);
                return ob_get_clean();
            }
        );
    });



    # UPDATER.
    $app->forge('updater');



}



# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_carousel_init');



/*
function nlm_taxonomy_edit_meta_field($term) {

    $term_id = $term->term_id;

    # Retrieve the existing value(s) for this meta field. This returns an array.
	$term_meta = get_option("taxonomy_$term_id");
	$value = esc_attr($term_meta['campaign_id']) ? esc_attr($term_meta['campaign_id']) : '';

	# Build the form field.
	$html = '';
	$html .= '<tr class="form-field">';
	$html .= '<th scope="row" valign="top"><label for="term_meta[campaign_id]">' . __('Luminate Campaign ID:', 'nlm_tracker') . '</label></th>';
		$html .= '<td>';
			$html .= '<input type="text" name="term_meta[campaign_id]" id="term_meta[campaign_id]" value="' . $value . '">';
			$html .= '<p class="description">' . __('This value represents the unique campaignId created by Luminate Online.', 'nlm_tracker') . '</p>';
		$html .= '</td>';
	$html .= '</tr>';

	echo $html;
}
function save_taxonomy_font_awesome_icon($term_id) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}
*/


//add_action('campaign_edit_form_fields', 'nlm_taxonomy_edit_meta_field', 10, 2);
//add_action('edited_category', 'save_taxonomy_font_awesome_icon', 10, 2);
//add_action('create_category', 'save_taxonomy_font_awesome_icon', 10, 2);
