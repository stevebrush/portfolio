<?php
class PostMetaBox extends WP_BlackbaudPlugin {

	public $slug;
	public $postType;

	protected $context;
	protected $label;
	protected $priority;
	protected $templateFile;

	private $nonceLabel;
	private $nonceActionLabel;

	private $fields = array ();
	private $fieldArgs = array ();
	private $defaults = array (
		"context" => "advanced",
		"priority" => "default"
	);

	public function __construct (Array $options = array (), WP_BlackbaudFactory $factory) {

		$this-> settings = array_merge ($this-> defaults, $options);
		$this-> factory = $factory;

		$this-> CheckAdmin ();
		$this-> SetProperties ();

		$this-> slug .= "_metabox";
		$this-> nonceLabel = $this-> postType . "_" . $this-> slug . "_nonce";
		$this-> nonceActionLabel = $this-> postType . "_" . $this-> slug;

		if (! isset ($this-> templateFile)) {
			$this-> templateFile = $this-> factory-> Config ("dir", "view") . "meta-box.php";
		}
	}

	public function AddField (Array $args = array ()) {
		$this-> fieldArgs [] = $args;
		return $this;
	}

	public function Build () {
		if (function_exists ("add_action") && $this-> isAdmin) {
			add_action ("add_meta_boxes_" . $this-> postType, array ($this, "Create"));
			add_action ("save_post_" . $this-> postType, array ($this, "Save"));
		}
	}

	public function Create () {
		add_meta_box ($this-> postType . "_" . $this-> slug, __ ($this-> label, $this-> postType), array ($this, "Display"), $this-> postType, $this-> context, $this-> priority);
	}

	public function Display (WP_POST $post) {

		$data = array ();
		$fieldsHTML = array ();
		$nonceField = wp_nonce_field ($this-> nonceActionLabel, $this-> nonceLabel, true, false);

		# Make sure our form gets a security signature.
		if (function_exists ("wp_nonce_field")) {
			$fieldsHTML [] = array (
				"containerStart" => '',
				"label" => "",
				"input" => $nonceField,
				"containerStop" => '',
				"html" => $nonceField
			);
		}

		# Print the appropriate fields.
		if (count ($this-> fieldArgs)) {
			foreach ($this-> fieldArgs as $thisField) {
				$field = new PostMetaField ($post, $thisField);
				$fieldsHTML [] = array (
					"containerStart" => $field-> Render ("containerStart", false),
					"label" => $field-> Render ("label", false),
					"input" => $field-> Render ("input", false),
					"containerStop" => $field-> Render ("containerStop", false),
					"html" => $field-> Render ("all", false)
				);
				$this-> fields [] = $field;
			}
		}

		$data = array (
			"fields" => $fieldsHTML
		);

		echo $this-> View ($data);
	}

	public function Save ($postId) {

		$response = $_POST;
		$post = get_post ($postId);

		# Check if our nonce is set.
		if (! isset ($response [$this-> nonceLabel])) {
			return $postId;
		}

		$nonce = $response [$this-> nonceLabel];

		# Verify that the nonce action is valid.
		if (function_exists ("wp_verify_nonce") && ! wp_verify_nonce ($nonce, $this-> nonceActionLabel)) {
			return $postId;
		}

		# If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if (defined ("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
			return $postId;
		}

		# Check the user's permissions.
		# Editing a page.
		if ("page" === $response ["post_type"]) {
			if (! current_user_can ("edit_page", $postId)) {
				return $postId;
			}
		}

		# Editing a post.
		else {
			if (! current_user_can ("edit_post", $postId)) {
				return $postId;
			}
		}

		# We've made it this far, let's save the post meta!

		# Get the field objects.
		if (count ($this-> fieldArgs)) {
			foreach ($this-> fieldArgs as $thisField) {
				$this-> fields [] = new PostMetaField ($post, $thisField);
			}
		}

		# Sanitize the user input.
		if (count ($this-> fields)) {
			foreach ($this-> fields as $field) {
				if (isset ($response [$field-> name])) {
					if ($response [$field-> name] === "on") {
						update_post_meta ($postId, $field-> name, "true");
					} else {
						update_post_meta ($postId, $field-> name, $response [$field-> name]);
					}
				} else {
					update_post_meta ($postId, $field-> name, "false");
				}
			}
		}
	}

	private function View ($data = array ()) {

		$file = $this-> templateFile;

		if (! file_exists ($file)) {
			return "";
		}

		ob_start ();
		include $file;
		return ob_get_clean ();

	}

}
