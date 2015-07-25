<?php
namespace blackbaud;
class TaxonomyField extends Field
{
    protected $taxonomy;
    protected $term;
    public function start()
    {
        if (is_admin())
        {
            $this->name = 'term_meta[' . $this->slug . ']';
            $this->id = $this->name;

            add_action($this->taxonomy . '_add_form_fields', array($this, 'display_create'), 10, 2);
            add_action($this->taxonomy . '_edit_form_fields', array($this, 'display_edit'), 10, 2);
            add_action('edited_' . $this->taxonomy, array($this, 'update'), 10, 2);
            add_action('create_' . $this->taxonomy, array($this, 'update'), 10, 2);
        }
    }

    public function display_create($term)
    {
        $this->template = 'taxonomy-field-create.blackbaud-assistant.php';
        $this->set_default_value();
    	echo $this->build()->render();
    }

    public function display_edit($term)
    {
        $this->template = 'taxonomy-field-edit.blackbaud-assistant.php';
        $this->term = $term;
        $this->set_default_value();
    	$this->build()->render();
    }

    public function update($term_id)
    {
        if (isset($_POST['term_meta']))
        {
            $term_meta = get_option("taxonomy_$term_id");
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key)
            {
                if (isset($_POST['term_meta'][$key]))
                {
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            # Save the option array.
            update_option("taxonomy_$term_id", $term_meta);
        }
    }

    public function set_default_value()
    {
        $term_id = (isset($this->term->term_id)) ? $this->term->term_id : '';
        $term_meta = get_option("taxonomy_" . $term_id);
    	$result = (isset($term_meta[$this->slug])) ? esc_attr($term_meta[$this->slug]) : '';
    	$value = "";

        # Nothing in the database.
    	if (empty($term_meta))
    	{
        	$value = $this->default;
    	}

        # Save the database value.
        else if (!empty($result))
        {
            $value = $result;
        }

        $this->value = $value;
    }

}
