<?php
/*
Plugin Name: Blackbaud: Assistant & Libraries
Description: Essential libraries for Blackbaud-supported plugins (DO NOT DEACTIVATE).
Author: Blackbaud Interactive Services
Version: 0.1
Text Domain: blackbaud_assistant
*/



# MAKE SURE WE'RE INSIDE A NAMESPACE.
namespace blackbaud;



# INCLUDE OUR LIBRARIES.
require_once 'classes/Core.php';
require_once 'classes/Field.php';
require_once 'classes/CustomPostType.php';
require_once 'classes/PostsColumns.php';
require_once 'classes/MetaBox.php';
require_once 'classes/MetaField.php';
require_once 'classes/TaxonomyField.php';
require_once 'classes/TaxonomyFields.php';
require_once 'classes/Shortcode.php';
require_once 'classes/TinyMCEShortcodeButton.php';
require_once 'classes/Asset.php';
require_once 'classes/SettingsPage.php';
require_once 'classes/Updater.php';
require_once 'classes/Application.php';
require_once 'classes/ApplicationModule.php';



# WRAP EVERYTHING IN A FUNCTION TO PRESERVE THE SCOPE.
function blackbaud_assistant_init($app)
{



    # CREATE THE FACTORY.
    $app->module('Factory',
        function ($app, $blackbaud)
        {
            return array(
                'actions' => array(),
                'bbi_scripts' => array(),
                'last_app' => null,
                'apps' => array(),
                'start' => function ($module)
                {
                    add_action('plugins_loaded', array($module, 'plugins_loaded'));
                },
                'get_app' => function ($alias, $module)
                {
                    return $module->apps[$alias];
                },
                'add_bbi_script' => function ($options = array(), $module)
                {
                    $module->bbi_scripts[] = $options['src'];
                    return $options;
                },
                'get_option_array' => function ($slug, $key)
                {
                    if ($settings = get_option($slug, false))
                    {
                        if ($settings && isset($settings[$key]))
                        {
                            return $settings[$key];
                        }
                        return $settings;
                    }
                    return false;
                },
                'plugins_loaded' => function ($module)
                {
                    # Execute all actions when the plugins have been activated.
                    foreach ($module->actions as $action)
                    {
                        do_action($action, $module);
                    }
                },
                'register' => function ($request = null, $module)
                {
                    # Determine how the options variable is generated.
                    # Can be an array or returned from a callback.
                    if (is_array($request))
                    {
                        $options = $request;
                    }
                    else if (is_callable($request))
                    {
                        $options = call_user_func($request, $module);
                    }
                    else
                    {
                        $options = array();
                    }

                    # Make sure $blackbaud is added as a property.
                    if (! isset($options ['blackbaud']))
                    {
                        $options ['blackbaud'] = $module;
                    }

                    # Store the new application in various places to find it later.
                    $module->last_app = new Application($options);
                    $module->apps[$options['alias']] = $module->last_app;

                    return $module->last_app;
                },
                'trigger' => function ($alias, $module)
                {
                    /**
                     * Adds an action to the list, to be ultimately triggered when all plugins
                     * have been loaded.
                     */
                    $module->actions[] = $alias;
                }
            );
        });



    # MAKE '$blackbaud' POINT TO THE FACTORY.
    $app->set('blackbaud', $app->module('Factory'));



    # BBI SCRIPT.
    $app->forge('bbi_script',
        function ($app)
        {
            return array('src' => $app->get('assets_url') . 'js/BBI.BlackbaudWP.js');
        });



    # ASSETS.
    $app->forge('asset',
        function ($app)
        {
            return array(
                'access' => 'dashboard',
                'handle' => 'blackbaud_assistant_dashboard_styles',
                'source' => $app->get('assets_url') . 'css/dashboard.blackbaud-assistant.css'
            );
        });



    # BBI APP INITS.
    $app->forge('asset',
        function ($app)
        {
            return array(
                'type' => 'html',
                'access' => 'dashboard',
                'output' => function ($app)
                {
                    # Load all assets required for the Media Gallery picker.
                    wp_enqueue_media();

                    # Add our bbi action to the page.
                    return '<div data-bbi-app="BlackbaudWP" data-bbi-action="dashboard"></div>';
                }
            );
        });



    # BBI NAMESPACE.
    $app->forge('asset',
        function ($app) {
            /**
             * Prints in the HEAD.
             * Adds all plugin-specific BBI Namespace scripts.
             */
            return array(
                'type' => 'html',
                'access' => 'global',
                'in_footer' => false,
                'output' => function ($app)
                {
                    return $app->get_template('bbi-namespace.blackbaud-assistant.php', array('scripts' => $app->blackbaud->get('bbi_scripts')));
                }
            );
        });



    # UPDATER.
    $app->forge('updater');



    # ADD '$blackbaud' TO THE GLOBAL ARRAY.
    $GLOBALS['blackbaud'] = $app->module('Factory');



    # TELL OUR VARIOUS PLUGINS TO INITIALIZE!
    $app->module('Factory')->trigger('blackbaud_ready');


}


# CREATE THE BLACKBAUD APPLICATION.
blackbaud_assistant_init(new Application(array(
    'class_aliases' => array(
        'asset'                    => 'blackbaud\Asset',
        'custom_post_type'         => 'blackbaud\CustomPostType',
        'post_sortable_columns'    => 'blackbaud\PostsColumns',
        'meta_box'                 => 'blackbaud\MetaBox',
        'meta_field'               => 'blackbaud\MetaField',
        'shortcode'                => 'blackbaud\Shortcode',
        'settings_page'            => 'blackbaud\SettingsPage',
        'taxonomy_field'           => 'blackbaud\TaxonomyField',
        'taxonomy_fields'           => 'blackbaud\TaxonomyFields',
        'tinymce_shortcode_button' => 'blackbaud\TinyMCEShortcodeButton',
        'updater'                  => 'blackbaud\Updater'
    ),
    'alias'           => 'blackbaud',
    'text_domain'     => 'blackbaud_assistant',
    'plugin_file'     => __FILE__,
    'plugin_basename' => plugin_basename(__FILE__),
    'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
    'assets_url' => plugins_url('assets/', __FILE__)
)));
