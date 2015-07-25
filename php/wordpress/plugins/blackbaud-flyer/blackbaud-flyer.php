<?php
/*
Plugin Name: Blackbaud: Flyer
Description: Create interactive modals and banners for your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.1
Text Domain: blackbaud_flyer
*/


# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_flyer_init($blackbaud) {



    # REGISTER.
    $app = $blackbaud->register(function($blackbaud) {
        return array(
            'alias'               => 'flyer',
            'text_domain'         => 'blackbaud_flyer',
            'post_type'           => 'blackbaud_flyer',
            'shortcode'           => 'blackbaud_flyer',
            'settings_slug'       => 'blackbaud_flyer',
            'plugin_file'         => __FILE__,
            'plugin_basename'     => plugin_basename(__FILE__),
            'url_root'            => plugins_url('public_html/', __FILE__),
            'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
        );
    });



    # CUSTOM POST TYPE.
    $app->forge('custom_post_type',
        function($app, $blackbaud) {
            $domain = $app->get('text_domain');
            return array(
                'slug'                   => $app->get('post_type'),
                'description'            => __('Create any number of lightboxes or alerts.', $domain),
                'supports'               => array('title', 'editor', 'thumbnail', 'excerpt'),
                'labels'                 => array(
                    'name'               => __('Flyers', $domain),
                    'singular_name'      => __('Flyer', $domain),
                    'menu_name'          => _x('Flyers', 'admin menu', $domain),
                    'name_admin_bar'     => _x('Flyer', 'add new on admin bar', $domain),
                    'add_new'            => _x('Add New', 'flyer', $domain),
                    'add_new_item'       => __('Add New Flyer', $domain),
                    'new_item'           => __('New Flyer', $domain),
                    'edit_item'          => __('Edit Flyer', $domain),
                    'view_item'          => __('View Flyer', $domain),
                    'all_items'          => __('All Flyers', $domain),
                    'search_items'       => __('Search Flyers', $domain),
                    'parent_item_colon'  => __('Parent Flyers:', $domain),
                    'not_found'          => __('No flyers found.', $domain),
                    'not_found_in_trash' => __('No flyers found in Trash.', $domain)
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
                    'blackbaud_flyer_id' => array(
                        'label' => __('Flyer ID', $domain),
                        'value' => function ($data, $app, $blackbaud) {
                            return $data['post_id'];
                        }
                    ),
                    'blackbaud_flyer_shortcode' => array(
                        'label' => __('Shortcode', $domain),
                        'value' => function ($data, $app, $blackbaud) {
                            return '<code class="blackbaud-selectable" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $app->get("shortcode") . ' flyer_id="' . $data['post_id'] . '"]</code>';
                        }
                    )
                )
            );
        });



    # META BOXES.
    $app->forge('meta_box',
        function($app, $blackbaud) {
            $domain = $app->get('text_domain');
            return array(
                'slug' => 'settings',
                'label' => 'Flyer Settings',
                'post_type' => $app->get('post_type'),
                'fields' => array(
                    array(
                        'slug'    => 'auto_launch',
                        'label'   => __('Launch automatically when page loads.', $domain),
                        'default' => '1',
                        'type'    => 'checkbox'
                    ),
                    array(
                        'slug'            => 'launcher_label',
                        'label'           => __('Launcher Button Label:', $domain),
                        'type'            => 'text',
                        'default'         => '',
                        'helplet'         => 'Leave blank to omit.',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '500',
                            'placeholder' => '(optional)'
                        )
                    ),
                    array(
                        'slug'            => 'button_label',
                        'label'           => __('Call-to-action Button Label:', $domain),
                        'type'            => 'text',
                        'default'         => '',
                        'helplet'         => 'Leave blank to omit.',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '500',
                            'placeholder' => '(optional)'
                        )
                    ),
                    array(
                        'slug'            => 'button_url',
                        'label'           => __('Call-to-action Button Link (URL):', $domain),
                        'type'            => 'text',
                        'default'         => '',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '5000',
                            'placeholder' => 'http://'
                        )
                    ),
                    array(
                        'slug'          => 'css_class',
                        'label'         => __('Custom CSS Class:', $domain),
                        'type'          => 'text',
                        'default'       => '',
                        'attributes'    => array(
                            'class'     => 'form-control',
                            'maxlength' => '500'
                        )
                    ),
                    array(
                        'slug'          => 'html_before',
                        'label'         => __('HTML Before Content:', $domain),
                        'type'          => 'textarea',
                        'default'       => '',
                        'attributes'    => array(
                            'class'     => 'form-control accepts-code',
                            'maxlength' => '5000'
                        )
                    ),
                    array(
                        'slug'          => 'html_after',
                        'label'         => __('HTML After Content:', $domain),
                        'type'          => 'textarea',
                        'default'       => '',
                        'attributes'    => array(
                            'class'     => 'form-control accepts-code',
                            'maxlength' => '5000'
                        )
                    )
                )
            );
        });



    # TINYMCE SHORTCODE BUTTON.
    $app->forge('tinymce_shortcode_button',
        function($app, $blackbaud) {
            return array(
                'slug'             => 'BBFlyerMCEButton',
                'post_type'        => $app->get('post_type'),
                'javascript_file'  => $app->get('url_root') . 'js/tinymce.blackbaud-flyer.js',
                'shortcode_slug'   => $app->get('shortcode'),
                'shortcode_id_key' => 'flyer_id'
            );
        });



    # SHORTCODE.
    $app->forge('shortcode',
        function($app, $blackbaud) {
            return array(
                'slug' => $app->get('shortcode'),
                'output' => function ($data, $app, $blackbaud) {

                    extract(shortcode_atts(array(
                        'flyer_id' => '0'
                    ), $data));
                    $post          = get_post($flyer_id);
                    $custom_fields = get_post_custom($flyer_id);
                    $data          = get_object_vars($post);

                    # Push the meta data to the options array.
                    foreach ($custom_fields as $k => $v) {
                        if (isset($v[0])) {
                            $data['meta'][$k] = $v[0];
                        }
                    }

                    # Save the thumbnail's src.
                    $data['meta']['thumbnail'] = wp_get_attachment_url(get_post_thumbnail_id($flyer_id));

                    # Output the HTML.
                    return $app->get_template('modal.blackbaud-flyer.php', $data);
                }
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
                    'handle' => 'blackbaud_flyer_bootstrap_js',
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
                    'handle' => 'blackbaud_flyer_bootstrap_css',
                    'source' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'
                );
            }
        });

    # Dashboard CSS.
    $app->forge('asset',
        function($app, $blackbaud) {
            return array(
                'access' => 'dashboard',
                'handle' => 'blackbaud_flyer_dashboard_styles',
                'source' => $app->get('url_root') . 'css/dashboard.blackbaud-flyer.css'
            );
        });

    # Front-end CSS.
    $app->forge('asset',
        function($app, $blackbaud) {
            if ($blackbaud->get_option_array($app->get('settings_slug'), 'default_styles') == 1) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $app->get('shortcode'),
                    'handle' => 'blackbaud_flyer_frontend_styles',
                    'source' => $app->get('url_root') . 'css/blackbaud-flyer.css'
                );
            }
        });



    # HELP & ABOUT PAGE.
    $app->forge('settings_page',
        function($app, $blackbaud) {
            return array(
                'slug' => $app->get('post_type') . '_help',
                'parent_slug' => 'edit.php?post_type=' . $app->get('post_type'),
                'menu_title' => 'Help',
                'page_title' => 'Blackbaud Flyers: Help',
                'callbacks' => array(
                    'display' => function($app, $blackbaud) {
                        echo $app->get_template("help.blackbaud-flyer.php");
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
    						)
    					)
    				)
    			)
    		);
        });



    # UPDATER.
    $app->forge('updater');



}



# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_flyer_init');
