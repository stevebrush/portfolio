<?php
/*
Plugin Name: Blackbaud: Online Express Forms
Description: Easily embed Online Express forms on your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.0.9
Text Domain: olx_forms
*/


include 'functions.php';


# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_olx_init($blackbaud) {



    # APPLICATION.
    $app = $blackbaud->register(function($blackbaud) {
        return array(
            'alias' => 'olx_forms',
            'text_domain' => 'olx_forms',
            'post_type' => 'olx_forms',
            'shortcode' => 'olx_form',
            'plugin_file' => __FILE__,
            'plugin_basename' => plugin_basename(__FILE__),
            'url_root' => plugins_url('public_html/', __FILE__),
            'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
            'settings_slug' => 'olx_forms_settings',
            'social_sharing_defaults' => array(
        		'active' => 'false',
        		'activateOnLoad' => 'true',
        		'buttonLabel' => 'Share',
        		'buttonIcon' => 'share-alt',
        		'includeDefaultStyles' => '1',
        		'includeBootstrap' => '1',
        		'includeFontAwesome' => '1',
        		'introductionTitle' => 'Share Your Contribution',
        		'introductionBody' => 'Please take some time to share with your friends and family how you supported this organization.',
        		'shareTitle' => '',
        		'shareSummary' => '',
        		'shareUrl' => '',
        		'shareImage' => '',
        		'shareThisPublisherId' => ''
        	)
        );
    });



    # BBI APPLICATION.
    $app->forge("bbi_script",
        function($app, $blackbaud) {
            return array(
                "src" => $app->get('url_root') . 'js/BBI.OnlineExpressForms.js'
            );
        });



    # ASSETS.
    # Bootstrap JS.
    $app->forge('asset',
        function($app, $blackbaud) {
            if ($blackbaud->get_option_array($app->get('settings_slug'), 'bootstrap_styles') == 1) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $app->get('shortcode'),
                    'handle' => 'blackbaud_olx_bootstrap_js',
                    'source' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
                );
            }
        });

    # Bootstrap CSS.
    $app->forge('asset',
        function($app, $blackbaud) {
            if ($blackbaud->get_option_array($app->get('settings_slug'), 'bootstrap_styles') == 1) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $app->get('shortcode'),
                    'handle' => 'blackbaud_olx_bootstrap_css',
                    'source' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'
                );
            }
        });

    # Font-awesome CSS.
    $app->forge('asset',
        function($app, $blackbaud) {
            if ($blackbaud->get_option_array($app->get('settings_slug'), 'font_awesome_styles') == 1) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $app->get('shortcode'),
                    'handle' => 'blackbaud_olx_font_awesome_css',
                    'source' => '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'
                );
            }
        });

    # Dashboard CSS.
    $app->forge('asset',
        function($app, $blackbaud) {
            return array(
                'access' => 'dashboard',
                'handle' => 'olx_forms_dashboard_styles',
                'source' => $app->get('url_root') . 'css/dashboard.blackbaud-olx.css'
            );
        });

    # Front-end CSS.
    $app->forge('asset',
        function($app, $blackbaud) {
            if ($blackbaud->get_option_array($app->get('settings_slug'), 'default_styles') == 1) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $app->get('shortcode'),
                    'handle' => 'olx_front_end_styles',
                    'source' => $app->get('url_root') . 'css/blackbaud-olx.css',
                    'type' => 'css'
                );
            }
        });



    # CUSTOM POST TYPE.
    $app->forge('custom_post_type',
        function($app, $blackbaud) {
            $domain = $app->get('text_domain');
            return array(
    			'slug' => $app->get('post_type'),
    			'description' => __('Stores the embed code for your various Online Express forms.', $domain),
    			'supports' => array('title'),
    			'labels' => array(
    				'name' => __('Online Express Forms', $domain),
    				'singular_name' => __('Form', $domain),
    				'menu_name' => _x('Online Express Forms', 'admin menu', $domain),
    				'menu_icon' => 'dashicons-slides',
    				'name_admin_bar' => _x('Online Express Form', 'add new on admin bar', $domain),
    				'add_new' => _x('Add New', 'form', $domain),
    				'add_new_item' => __('Add New Form', $domain),
    				'new_item' => __('New Form', $domain),
    				'edit_item' => __('Edit Form', $domain),
    				'view_item' => __('View Form', $domain),
    				'all_items' => __('All Forms', $domain),
    				'search_items' => __('Search Online Express Forms', $domain),
    				'parent_item_colon' => __('Parent Forms:', $domain),
    				'not_found' => __('No forms found.', $domain),
    				'not_found_in_trash' => __('No Online Express Forms found in Trash.', $domain)
    			)
    		);
        });



    # SORTABLE COLUMNS.
    $app->forge('post_sortable_columns',
        function($app, $blackbaud) {
            $domain = $app->get('text_domain');
            return array(
                'post_type' => $app->get('post_type'),
                'columns' => array(
                    'olx_form_id' => array(
                        'label' => __('Form ID', $domain),
                        'value' => function($data, $app, $blackbaud) {
                            return $data['post_id'];
                        }
                    ),
                    'olx_form_shortcode' => array(
                        'label' => __('Shortcode', $domain),
                        'value' => function($data, $app, $blackbaud) {
                            return '<code class="blackbaud-selectable" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $app->get('shortcode') . ' form_id="' . $data['post_id'] . '"]</code>';
                        }
                    )
                )
            );
        });



    # META BOX: EMBED CODE.
    $app->forge('meta_box',
        function($app, $blackbaud) {
            $domain = $app->get('text_domain');
            return array(
                'label' => 'Form Settings',
                'post_type' => $app->get('post_type'),
                'fields' => array(
    				array(
    					'slug' => 'embed_code',
    					'label' => __('Embed Code:', $domain),
    					'type' => 'textarea',
    					'attributes' => array(
    						'class' => 'form-control accepts-code',
    						'maxlength' => '5000',
    						'spellcheck' => 'false'
    					)
    				),
    				array(
    					'slug' => 'html_after',
    					'label' => __('HTML After:', $domain),
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



    # META BOX: SOCIAL SHARING.
    $app->forge('meta_box',
        function($app, $blackbaud) {
            $domain = $app->get('text_domain');
            return array(
                'slug' => 'social_sharing',
                'label' => 'Social Sharing',
                'post_type' => $app->get('post_type'),
                'fields' => array(
    				array(
    					'slug'  => 'social_sharing',
    					'label' => __('Allow social sharing on confirmation screen', $domain),
    					'default' => false,
    					'type'  => 'checkbox',
    					'parent_attributes' => array(
    						'data-checkbox-group-selector' => '.blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_activate_on_load',
    					'label' => __('Auto-display lightbox.', $domain),
    					'default' => true,
    					'type'  => 'checkbox',
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_intro_title',
    					'label' => __('Lightbox: Title', $domain),
    					'default' => __('Share Your Contribution', $domain),
    					'type'  => 'text',
    					'attributes'  => array(
    						'class' => 'form-control',
    						'maxlength' => '500'
    					),
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_intro_description',
    					'label' => __('Lightbox: Body Text (accepts HTML)', $domain),
    					'default' => __('Please take some time to share with your friends and family how you supported this organization.', $domain),
    					'type'  => 'textarea',
    					'attributes'  => array(
    						'class' => 'form-control',
    						'maxlength' => '5000'
    					),
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_share_title',
    					'label' => __('Sharing Title (default)', $domain),
    					'type'  => 'text',
    					'attributes'  => array(
    						'class' => 'form-control',
    						'maxlength' => '500'
    					),
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_share_summary',
    					'label' => __('Sharing Summary (default)', $domain),
    					'type'  => 'textarea',
    					'attributes'  => array(
    						'class' => 'form-control',
    						'maxlength' => '1500'
    					),
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_share_url',
    					'label' => __('Sharing URL', $domain),
    					'type'  => 'text',
    					'attributes'  => array(
    						'class' => 'form-control',
    						'maxlength' => '1500',
    						'placeholder' => 'http://'
    					),
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				),
    				array(
    					'slug'  => 'social_sharing_share_image',
    					'label' => __('Sharing Image', $domain),
    					'type'  => 'media-gallery-picker',
    					'attributes'  => array(
    						'class' => 'form-control',
    						'maxlength' => '1500',
    						'placeholder' => 'http://'
    					),
    					'parent_attributes' => array(
    						'class' => 'form-group blackbaud-confirmation-social-sharing'
    					)
    				)
    			)
            );
        });



    # TINYMCE SHORTCODE BUTTON.
    $app->forge('tinymce_shortcode_button',
        function($app, $blackbaud) {
            return array(
                'slug'             => 'OLXFormsMCEButton',
                'post_type'        => $app->get('post_type'),
                'javascript_file'  => $app->get('url_root') . 'js/tinymce.blackbaud-olx.js',
                'shortcode_slug'   => $app->get('shortcode'),
                'shortcode_id_key' => 'form_id'
            );
        });



    # SHORTCODE.
    $app->forge('shortcode',
        function($app, $blackbaud) {
            return array(
                'slug' => $app->get('shortcode'),
                'output' => function($data, $app, $blackbaud) {
                    extract(shortcode_atts(array('form_id' => '0'), $data));
                    ob_start();
                    # Use the template tag.
                    the_olx_form($form_id);
                    return ob_get_clean();
                }
            );
        });



    # HELP & ABOUT PAGE.
    $app->forge('settings_page',
        function($app, $blackbaud) {
            return array(
                'slug' => $app->get('post_type') . '_help',
                'parent_slug' => 'edit.php?post_type=' . $app->get('post_type'),
                'menu_title' => 'Help',
                'page_title' => 'Online Express Forms: About & Help',
                'callbacks' => array(
                    'display' => function($app, $blackbaud) {
                        echo $app->get_template("help.blackbaud-olx.php");
                    }
                )
            );
        });



    # SETTINGS PAGE.
    $app->forge('settings_page',
        function($app, $blackbaud) {
            return array(
    			'slug' => $app->get('settings_slug'),
    			'parent_slug' => 'edit.php?post_type=' . $app->get('post_type'),
    			'sections' => array(
    				'social_sharing' => array(
    					'title' => 'Social Sharing',
    					'fields' => array(
    						array(
    							'slug' => 'publisher_id',
    							'type' => 'text',
    							'label' => 'ShareThis Publisher ID:'
    						),
    						array(
    							'slug' => 'share_button_label',
    							'type' => 'text',
    							'label' => 'Share Button Label:',
    							'default' => 'Share'
    						),
    						array(
    							'slug' => 'share_button_icon',
    							'type' => 'text',
    							'label' => 'Share Button Icon Suffix (Font Awesome):',
    							'default' => 'share-alt'
    						)
    					)
    				),
    				'optimizations' => array(
    					'title' => 'Optimizations',
    					'fields' => array(
    						array(
    							'slug' => 'default_styles',
    							'type' => 'checkbox',
    							'label' => 'Include default style sheet',
    							'default' => '1'
    						),
    						array(
    							'slug' => 'bootstrap_styles',
    							'type' => 'checkbox',
    							'label' => 'Include Twitter Bootstrap (v.3.3.2) styles and scripts',
    							'default' => '1'
    						),
    						array(
    							'slug' => 'font_awesome_styles',
    							'type' => 'checkbox',
    							'label' => 'Include Font Awesome icons',
    							'default' => '1'
    						)
    					)
    				)
    			)
    		);
        });



    # OLX SOCIAL SHARING.
    $app->add_module('SocialSharing',
        function($app, $blackbaud) {
            return array(
                'get_data' => function($post_id, $module) use ($app, $blackbaud) {
                    $defaults = $app->get('social_sharing_defaults');
                    $settings = get_option($app->get('settings_slug'));
                    $cpts = $app->forged('custom_post_type');
                    $cpt = $cpts[0];
            		$data = array(
            			'active'               => $cpt->meta($post_id, 'social_sharing'),
            			'activateOnLoad'       => $cpt->meta($post_id, 'social_sharing_activate_on_load'),
            			'buttonLabel'          => (isset ($settings['buttonLabel'])) ? $settings['buttonLabel'] : $defaults['buttonLabel'],
            			'buttonIcon'           => (isset ($settings['buttonIcon'])) ? $settings['buttonIcon'] : $defaults['buttonIcon'],
            			'shareTitle'           => htmlentities ($cpt->meta($post_id, 'social_sharing_share_title')),
            			'shareSummary'         => htmlentities ($cpt->meta($post_id, 'social_sharing_share_summary')),
            			'shareUrl'             => $cpt->meta($post_id, 'social_sharing_share_url'),
            			'shareImage'           => $cpt->meta($post_id, 'social_sharing_share_image'),
            			'shareThisPublisherId' => (isset ($settings['shareThisPublisherId'])) ? $settings['shareThisPublisherId'] : $defaults['shareThisPublisherId'],
            			'introductionTitle'    => htmlentities ($cpt->meta($post_id, 'social_sharing_intro_title')),
            			'introductionBody'     => htmlentities ($cpt->meta($post_id, 'social_sharing_intro_description'))
            		);
            		foreach ($data as $k => $v) {
            			if (!empty($v)) {
            				$data[$k] = $v;
            			} else {
            				$data[$k] = $defaults[$k];
            			}
            		}
            		return $data;
                },
                'meta_tags' => function($module) use ($app, $blackbaud) {
                    global $wp_query;
            		if (!isset($wp_query->post) || !is_object($wp_query->post)) {
            			return false;
            		}
            		$postType = $wp_query->post->post_type;
            		if ($postType == 'page') {
            			$content = $wp_query->post->post_content;
            			$content = explode ('[olx_form ', $content);
            			if (count ($content) > 1) {
            				$content = explode ('form_id=', $content [1]);
            				$content = explode (']', $content [1]);
            				$postId = str_replace ('"', '', str_replace ("'", '', $content [0]));
            			} else {
            				return false;
            			}
            		} else if ($postType == $app->get("post_type")) {
            			$postId = $wp_query->post->ID;
            		} else {
            			return false;
            		}
            		$data = $module->get_data($postId);
            		if ($data['active'] == 'true') {
            			$app->get_template("meta-tags.php", $data);
            		}
                }
            );
        });



    # UPDATER.
    $app->forge('updater');



    # ADD META TAGS ON THE FRONT-END.
    if (!is_admin()) {
        add_action('wp_head', array($app->module('SocialSharing'), 'meta_tags'));
    }



}


# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_olx_init');
