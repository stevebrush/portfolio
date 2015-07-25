<?php
namespace blackbaud;
class TaxonomyFields extends Core
{
    protected $fields;
    protected $taxonomy;
    protected function start()
    {
        foreach ($this->fields as $field)
        {
            $field['taxonomy'] = $this->taxonomy;
            $this->app->forge("taxonomy_field", $field);
        }
    }
}
