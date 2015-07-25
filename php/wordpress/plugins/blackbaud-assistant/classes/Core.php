<?php
namespace blackbaud;
abstract class Core
{
    protected $defaults;
    protected $settings;

    public function __construct($options = array())
    {
        if (empty($this->defaults))
        {
            $this->defaults = array();
        }
        $this->settings = array_merge($this->defaults, $options);
        $this->make_properties($this->settings);
        if (method_exists($this, "start"))
        {
            $this->start($this->settings);
        }
    }

    public function get($key)
    {
        if (isset($this->$key))
        {
            return $this->$key;
        }
        return false;
    }

    public function set($key, $val)
    {
        $this->$key = $val;
        return $this;
    }

    public function print_tidy($arr = array(), $title = "")
    {
        if ($title)
        {
            echo "<h2>" . $title . "</h2>";
        }
        echo '<pre class="blackbaud-tidy-print" style="height:250px;overflow:auto;border:1px solid #ccc;background:#f2f1f0;margin:0 0 15px;">';
        print_r($arr);
        echo '</pre>';
    }

    public function safe_html($stringvar)
    {
        return stripslashes(esc_attr($stringvar));
    }

    public function view($file, $data = array())
    {
        if (! file_exists($file))
        {
            return "<strong>[blackbaud-assistant] Template file does not exist! $file</strong>";
        }
        ob_start();
        include $file;
        return ob_get_clean();
    }

    protected function make_properties($args = array())
    {
        foreach ($args as $key => $val)
        {
            if (! isset($val))
            {
                continue;
            }
            $this->$key = $val;
        }
    }

    public function get_template($file_name = "", $data = array())
    {
        $file_loc = $this->get('templates_directory') . $file_name;
        $theme_file = get_stylesheet_directory() . $file_loc;

        # Should we use the theme's version of the shortcode's template, or the default?
        if (file_exists($theme_file)) {
            $file = $theme_file;
        } else {
            $file = $file_loc;
        }

        return $this->view($file, $data);
    }
}
