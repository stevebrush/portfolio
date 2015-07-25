<?php
namespace blackbaud;
class PostsColumns extends Core
{
    protected $columns = array();
    protected $post_type = "post";

    public function manager($columns)
    {
        $temp = array();
        if (count($this->columns) > 0)
        {
            foreach ($this->columns as $k => $v)
            {
                $temp[$k] = $v["label"];
            }
        }
        return array_merge($columns, $temp);
    }

    public function posts_pre_query($query)
    {
        if ($query->is_main_query() &&($orderby = $query->get("orderby")))
        {
            if (count($this->columns) > 0)
            {
                foreach ($this->columns as $k => $v)
                {
                    if (isset($v["meta_key"]))
                    {
                        if ($orderby == $v["meta_key"])
                        {
                            $query->set("meta_key", $v["meta_key"]);
                            $query->set("orderby", $v["orderby"]);
                        }
                    }
                }
            }
        }
        return $query;
    }

    public function sortable($sortable_columns)
    {
        if (count($this->columns) > 0) {
            foreach ($this->columns as $k => $v)
            {
                if (isset($v["meta_key"]))
                {
                    $sortable_columns[$k] = $v["meta_key"];
                }
            }
        }
        return $sortable_columns;
    }

    public function values($column, $post_id)
    {
        if (isset($this->columns[$column]))
        {
            $callback = $this->columns[$column]["value"];
            $atts = array(
                "column" => $column,
                "post_id" => $post_id
            );
            if (isset($callback) && is_callable($callback))
            {
                echo call_user_func_array($callback, array($atts, $this->app, $this->app->blackbaud));
            }
            else
            {
                echo "";
            }

            /*
            # The command string must end with a semicolon.
            if (strpos($str, ";") === false)
            {
                $str = $str . ";";
            }

            # The command string must start with 'echo'.
            if (strpos($str, "echo") === false)
            {
                $str = "echo " . $str;
            }

            eval($str);
            */

        }
        else
        {
            echo "";
        }
    }

    protected function start()
    {
        add_action("manage_" . $this->post_type . "_posts_columns", array($this, "manager"));
        add_action("manage_" . $this->post_type . "_posts_custom_column", array($this, "values"), 5, 2);
        add_filter("manage_edit-" . $this->post_type . "_sortable_columns", array($this, "sortable"));
        add_action("pre_get_posts", array($this, "posts_pre_query"), 1);
    }
}
