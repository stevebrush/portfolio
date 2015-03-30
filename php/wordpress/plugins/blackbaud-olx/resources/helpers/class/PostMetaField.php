<?php
class PostMetaField extends WP_BlackbaudPlugin {

	public $name;

	protected $post;
	private $html = array ();

	public $attr = array ();
	private $defaultAttr = array ("class" => "form-control");

	public $parentAttr = array ();
	private $defaultParentAttr = array ("class" => "form-group");

	public function __construct (WP_Post $post, $options = array ()) {

		$this-> post = $post;
		$this-> settings = $options;
		$this-> name = $this-> post-> post_type . "_" . $this-> settings ["slug"] . "_field";

		# Get the value already stored in the field.
		if (function_exists ("get_post_meta")) {
			$this-> settings ["value"] = get_post_meta ($this-> post-> ID, $this-> name, true);
		}

		$this-> CheckAdmin ();
		$this-> SetProperties ();
		$this-> Build ();
	}

	private function Build () {

		$this-> attributeHtml = $this-> BuildAttributes ();

		$this-> html ["containerStart"] = '<div' . $this-> BuildParentAttributes () . '>';
		$this-> html ["containerStop"] = '</div>';

		switch ($this-> type) {
			case "text":
				$this-> html ["label"] = '<label for="' . $this-> name . '" class="control-label">' . $this-> label . '</label>';
				$this-> html ["input"] = '<input type="text" id="' . $this-> name . '" name="' . $this-> name . '" value="' . $this-> value . '"' . $this-> attributeHtml . '>';
				break;
			case "textarea":
				$this-> html ["label"] = '<label for="' . $this-> name . '" class="control-label">' . $this-> label . '</label>';
				$this-> html ["input"] = '<textarea id="' . $this-> name . '" name="' . $this-> name . '"' . $this-> attributeHtml . '>' . $this-> value . '</textarea>';
				break;
			case "checkbox":
				$checked = ($this-> value == "true") ? ' checked' : '';
				$this-> html ["label"] = '';
				$this-> html ["input"] = '<label><input type="checkbox" id="' . $this-> name . '" name="' . $this-> name . '"' . $this-> attributeHtml . $checked . '>' . $this-> label . '</label>';
				break;
			case "media-gallery-picker":
				$this-> html ["label"] = '<label for="' . $this-> name . '" class="control-label">' . $this-> label . '</label>';
				$this-> html ["input"] = '<div class="blackbaud-metabox-gallery-picker"><input type="text" id="' . $this-> name . '" name="' . $this-> name . '" value="' . $this-> value . '"' . $this-> attributeHtml . '><a href="#" class="button blackbaud-metabox-gallery-picker-button">Select Image</a></div>';
				break;
		}

	}

	private function BuildAttributes () {

		$html = "";
		$this-> attr = array_merge ($this-> defaultAttr, $this-> attr);

		if (count ($this-> attr)) {
			foreach ($this-> attr as $k => $v) {
				$html .= ' ' . $k . '="'. $v . '"';
			}
		}

		return $html;

	}

	private function BuildParentAttributes () {

		$html = "";
		$this-> parentAttr = array_merge ($this-> defaultParentAttr, $this-> parentAttr);

		if (count ($this-> parentAttr)) {
			foreach ($this-> parentAttr as $k => $v) {
				$html .= ' ' . $k . '="'. $v . '"';
			}
		}

		return $html;

	}

	public function Render ($what = "all", $echo = true) {

		$html = "";

		switch ($what) {
			case "label":
				$html .= $this-> html ["label"];
				break;
			case "input":
				if (isset ($this-> html ["input"])) {
					$html .= $this-> html ["input"];
				}
				break;
			default:
				$html .= $this-> html ["containerStart"];
				$html .= $this-> html ["label"];
				$html .= $this-> html ["input"];
				$html .= $this-> html ["containerStop"];
				break;
		}

		if ($echo) {
			echo $html;
		} else {
			return $html;
		}

	}

}
