<?php
namespace blackbaud;
class Asset extends Core
{
    protected $defaults = array (
        "access" => "global",
        "dependencies" => array(),
        "version" => "1.0",
        "in_footer" => true
    );
    protected $type;
    protected $access;
    protected $for_shortcode;
    protected $handle;
    protected $source;
    protected $dependencies;
    protected $version;
    protected $in_footer;
    protected $output;

    protected function start($settings)
    {
        # Set the type based on the source's extension.
        if (isset($this->source) && (!isset($this->type) || empty($this->type)))
        {
            $this->type = substr(strrchr($this->source, '.'), 1);
        }

        # Check if this post/page contains the required shortcode.
        if (isset($this->for_shortcode) && !empty($this->for_shortcode))
        {
            add_filter('the_posts', array($this, 'look_for_shortcode'));
            return; // quit no matter what
        }

        # Add it according to it's access.
        $this->enqueue();
    }

    public function add_stylesheet()
    {
        wp_enqueue_style($this->handle, $this->source, $this->dependencies, $this->version);
    }

    public function add_script()
    {
        $this->print_tidy($this->handle, "HANDLES");
        wp_enqueue_script($this->handle, $this->source, $this->dependencies, $this->version, $this->in_footer);
    }

    public function add_html()
    {
        echo call_user_func_array($this->output, array($this->app, $this->app->blackbaud));
    }

    private function enqueue()
    {
        if (is_admin())
        {
            if ($this->access === "global" || $this->access === "dashboard")
            {
                switch ($this->type)
                {
                    case "js":
                    add_action ("admin_enqueue_scripts", array ($this, "add_script"));
                    break;

                    case "css":
                    add_action ("admin_enqueue_scripts", array ($this, "add_stylesheet"));
                    break;

                    case "html":
                    case "php":
                    if ($this->in_footer)
                    {
                        add_action ('admin_footer', array($this, "add_html"));
                    }
                    else
                    {
                        add_action ('admin_head', array($this, "add_html"));
                    }
                    break;
                }
            }
        }
        else if ($this->access != "dashboard")
        {
            switch ($this->type)
            {
                case "js":
                if ($this->in_footer)
                {
                    add_action("wp_footer", array ($this, "add_script"));
                }
                else
                {
                    add_action("wp_head", array ($this, "add_script"));
                }
                break;

                case "css":
                add_action("wp_print_styles", array ($this, "add_stylesheet"));
                break;

                case "html":
                case "php":
                if ($this->in_footer)
                {
                    add_action ('wp_footer', array($this, "add_html"));
                }
                else
                {
                    add_action ('wp_head', array($this, "add_html"));
                }
                break;
            }
        }
    }

    public function look_for_shortcode($posts)
    {
        if (empty($posts)) {
            return $posts;
        }

        $shortcode_found = false;

    	foreach ($posts as $post)
    	{
    		if (stripos($post->post_content, '[' . $this->for_shortcode) !== false)
    		{
    			$shortcode_found = true;
    			break;
    		}
    	}

    	if ($shortcode_found) {
    		$this->enqueue();
    	}

    	return $posts;
    }

}
