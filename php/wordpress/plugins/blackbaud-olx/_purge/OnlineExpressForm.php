<?php
class OnlineExpressForm {
	
	private $defaults = array (
		"method" => "post",
		"allowUpload" => "false",
		"orientation" => "vertical", # vertical, horizontal, inline
		"heading" => null,
		"cssClass" => null,
		"apiCallback" => null,
		"headingTag" => "h1"
	);
	
	private $id,
		$action,
		$method,
		$allowUpload,
		$orientation,
		$cssClass,
		$apiCallback,
		$headingTag,
		$headingLink;
		
	protected $fields = array ();
	
	public function __construct ($opts = array ()) {
		$this->options = array_merge ($this->defaults, $opts);
		$this->setProperties ();
		$this->id = "olx_form_" . uniqid ();
	}
	
	public function GetFormId () {
		return $this->id;
	}
	
	public function Start () {
		$enctype = ($this->allowUpload) ? "enctype=\"multipart/form-data\" " : "";
		$orientation = " form-" . $this->orientation;
		$cssClass = ($this->cssClass) ? " {$this->cssClass}" : "";
		$apiCallback = (isset ($this->apiCallback)) ? " data-olx-callback=\"{$this->apiCallback}\"" : "";
		$html = "<div class=\"form-container\"><form id=\"{$this->id}\" class=\"olx-form{$orientation}{$cssClass}\" {$enctype}action=\"{$this->action}\" method=\"{$this->method}\"{$apiCallback}>";
		print ($html);
	}
	
	public function Stop () {
		echo "</form></div>";
	}
	
	public function Heading () {
		$html = "";
		if (isset ($this->heading) && $this->heading !== "") {
			$html = "<div class=\"form-header\">";
			$html .= "<" . $this->headingTag . " class=\"form-heading\">";
				$html .= $this->heading;
			$html .= "</" . $this->headingTag . ">";	
			if (isset ($this->headingLink)) {
				$html .= '<a href="' . $this->headingLink ["href"] . '">' . $this->headingLink ["label"] . '</a>';
			}
			$html .= "</div>";
		}
		print ($html);
	}
	
	public function GetHeading () {
		return $this->heading;
	}
	
	public function Alert ($message = "", $type = "default") {
		$class = ($message !== "") ? " in olx-session-alert" : "";
		switch ($type) {
		case "default":
		default:
			$class .= " alert-info";
			break;
		case "success":
			$class .= " alert-success";
			break;
		case "error":
			$class .= " alert-danger";
			break;
		}
		print ("<div class=\"form-alert\" id=\"form_message_{$this->id}\"><div class=\"alert{$class}\">{$message}</div></div>");
	}
	
	public function GetOrientation () {
		return $this->orientation;
	}
	
	private function SetProperties () {
		foreach ($this->options as $key => $val) {
			if (! isset ($val)) continue;
			if ($val === "true" || $val === "yes") $val = true;
			if ($val === "false" || $val === "no") $val = false;
			$this->$key = $val;
		}
	}
	
	public function Field ($slug, $options = array ()) {
		$field = new OnlineExpressFormField ($this, $options);
		$this->fields [$slug] = $field;
	}
	
	public function GetField ($slug) {
		if (isset ($this->fields [$slug])) {
			return $this->fields [$slug];
		} else {
			return new OnlineExpressFormField ($this, array());
		}
	}
	
}