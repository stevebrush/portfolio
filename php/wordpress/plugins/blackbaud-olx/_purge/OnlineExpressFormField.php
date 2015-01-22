<?php 
class OnlineExpressFormField {

	private $defaults = array (
		"maxlength" => "90",
		"autoCapitalize" => "true",
		"autoComplete" => "true",
		"autoCorrect" => "false",
		"decorationClass" => "",
		"submitOnReturn" => "false",
		"disabled" => "false",
		"required" => "false",
		"dataLoadingText" => "Processing...",
		"attr" => array ()
	);

	# HTML ATTRIBUTES
	private $id, 
		$type,
		$name,
		$value,
		$checked,
		$maxlength,
		$autoCapitalize,
		$autoComplete,
		$autoCorrect,
		$disabled,
		$placeholder;
		
	private $options,
		$label,
		$required,
		$submitOnReturn,
		$dataLoadingText,
		$helplet;	
		
	private $decorationClass,
		$labelHtml,
		$labelClass,
		$fieldHtml,
		$fieldClass,
		$decoration;
	
	public function __construct ($opts = array ()) {
		$this->options = array_merge ($this->defaults, $opts);
		$this->setProperties ();
		$this->id = $this->type . "_" . uniqid () . "_" . $this->name;
	}
	
	public function Render ($what = "all", $echo = true) {
	
		$html = "";
	
		if (! isset ($this->fieldHtml)) {
			$this->Build ();
		}
		
		if ($what == "label") {
			$html .= $this->labelHtml;
			
		} else if ($what == "field") {
			$html .= $this->fieldHtml;
			
		} else {
			
			if (! is_empty ($this->decorationClass)) {
				$html .= "<div class=\"form-group {$this->decorationClass}\">";
			} else {
				$html .= "<div class=\"form-group\">";
			}
			$html .= $this->labelHtml;
			$html .= $this->DecorationStart ();
			$html .= $this->fieldHtml;
			$html .= $this->Helplet ();
			$html .= $this->DecorationStop ();
			$html .= "</div>";
		}
		
		if ($echo) {
			echo $html;
		} else {
			return $html;
		}
	}
	
	public function DecorationStart () {
		echo (! empty ($this->decoration)) ? $this->decoration : "";
	}
	
	public function DecorationStop () {
		echo (! empty ($this->decoration)) ? "</div>" : "";
	}
	
	public function Get ($property = "") {
		if (! isset ($this->$property)) {
			return false;
		}
		return $this->$property;
	}
	
	public function Set ($property, $value = "") {
		if (gettype ($property) === "array") {
			foreach ($property as $k => $v) {
				$this->$k = $v;
			}
			return $this;
		}
		$this->$property = $value;
		return $this;
	}
	
	public function SetLabel ($value = "") {
		$this->label = $value;
	}
	
	public function SetName ($value = "") {
		$this->name = $value;
	}
	
	public function SetValue ($value = "") {
		$this->value = $value;
	}
	
	public function SetHelplet ($value = "") {
		$this->helplet = $value;
	}

	public function Helplet () {
		if (isset ($this->helplet) && ! is_empty ($this->helplet)) {
			return "<div class=\"help-block\">" . $this->helplet . "</div>";
		}
	}
	
	private function SetProperties () {
		foreach ($this->options as $key => $val) {
			if (! isset ($val)) {
				continue;
			}
			if ($val === "true" || $val === "yes") {
				$val = true;
			}
			if ($val === "false" || $val === "no") {
				$val = false;
			}
			$this->$key = $val;
		}
	}
	
	public function Build () {
	
		$isHorizontal = true;
	
		switch ($this->type) {
		
		case "hidden":
			$this->fieldHtml = "<input type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\">";
			break;
	
		case "url":
		case "text":
		case "email":
		case "number":
		case "password":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-9\">";
				$this->labelClass .= " col-sm-3";
			}
			$disabled = ($this->disabled) ? " disabled" : "";
			$placeholder = (! empty ($this->placeholder)) ? " placeholder=\"{$this->placeholder}\"" : "";
			$submitOnReturn = ($this->submitOnReturn) ? " data-gd-submit-on-return" : "";
			$this->labelHtml .= (! empty ($this->label) || $isHorizontal) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}</label>" : "";
			$this->fieldHtml .= "<input id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\"{$submitOnReturn} type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\"{$disabled}{$placeholder}{$this->buildAttributes()}>";
			break;
		
