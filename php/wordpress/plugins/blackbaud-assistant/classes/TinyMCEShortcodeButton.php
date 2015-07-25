<?php
namespace blackbaud;
class TinyMCEShortcodeButton extends Core
{
    protected $slug;
    protected $post_type;
    protected $shortcode_slug;
    protected $shortcode_id_key;
    protected $javascript_file;
    protected $defaults = array("shortcode_id_key" => 'post_id');

    public function add_button($buttons)
    {
        # Add a separation before our button.
        array_push($buttons, "|", $this->slug);
        return $buttons;
    }

    public function add_js_plugin($plugins)
    {
        global $post;

        # Save the global $post variable so we can use it later.
        $temp = $post;
        $data = array();
        $args = array(
            'post_type' => $this->post_type,
            'post_status' => 'publish',
            'posts_per_page' => '-1',
            'ignore_sticky_posts'=> 1,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $query = new \WP_Query($args);

        if ($query->have_posts())
        {
            while ($query->have_posts())
            {
                $query->the_post();
                $data[] = array(
                    "title" => get_the_title(),
                    "shortcode" => '<!-- ' . get_the_title() . ' -->[' . $this->shortcode_slug . ' ' . $this->shortcode_id_key . '="' . get_the_ID() . '"]'
                );
            }
        }

        echo '<script>';
        echo '(function($) {';
        echo 'window.BlackbaudTinyMCEData = window.BlackbaudTinyMCEData || {};';
        echo 'window.BlackbaudTinyMCEData["' . $this->slug . '"] = ' . json_encode ($data) . ';';
        echo '})(jQuery);';
        echo '</script>';

        # This plugin file will work the magic of our button.
        $plugins[$this->slug] = $this->javascript_file;

        # Set the global $post object back to what it was.
        $GLOBALS["post"] = $temp;

        return $plugins;
    }

    public function register()
    {
        if (current_user_can("edit_posts") && current_user_can("edit_pages"))
        {
            add_filter("mce_buttons", array($this, "add_button"));
            add_filter("mce_external_plugins", array($this, "add_js_plugin"));
        }
    }

    protected function start()
    {
        if (is_admin())
        {
            add_action("admin_init", array($this, "register"));
        }
    }
}
