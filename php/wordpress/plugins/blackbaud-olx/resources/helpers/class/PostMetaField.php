<?php
if (class_exists ("PostMetaField")) exit ();
class PostMetaField extends BB_PluginHelper {

	public $name;

	protected $post;
	private $html = array ();
	private $defaultAttr = array ("class" => "form-control");

	public function __construct (WP_Post $post, $options = array ()) {

		$this->post = $post;
		$this->settings = $options;
		$this->name = $this->post->post_type . "_" . $this->settings ["slug"] . "_field";

		# Get the value already stored in the field.
		if (function_exists ("get_post_meta")) {
			$this->settings ["value"] = get_post_meta ($this->post->ID, $this->name, true);
		}

		$this->CheckAdmin ();
		$this->SetProperties ();
		$this->Build ();
	}

	private function Build () {
		$this->attributeHtml = $this->BuildAttributes ();
		$this->html ["label"] = '<label for="' . $this->name . '" class="control-label">' . $this->label . '</label>';
		switch ($this->type) {
			case "text":
				$this->html ["input"] = '<input type="text" id="' . $this->name . '" name="' . $this->name . '" value="' . $this->value . '"' . $this->attributeHtml . ' />';
				break;
			case "textarea":
				$this->html ["input"] = '<textarea id="' . $this->name . '" name="' . $this->name . '"' . $this->attributeHtml . '>' . $this->value . '</textarea>';
				break;
		}
	}

	private function BuildAttributes () {

		$html = "";
		$this->attr = array_merge ($this->defaultAttr, $this->attr);

		if (count ($this->attr)) {
			foreach ($this->attr as $k => $v) {
				$html .= ' ' . $k . '="'. $v . '"';
			}
		}

		return $html;
	}

	public function Render ($what = "all", $echo = true) {

		$html = "";

		switch ($what) {
			case "label":
				$html .= $this->html ["label"];
				break;
			case "input":
				if (isset ($this->html ["input"])) {
					$html .= $this->html ["input"];
				}
				break;
			default:
				$html .= $this->html ["label"];
				$html .= $this->html ["input"];
				break;
		}

		if ($echo) {
			echo $html;
		} else {
			return $html;
		}
	}

}
