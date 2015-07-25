<?php
namespace blackbaud;
class ApplicationModule extends Core
{
    public $app;
    private $reserved_keys = array('app','blackbaud');

    public function __construct(array $arguments = array(), Application $app)
    {
        if (!empty($arguments))
        {
            foreach ($arguments as $property => $argument)
            {
                foreach ($this->reserved_keys as $key)
                {
                    if ($property == $key)
                    {
                        die("The key '{$key}' is reserved. Please choose another property name for your module.");
                        break;
                    }
                }
                $this->{$property} = $argument;
            }
        }

        $this->app = $app;

        # Execute the object's start() method.
        if (isset($this->start) && is_callable($this->start))
        {
            call_user_func_array($this->start, array($this));
        }
    }

    public function __call($method, $arguments)
    {
        if (is_callable($this->{$method}))
        {
            if (isset($arguments[0]) && $arguments[0])
            {
                $arguments[] = &$this;
            }
            else
            {
                $arguments = array(&$this);
            }
            return call_user_func_array($this->$method, $arguments);
        }
    }
}
