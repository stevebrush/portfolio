<?php
namespace blackbaud;
class CustomPostType extends Core
{
    protected $app;
    protected $slug;
    protected $register_taxonomies = array();
    protected $defaults = array("public" => true);

    public function meta($postId = null, $field_slug = "", $decode = true)
    {
        $meta = get_post_meta($postId, $field_slug, true);
        if (! $decode)
        {
            return html_entity_decode($meta, ENT_QUOTES, 'UTF-8');
        }
        else
        {
            return $meta;
        }
    }

    public function on_activation()
    {
        # First, we add the custom post type.
        $this->register();

        # Reset Apache rewrites to accommodate our new post type.
        flush_rewrite_rules();
    }

    public function register()
    {
        register_post_type($this->slug, $this->settings);
    }

    public function register_taxonomies()
    {
        if (count($this->register_taxonomies) > 0)
        {
            foreach ($this->register_taxonomies as $k => $v)
            {
                register_taxonomy($k, $this->slug, $v);
            }
        }
    }

    public function setup_theme()
    {

        $supported_types = get_theme_support('post-thumbnails');

        # Add theme support for post thumbnails.
        if ($supported_types === false)
        {
            add_theme_support('post-thumbnails', array($this->slug));
        }
        else if (is_array($supported_types))
        {
            $supported_types[0][] = $this->slug;
            add_theme_support('post-thumbnails', $supported_types[0]);
        }
    }

    protected function start()
    {
        # Run these functions on dashboard and front-end.
        add_action("init", array($this, "register"));
        add_action("init", array($this, "register_taxonomies"));

        # If the post type wants to use featured images, make sure the theme supports it.
        if (in_array("thumbnail", $this->supports))
        {
            add_action("after_setup_theme", array($this, "setup_theme"), 11);
        }

        # WordPress dashboard, only.
        if (is_admin())
        {
            # Update the wording for various messages on the dashboard.
            add_filter("post_updated_messages", array($this, "update_dashboard_messages"));

            # Do this when the plugin is activated for the first time.
            if (! $file = $this->app->get("plugin_file"))
            {
                register_activation_hook($file, array($this, "on_activation"));
            }

            # Title placeholder
            if (isset($this->title_placeholder))
            {
                add_filter('enter_title_here', array($this, "title_placeholder"));
            }
        }
    }

    public function title_placeholder($title)
    {
        $screen = get_current_screen();
        if ($this->slug == $screen->post_type) {
            $title = $this->title_placeholder;
        }
        return $title;
    }

    public function update_dashboard_messages($messages = array())
    {
        # Change the wording for various success/error messages to reflect our custom post type.
        global $post;
        global $post_ID;

        $obj       = get_post_type_object($this->slug);
        $singular  = $obj->labels->singular_name;
        $permalink = get_permalink($post_ID);

        $messages[$this->slug] = array(
            1  => sprintf(__($singular . ' updated. <a href="%s">View ' . strtolower($singular) . '</a>'), esc_url($permalink)),
            2  => __("Custom field updated."),
            3  => __("Custom field deleted."),
            4  => __($singular . " updated."),
            5  => isset($_GET["revision"]) ? sprintf(__($singular . " restored to revision from %s"), wp_post_revision_title((int) $_GET["revision"], false)) : false,
            6  => sprintf(__($singular . ' published. <a href="%s">View ' . strtolower($singular) . '</a>'), esc_url($permalink)),
            7  => __("Page saved."),
            8  => sprintf(__($singular . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower($singular) . '</a>'), esc_url(add_query_arg('preview', 'true', $permalink))),
            9  => sprintf(__($singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . strtolower($singular) . '</a>'), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url($permalink)),
            10 => sprintf(__($singular . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower($singular) . '</a>'), esc_url(add_query_arg('preview', 'true', $permalink))),
        );
        return $messages;
    }

}
