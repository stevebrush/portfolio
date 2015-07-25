<?php
namespace blackbaud;

class Application extends Core
{
    public $blackbaud;
    public $last_forged;
    protected $plugin_file;
    protected $text_domain;
    private $actions = array();
    protected $modules = array();
    private $forged = array();
    public $last_module;

    public function add_module($slug, $callback)
    {
        if (is_callable($callback))
        {
            $options = call_user_func_array($callback, array($this, $this->blackbaud));
            $this->last_module = new ApplicationModule($options, $this);
            $this->modules[$slug] = $this->last_module;
            return $this->last_module;
        }
        return $this;
    }

    public function forge($what = "", $request = null)
    {
        # Determine how the options variable is generated.
        # Can be an array or returned from a callback.
        if (is_array($request))
        {
            $options = $request;
        }
        else if (is_callable($request))
        {
            $options = call_user_func_array($request, array($this, $this->blackbaud));
        }
        else
        {
            $options = array();
        }

        # Send the app as a property of this object.
        if (! isset($options["app"]))
        {
            $options["app"] = $this;
        }

        # Determine what to do based on what's being forged.
        switch ($what)
        {
            case "bbi_script":
            $this->last_forged = $this->blackbaud->add_bbi_script($options);
            break;

            default:
            $this->last_forged = $this->instantiate($what, $options);
            break;
        }

        if ($this->last_forged)
        {
            $this->forged[$what][] = $this->last_forged;
        }

        return $this->last_forged;

    }

    public function forged($key = "", $returnOne = false)
    {
        if (isset($this->forged[$key]))
        {
            if ($returnOne)
            {
                return $this->forged[$key][0];
            }
            else
            {
                return $this->forged[$key];
            }
        }
        else
        {
            return false;
        }
    }

    private function instantiate($what, $options)
    {
        $aliases = $this->blackbaud->app->get("class_aliases");

        # No aliases are set, so we won't be able to create an object.
        # Make sure the aliases are being sent through the global $blackbaud object.
        if (! isset($aliases[$what])) {
            return false;
        }

        # We'll be saving this object in this application.
        # Make sure the array is ready to store it.
        if (! isset($this->forged[$what]))
        {
            $this->forged[$what] = array();
        }

        # Create the object and add it to storage.
        return new $aliases[$what]($options);
    }

    protected function start($settings = array())
    {
        $error_prefix = '[! BLACKBAUD PLUGIN ERROR !] Insufficient data provided to Blackbaud::register() method. ';
        if (!isset($settings['plugin_file']))
        {
            die($error_prefix . "Your plugin application '$this->alias' must have the property 'plugin_file' set.");
        }
        if (!isset($settings['plugin_basename']))
        {
            die($error_prefix . "Your plugin application '$this->alias' must have the property 'plugin_basename' set.");
        }
        if (!isset($settings['text_domain']))
        {
            die($error_prefix . "Your plugin application '$this->alias' must have the property 'text_domain' set.");
        }
    }

    public function module($slug, $constructor = null)
    {
        if (is_callable($constructor))
        {
            return $this->add_module($slug, $constructor);
        }
        return $this->modules[$slug];
    }
}
