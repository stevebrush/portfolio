<?php
if (class_exists ("CustomPostType")) exit ();
class CustomPostType extends BB_PluginHelper {



	public $slug = "";

	protected $pluginMainFile;
	private $args = array ();
	private $columns = array ();
	private $taxonomies = array ();
	private $defaults = array ("public" => true);



	public function __construct ($args = array (), BlackbaudCPT $factory) {

		$this->slug = $args ["slug"];
		$this->args = array_merge ($this->defaults, $args);
		$this->factory = $factory;

		$this->CheckAdmin ();
		$this->Setup ();

	}



	private function Setup () {

		# Run these functions on dashboard and front-end.
		add_action ("init", array ($this, "Register"));
		add_action ("init", array ($this, "RegisterTaxonomies"));

		# WordPress dashboard, only.
		if ($this->isAdmin) {

			add_action ("init", array ($this, "RegisterDashboardAssets"));
			add_action ("admin_enqueue_scripts", array ($this, "PrintDashboardAssets"));
			add_filter ("post_updated_messages", array ($this, "UpdateDashboardMessages"));

			# Do this when the plugin is activated for the first time.
			if (isset ($this->pluginMainFile)) {
				register_activation_hook ($this->pluginMainFile, array ($this, "OnActivation"));
			}
		}
	}



		public function RegisterDashboardAssets () {
			wp_register_style ("bbi_helpers_dashboard_styles", $this->factory->Config ("url", "css") . "dashboard-styles.css");
		}



		public function PrintDashboardAssets () {
			wp_enqueue_style ("bbi_helpers_dashboard_styles");
		}



		public function UpdateDashboardMessages ($messages) {

			global $post;
			global $post_ID;

			$obj = get_post_type_object ($this-> slug);
			$singular = $obj-> labels-> singular_name;

			$messages [$this-> slug] = array (
				1  => sprintf (__ ($singular . ' updated. <a href="%s">View ' . strtolower ($singular) . '</a>'), esc_url (get_permalink ($post_ID))),
				2  => __ ('Custom field updated.'),
				3  => __ ('Custom field deleted.'),
				4  => __ ($singular . ' updated.'),
				5  => isset ($_GET ['revision']) ? sprintf (__ ($singular . ' restored to revision from %s'), wp_post_revision_title ((int) $_GET ['revision'], false)) : false,
				6  => sprintf (__ ($singular . ' published. <a href="%s">View ' . strtolower ($singular) . '</a>'), esc_url (get_permalink ($post_ID))),
				7  => __ ('Page saved.'),
				8  => sprintf (__ ($singular . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower ($singular) . '</a>'), esc_url (add_query_arg ('preview', 'true', get_permalink ($post_ID)))),
				9  => sprintf (__ ($singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . strtolower ($singular) . '</a>'), date_i18n (__ ('M j, Y @ G:i'), strtotime ($post->post_date)), esc_url (get_permalink ($post_ID))),
				10 => sprintf (__ ($singular . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower ($singular) . '</a>'), esc_url (add_query_arg ('preview', 'true', get_permalink ($post_ID)))),
			);

			return $messages;

		}



		public function OnActivation () {

		    # First, we "add" the custom post type.
		    $this->Register ();

		    # Reset Apache rewrites to accommodate our new post type.
		    flush_rewrite_rules();

		}



	public function Register () {
		register_post_type ($this->slug, $this->args);
	}



	public function RemoveSlugFromPermalink () {

		add_filter('post_type_link', array ($this, "RemovePostTypeSlug"), 10, 3);
		add_action('pre_get_posts', array ($this, "PostTypeTrickery"));
	}



		/**
		 * Remove the slug from published post permalinks.
		 **/
		public function RemovePostTypeSlug ($post_link, $post, $leavename) {

			if ($this->slug != $post->post_type || $post->post_status != "publish") {
		        return $post_link;
		    }

		    $post_link = str_replace("/" . $post->post_type . "/", "/", $post_link);

		    return $post_link;

		}



		/**
		 * Some hackery to have WordPress match postname to any of our public post types
		 * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
		 * Typically core only accounts for posts and pages where the slug is /post-name/
		 */
		public function PostTypeTrickery ($query) {

			// Only noop the main query
		    if (! $query->is_main_query()) {
		        return;
		    }

		    // Only noop our very specific rewrite rule match
		    if (! isset ($query->query ["page"])) {
		        return;
		    }

		    // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
		    if (! empty ($query->query ["name"])) {
		        $query->set ("post_type", array ("post", $this->slug, "page"));
		    }

		}



	public function Columns (Array $args = array ()) {
		$this->columns = $args;
		add_action ("manage_" . $this-> slug . "_posts_columns", array ($this, "ManagePostsColumns"));
		add_action ("manage_" . $this-> slug . "_posts_custom_column", array ($this, "ManagePostsColumnsValues"), 5, 2);
		add_filter ("manage_edit-" . $this-> slug . "_sortable_columns", array ($this, "ManagePostsColumnsSortable"));
		add_action ("pre_get_posts", array ($this, "ManagePostsPreQuery"), 1);
	}



		public function ManagePostsColumns ($columns) {
			$temp = array ();
			if (count ($this->columns) > 0) {
				foreach ($this->columns as $k => $v) {
					$temp [$k] = $v ["label"];
				}
			}
			return array_merge ($columns, $temp);
		}



		public function ManagePostsColumnsValues ($column, $post_id) {
			if (isset ($this->columns [$column])) {
				$str = $this->columns [$column] ["value"];
				if (strpos ($str, ";") === false) {
					$str = $str . ";";
				}
				if (strpos ($str, "echo") === false) {
					$str = "echo " . $str;
				}
				eval ($str);
			} else {
				echo "";
			}
		}



		public function ManagePostsColumnsSortable ($sortable_columns) {
			if (count ($this->columns) > 0) {
				foreach ($this->columns as $k => $v) {
					if (isset ($v ["meta_key"])) {
						$sortable_columns [$k] = $v ["meta_key"];
					}
				}
			}
			return $sortable_columns;
		}



		public function ManagePostsPreQuery ($query) {
			if ($query->is_main_query () && ($orderby = $query->get ('orderby'))) {
				if (count ($this->columns) > 0) {
					foreach ($this->columns as $k => $v) {
						if (isset ($v ["meta_key"])) {
							if ($orderby == $v ["meta_key"]) {
								$query->set ('meta_key', $v ["meta_key"]);
								$query->set ('orderby', $v ["orderby"]);
							}
						}
					}
				}
			}
			return $query;
		}



	public function Taxonomy ($name, $options = array ()) {
		$this->taxonomies [$name] = $options;
	}

		public function RegisterTaxonomies () {
			if (count ($this->taxonomies) > 0) {
				foreach ($this->taxonomies as $k => $v) {
					register_taxonomy ($k, $this->slug, $v);
				}
			}
		}



}
