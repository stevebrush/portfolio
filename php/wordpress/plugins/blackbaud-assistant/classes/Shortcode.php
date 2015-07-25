<?php
namespace blackbaud;
class Shortcode extends Core
{
    protected $slug;
    protected $output;

    public function display($atts = array())
    {
        if (isset($this->output) && is_callable($this->output))
        {
            return call_user_func_array($this->output, array($atts));
        }
        else
        {
            return "";
        }
    }

    protected function start()
    {
        if (! is_admin())
        {
            add_shortcode($this->slug, array($this, "display"));
        }
    }
}
