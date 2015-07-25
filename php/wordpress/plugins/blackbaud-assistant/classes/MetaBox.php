<?php
namespace blackbaud;
class MetaBox extends Core
{
    protected $app;
    protected $post_type;
    protected $slug;
    protected $label;
    protected $priority;
    protected $nonce_label;
    protected $nonce_action_label;
    protected $template_file;
    protected $fields = array();
    protected $defaults = array(
        "context" => "advanced",
        "priority" => "default",
        'slug' => 'settings'
    );

    public function create($post)
    {
        add_meta_box($this->post_type . "_" . $this->slug, __($this->label, $this->post_type), array($this, "display"), $this->post_type, $this->context, $this->priority);
    }

    public function display($post)
    {
        global $blackbaud;

        $data        = array();
        $fields_html = array();
        $nonce_field = wp_nonce_field($this->nonce_action_label, $this->nonce_label, true, false);

        # Make sure our form gets a security signature.
        if (function_exists("wp_nonce_field"))
        {
            $fields_html[] = array(
                "containerStart" => '',
                "label"          => "",
                "input"          => $nonce_field,
                "containerStop"  => '',
                "html"           => $nonce_field
            );
        }

        # Set the HTML for the appropriate fields.
        if (count($this->field_args))
        {
            foreach ($this->field_args as $this_field)
            {
                $this_field["post"] = $post;
                $this->app->forge("meta_field", $this_field);
                $field = $this->app->last_forged;
                $fields_html[] = array(
                    "containerStart" => $field->render("containerStart", false),
                    "label"          => $field->render("label", false),
                    "input"          => $field->render("input", false),
                    "containerStop"  => $field->render("containerStop", false),
                    "html"           => $field->render("all", false)
                );
                $this->fields[] = $field;
            }
        }

        # Print the HTML.
        echo $this->app->blackbaud->app->get_template("meta-box.blackbaud-assistant.php", array("fields" => $fields_html));
    }

    public function save($postId)
    {
        global $blackbaud;

        $post = get_post($postId);
        $saved = array();

        # Check if our nonce is set.
        if (! isset($_POST[$this->nonce_label]))
        {
            return $postId;
        }

        $nonce = $_POST[$this->nonce_label];

        # Only mess with posts that use our post type.
        if ($this->post_type != $_POST['post_type'])
        {
            return $postId;
        }

        # Verify that the nonce action is valid.
        if (! wp_verify_nonce($nonce, $this->nonce_action_label))
        {
            return $postId;
        }

        # If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        {
            return $postId;
        }

        # Check the user's permissions.
        # Editing a page.
        if ("page" === $_POST["post_type"])
        {
            if (! current_user_can("edit_page", $postId))
            {
                return $postId;
            }
        }

        # Editing a post.
        else if (! current_user_can("edit_post", $postId))
        {
            return $postId;
        }

        # !
        # We've made it this far, let's save the post meta!
        # !

        # Get the field objects.
        if (count($this->field_args))
        {
            foreach ($this->field_args as $this_field)
            {
                $this_field["post"] = $post;
                $this->app->forge("meta_field", $this_field);
                $saved[] = $this->app->last_forged;
            }
        }

        # Sanitize the user input.
        if (count($saved))
        {
            foreach ($saved as $f)
            {
                if (isset($_POST[$f->get("slug")]))
                {
                    if ($_POST[$f->get("slug")] === "on")
                    {
                        update_post_meta($postId, $f->get("slug"), "true");
                    }
                    else
                    {
                        update_post_meta($postId, $f->get("slug"), $_POST[$f->get("slug")]);
                    }
                }
                else
                {
                    update_post_meta($postId, $f->get("slug"), "false");
                }
            }
        }
    }

    protected function add_field($args = array())
    {
        $this->field_args[] = $args;
        return $this;
    }

    protected function start($settings = array())
    {
        global $blackbaud;

        $this->slug .= "_metabox";
        $this->nonce_label = $this->post_type . "_" . $this->slug . "_nonce";
        $this->nonce_action_label = $this->post_type . "_" . $this->slug;

        # Template file for metaboxes.
        if (! isset($this->template_file) && isset($blackbaud))
        {
            $this->template_file = $blackbaud->get("templates_directory") . "meta-box.blackbaud-assistant.php";
        }

        # Process several fields at once if provided in options.
        if (isset($this->fields))
        {
            foreach ($this->fields as $field)
            {
                $this->add_field($field);
            }
        }

        # Build and save the fields.
        if (is_admin())
        {
            add_action("add_meta_boxes_" . $this->post_type, array($this, "create"));
            add_action("save_post_" . $this->post_type, array($this, "save"));
        }
    }
}
