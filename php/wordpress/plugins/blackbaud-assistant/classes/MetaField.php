<?php
namespace blackbaud;
class MetaField extends Core
{
    protected $post;
    protected $slug;
    protected $attributes = array();
    protected $parent_attributes = array();
    protected $html = array();
    protected $default_attributes = array();
    protected $default_parent_attributes = array("class" => "form-group");

    public function render($what = "all", $echo = true)
    {
        $html = "";

        switch ($what)
        {

            case "label":
            $html .= $this->html["label"];
            break;

            case "input":
            if (isset($this->html["input"]))
            {
                $html .= $this->html["input"];
            }
            break;

            default:
            $html .= $this->html["containerStart"];
            $html .= '<div class="form-label-container">';
            $html .= $this->html["label"];
            $html .= '</div>';
            $html .= '<div class="form-control-container">';
            $html .= $this->html["input"];
            $html .= $this->html["helplet"];
            $html .= '</div>';
            $html .= $this->html["containerStop"];
            break;

        }

        if ($echo)
        {
            echo $html;
        }

        else
        {
            return $html;
        }

    }

    protected function start()
    {
        if ($this->type != "checkbox" && !isset($this->attributes['class']))
        {
            $this->default_attributes["class"] = "form-control";
        }

        # Assign value.
        $data = get_post_custom($this->post->ID);
        if (empty($data) && isset($this->default))
        {
            # The post is being created for the first time.
            # Prefill the value with a default, if available.
            $this->value = $this->default;
        }
        else
        {
            # Get the field's value from the database.
            $this->value = get_post_meta($this->post->ID, $this->slug, true);
        }

        # Build the field.
        $this->build();
    }

    private function build()
    {
        $this->attributesHtml = $this->build_attributes();
        $this->html["helplet"] = '';
        $this->html["containerStart"] = '<div' . $this->build_parent_attributes() . '>';
        $this->html["containerStop"] = '</div>';

        switch ($this->type)
        {
            case "text":
            $this->html["label"]   = '<label for="' . $this->slug . '" class="control-label">' . $this->label . '</label>';
            $this->html["input"]   = '<input type="text" id="' . $this->slug . '" name="' . $this->slug . '" value="' . $this->value . '"' . $this->attributesHtml . '>';
            $this->html["helplet"] = (isset($this->helplet)) ? '<div class="help-block">' . $this->helplet . '</div>': '';
            break;

            case "textarea":
            $this->html["label"] = '<label for="' . $this->slug . '" class="control-label">' . $this->label . '</label>';
            $this->html["input"] = '<textarea id="' . $this->slug . '" name="' . $this->slug . '"' . $this->attributesHtml . '>' . $this->value . '</textarea>';
            break;

            case "checkbox":
            $checked             = ($this->value == "true" || $this->value == "on" || $this->value == "1") ? ' checked' : '';
            $this->html["label"] = '';
            $this->html["input"] = '<label><input type="checkbox" id="' . $this->slug . '" name="' . $this->slug . '"' . $this->attributesHtml . $checked . '>' . $this->label . '</label>';
            break;

            case "media-gallery-picker":
            $this->html["label"] = '<label for="' . $this->slug . '" class="control-label">' . $this->label . '</label>';
            $this->html["input"] = '<div class="blackbaud-metabox-gallery-picker"><input type="text" id="' . $this->slug . '" name="' . $this->slug . '" value="' . $this->value . '"' . $this->attributesHtml . '><a href="#" class="button blackbaud-metabox-gallery-picker-button">Select Image</a></div>';
            break;
        }
    }

    private function build_attributes()
    {
        $html = "";
        $this->attributes = array_merge($this->default_attributes, $this->attributes);
        if (count($this->attributes))
        {
            foreach ($this->attributes as $k => $v)
            {
                $html .= ' ' . $k . '="'. $v . '"';
            }
        }
        return $html;
    }

    private function build_parent_attributes()
    {
        $html = "";
        $this->parent_attributes = array_merge($this->default_parent_attributes, $this->parent_attributes);
        if (count($this->parent_attributes))
        {
            foreach ($this->parent_attributes as $k => $v)
            {
                $html .= ' ' . $k . '="'. $v . '"';
            }
        }
        return $html;
    }

}