		case "textarea":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-9\">";
				$this->labelClass .= " col-sm-3";
			}
			$disabled = ($this->disabled) ? " disabled" : "";
			$placeholder = (!empty($this->placeholder)) ? " placeholder=\"{$this->placeholder}\"" : "";
			$submitOnReturn = ($this->submitOnReturn) ? " data-gd-submit-on-return" : "";
			$this->labelHtml .= (!empty($this->label) || $isHorizontal) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}</label>" : "";
			$this->fieldHtml .= "<textarea id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\"{$submitOnReturn} name=\"{$this->name}\"{$disabled}{$placeholder}{$this->buildAttributes()}>{$this->value}</textarea>";
			break;
		
		case "checkbox":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-offset-3 col-sm-9\">";
			}
			$checked = ($this->checked) ? " checked" : "";
			$disabled = ($this->disabled) ? " disabled" : "";
			$this->fieldHtml .= "<div class=\"checkbox\"><label for=\"{$this->id}\" class=\"control-label\"><input id=\"{$this->id}\" class=\"{$this->fieldClass}\" type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\"{$checked}{$disabled}{$this->buildAttributes()}>{$this->label}</label></div>";
			break;
			
		case "static":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-9\">";
				$this->labelClass .= " col-sm-3";
			}
			if (isset ($this->label) && $this->label !== "") {
				$this->labelHtml .= "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}</label>";
			} else {
				$this->labelHtml .= "";
			}
			$this->fieldHtml .= "<div id=\"{$this->id}\" class=\"form-control-static {$this->fieldClass}\">{$this->value}</div>";
			break;
			
		case "radioGroup":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-9\">";
				$this->labelClass .= " col-sm-3";
			}
			$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$this->label}</label>";
			$disabled = ($this->disabled) ? " disabled" : "";
			if (! $this->choices) {
				break;
			}
			$counter = 0;
			foreach ($this->choices as $choice) {
				$counter++;
				$checked = (isset($choice["selected"]) && $choice ["selected"] == "true") ? " checked" : "";
				$this->fieldHtml .= "<div class=\"radio\"><label><input type=\"radio\" id=\"{$this->id}_{$counter}\" value=\"{$choice['value']}\" name=\"{$this->name}\"{$disabled}{$checked}>{$choice['label']}</label></div>";
			}
			break;
			
		case "checkboxGroup":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-9\">";
				$this->labelClass .= " col-sm-3";
			}
			$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$this->label}</label>";
			$disabled = ($this->disabled) ? " disabled" : "";
			if (!$this->choices) {
				break;
			}
			$counter = 0;
			foreach ($this->choices as $choice) {
				$counter++;
				$checked = (isset ($choice["selected"]) && $choice ["selected"] == "true") ? " checked" : "";
				$this->fieldHtml .= "<div class=\"checkbox\"><label><input type=\"checkbox\" id=\"{$this->id}_{$counter}\" value=\"{$choice['value']}\" name=\"{$this->name}[]\"{$disabled}{$checked}>{$choice['label']}</label></div>";
			}
			break;
			
		case "select":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-9\">";
				$this->labelClass .= " col-sm-3";
			}
			$this->labelHtml .= (!empty($this->label) || $isHorizontal) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}</label>" : "";
			$disabled = ($this->disabled) ? " disabled" : "";
			if (!$this->choices) {
				break;
			}
			$counter = 0;
			$this->fieldHtml .= "<select id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\" name=\"{$this->name}\"{$disabled}>";
			foreach ($this->choices as $choice) {
				$selected = (isset ($choice["selected"]) && $choice ["selected"] == "true") ? " selected" : "";
				$this->fieldHtml .= "<option value=\"{$choice['value']}\"{$selected}>{$choice['label']}</option>";
			}
			$this->fieldHtml .= "</select>";
			break;
			
		case "submit":
			if ($isHorizontal) {
				$this->decoration .= "<div class=\"col-sm-offset-3 col-sm-9\">";
			}
			$disabled = ($this->disabled) ? " disabled" : "";
			$this->fieldHtml .= "<button type=\"button\" data-loading-text=\"{$this->dataLoadingText}\" class=\"btn btn-submit {$this->fieldClass}{$disabled}\"{$disabled}>{$this->label}</button>";
			break;
		}
		
		return $this;
		
	}
	
	private function BuildAttributes() {
		$html = array ();
		$html [] = (isset ($this->maxlength)) ? "maxlength=\"{$this->maxlength}\"" : "";
		$html [] = ($this->autoCapitalize === true) ? "autocapitalize=\"on\"" : "autocapitalize=\"off\"";
		$html [] = ($this->autoComplete === true) ? "autocomplete=\"on\"" : "autocomplete=\"off\"";
		$html [] = ($this->autoCorrect === true) ? "autocorrect=\"on\"" : "autocorrect=\"off\"";
		if (count ($this->attr) > 0) {
			foreach ($this->attr as $k => $v) {
				$html [] = "{$k}=\"{$v}\"";
			}
		}
		return " " . join ($html, " ");
	}
	
}