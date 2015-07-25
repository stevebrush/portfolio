<?php
namespace blackbaud;
class SettingsPage extends Core
{
    protected $defaults;
    protected $default_field = array(
        "slug" => null,
        "type" => "text",
        "label" => "",
        "default" => ""
    );

    public function __construct($options = array())
    {
        $this->defaults = array(
            "slug" => null,
            "parent_slug" => null,
            "page_title" => "Settings",
            "menu_title" => "Settings",
            "never_save" => false,
            "capability" => "manage_options",
            "callbacks" => array(
                "display" => array($this, "display"),
                "validation" => function ($r)
                {
                    return $r;
                }
            ),
            "sections" => array(
                "sample_section" => array(
                    "title" => "Sample Section",
                    "callback" => function ()
                    {},
                    "fields" => array()
                )
            )
        );

        $this->default_field['callback'] = array($this, "build_field");

        if (isset($options["callbacks"]))
        {
            $options["callbacks"] = array_merge($this->defaults["callbacks"], $options["callbacks"]);
        }

        parent::__construct($options);

    }

    /**
     * Adds a submenu page based on a parent slug.
     * This method also attaches the 'display' callback, which determines what
     * content gets added to the page.
     */
    public function add_submenu()
    {
        add_submenu_page($this->settings["parent_slug"], $this->settings["page_title"], $this->settings["menu_title"], $this->settings["capability"], $this->settings["slug"], array($this, "callback_display"));
    }

    /**
     * Prints the field's HTML.
     */
    public function build_field($field = array())
    {
        $settings_slug = $this->settings["slug"];
        $field_slug = $field["slug"];
        $storage = get_option($settings_slug);
        $html = "";

        # Set the label's "for" attribute.
        if (! isset($field["label_for"]))
        {
            $field["label_for"] = $field_slug;
        }

        # Get saved value, or set to default.
        if (empty($storage)) {
            if (isset($field["default"])) {
                $value = $field["default"];
            } else {
                $value = "";
            }
        } else {
            if (isset($storage[$field_slug])) {
                $value = $storage[$field_slug];
            } else {
                $value = "";
            }
        }

        # Get the HTML.
        switch ($field["type"])
        {
            case "text":
            default:
            $html = '<input name="' . $settings_slug . '[' . $field_slug . ']" id="' . $field["label_for"] . '" type="text" value="' . $value . '">';
            break;

            case "checkbox":
            $html = '<label><input name="' . $settings_slug . '[' . $field_slug . ']" id="' . $field["label_for"] . '" type="checkbox" value="1" ' . checked($value, 1, false) . '> ' . $field["label"] . '</label>';
            break;

            case "select":
            $html = '<select name="' . $settings_slug . '[' . $field_slug . ']" id="' . $field["label_for"] . '">';
            foreach ($field['options'] as $k => $v)
            {
                $html .= '<option' . selected($value, $v, false) . ' value="' . $v . '">' . $k . '</option>';
            }
            $html .= '</select>';
            break;
        }

        echo $html;

    }

    /**
     * This method executes the callback function 'display', which can be overwritten
     * by the app.
     */
    public function callback_display()
    {
        if (is_callable($this->settings["callbacks"]["display"]))
        {
            call_user_func_array($this->settings["callbacks"]["display"], array($this->app, $this->app->blackbaud));
        }
    }

    /**
     * This is the default 'display' callback method.
     */
    public function display($app, $blackbaud)
    {
        $data = array(
            "page_id" => $this->slug,
            "page_title" => __($this->page_title, $this->app->get("text_domain"))
        );
        echo $this->app->blackbaud->app->get_template("settings-page.blackbaud-assistant.php", $data);
    }

    /**
     * Displays the HTML of the settings page.
     * Also takes care of what value should be inserted in the fields.
     */
    public function register_fields()
    {
        $settings_slug = $this->settings["slug"];

        # Always reset the options?
        if ($this->settings['never_save'])
        {
            delete_option($settings_slug);
        }

        register_setting($settings_slug, $settings_slug, $this->settings["callbacks"]["validation"]);

        $this->set_fields_default_values();

        foreach ($this->settings["sections"] as $section_slug => $section)
        {
            if (! isset($section["callback"]))
            {
                $section["callback"] = function() {};
            }

            add_settings_section($section_slug, $section["title"], $section["callback"], $settings_slug);

            foreach ($section["fields"] as $field)
            {
                $field = array_merge($this->default_field, $field);

                # Remove the label if it's a checkbox.
                $label = ($field["type"] == "checkbox") ? "" : $field["label"];
                add_settings_field($field["slug"], $label, $field['callback'], $settings_slug, $section_slug, $field);
            }
        }
    }

    /**
     * Sets default values if settings option does not exist.
     */
    public function set_fields_default_values()
    {
        $settings_slug = $this->settings["slug"];
        if (false === ($options = get_option($settings_slug)))
        {
            $defaults = array();
            foreach ($this->settings['sections'] as $section)
            {
                foreach ($section['fields'] as $field)
                {
                    $defaults[$field['slug']] = $field['default'];
                }
            }
            update_option($settings_slug, $defaults);
        }
    }

    /**
     * Initializer.
     */
    public function start()
    {
        register_activation_hook($this->app->get('plugin_file'), array($this, "set_fields_default_values"));
        add_action("admin_menu", array($this, "add_submenu"));
        add_action("admin_init", array($this, "register_fields"));
    }
}
