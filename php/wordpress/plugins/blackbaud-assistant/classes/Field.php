<?php
namespace blackbaud;
abstract class Field extends Core
{
    protected $slug;
    protected $name;
    protected $id;
    protected $helplet;
    protected $type;
    protected $label;
    protected $value;
    protected $default;
    protected $attributes;
    protected $attributes_html;
    protected $parent_attributes_html;
    protected $default_attributes = array();
    protected $parent_attributes = array();
    protected $default_parent_attributes = array("class" => "form-group");
    protected $template;

    public function build()
    {
        $this->attributes_html = $this->build_attributes();
        $this->parent_attributes_html = $this->build_parent_attributes();
        return $this;
    }

    public function render()
    {
        $data = array(
            "value" => $this->safe_html($this->value),
            "label" => $this->label,
            "type" => $this->type,
            "attributes" => $this->attributes_html,
            "parent_attributes" => $this->parent_attributes_html,
            "id" => $this->id,
            "name" => $this->name,
            "helplet" => $this->helplet,
            "options" => (isset($this->options)) ? $this->options : array()
        );
        echo $this->app->blackbaud->app->get_template($this->template, $data);
    }

    private function build_attributes()
    {
        $html = "";
        if (!isset($this->attributes))
        {
            return $html;
        }
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

    abstract function set_default_value();

}
