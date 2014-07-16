<?php
class Form {
	
	private $defaults = array(
		"method" => "post",
		"allowUpload" => "false",
		"orientation" => "vertical",
		"heading" => null,
		"cssClass" => null,
		"apiCallback" => null
	);
	
	private $id,
		$action,
		$method,
		$allowUpload,
		$orientation,
		$cssClass,
		$apiCallback;
	
	public function __construct($opts=array()) {
		$this->options = array_merge($this->defaults, $opts);
		$this->setProperties();
		$this->id = "gdForm_".uniqid();
	}
	
	public function getFormId() {
		return $this->id;
	}
	
	public function start() {
		$enctype = ($this->allowUpload) ? "enctype=\"multipart/form-data\" " : "";
		$orientation = ($this->orientation == "horizontal") ? " form-horizontal" : "";
		$cssClass = ($this->cssClass) ? " {$this->cssClass}" : "";
		$apiCallback = (isset($this->apiCallback)) ? " data-gd-callback=\"{$this->apiCallback}\"" : "";
		$html = "<form id=\"{$this->id}\" class=\"gd-form{$orientation}{$cssClass}\" {$enctype}action=\"{$this->action}\" method=\"{$this->method}\"{$apiCallback}>";
		print($html);
	}
	
	public function stop() {
		echo "</form>";
	}
	
	public function heading() {
		if ($this->heading) print("<div class=\"form-heading\"><h1 class=\"form-title\">{$this->heading}</h1></div>");
	}
	
	public function getHeading() {
		return $this->heading;
	}
	
	public function alert($message = "", $type = "default") {
		$class = ($message !== "") ? " in gd-session-alert" : "";
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
		print("<div id=\"form_message_{$this->id}\" class=\"alert{$class} gd-form-alert clearfix\">{$message}</div>");
	}
	
	public function getOrientation() {
		return $this->orientation;
	}
	
	private function setProperties() {
		foreach ($this->options as $key => $val) {
			if (!isset($val)) continue;
			if ($val === "true" || $val === "yes") $val = true;
			if ($val === "false" || $val === "no") $val = false;
			$this->$key = $val;
		}
	}
	
}