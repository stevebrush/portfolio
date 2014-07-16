<?php 
class FormField {

	private $defaults = array(
		"maxLength" => "90",
		"autoCapitalize" => "true",
		"autoComplete" => "true",
		"autoCorrect" => "false",
		"submitOnReturn" => "false",
		"disabled" => "false",
		"required" => "false",
		"dataLoadingText" => "Processing...",
		"attr" => array()
	);

	// HTML ATTRIBUTES
	private $id, 
		$type,
		$name,
		$value,
		$checked,
		$maxLength,
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
		
	private $form,
		$labelHtml,
		$labelClass,
		$fieldHtml,
		$fieldClass,
		$decoration;
	
	public function __construct(Form $frm, $opts = array()) {
		$this->options = array_merge($this->defaults, $opts);
		$this->setProperties();
		$this->id = $this->type."_".uniqid()."_".$this->name;
		$this->form = $frm;
	}
	
	public function render( $what = "all" ) {
	
		if (!isset($this->fieldHtml)) $this->build();
		
		if ($what == "label") {
			echo $this->labelHtml;
			
		} else if ($what == "field") {
			echo $this->fieldHtml;
			
		} else {
		
			echo "<div class=\"form-group\">";
			echo $this->labelHtml;
			$this->decorationStart();
			echo $this->fieldHtml;
			echo $this->helplet();
			$this->decorationStop();
			echo "</div>";
			
		}
		return $this;
	}
	
	public function decorationStart() {
		echo (!empty($this->decoration)) ? $this->decoration : "";
	}
	
	public function decorationStop() {
		echo (!empty($this->decoration)) ? "</div>" : "";
	}
	
	public function get( $property = "" ) {
		if (!isset($this->$property)) return false;
		return $this->$property;
	}
	
	public function set( $property, $value = "" ) {
		if (gettype($property) === "array") {
			foreach ($property as $k => $v) {
				$this->$k = $v;
			}
			return $this;
		}
		$this->$property = $value;
		return $this;
	}
	
	public function setLabel( $value = "" ) {
		$this->label = $value;
	}
	
	public function setName( $value = "" ) {
		$this->name = $value;
	}
	
	public function setValue( $value = "" ) {
		$this->value = $value;
	}
	
	public function setHelplet($value = "") {
		$this->helplet = $value;
	}

	public function helplet() {
		if (isset($this->helplet) && !isEmpty($this->helplet)) {
			return "<div class=\"help-block\">".$this->helplet."</div>";
		}
	}
	
	private function setProperties() {
		foreach ($this->options as $key => $val) {
			if (!isset($val)) continue;
			if ($val === "true" || $val === "yes") $val = true;
			if ($val === "false" || $val === "no") $val = false;
			$this->$key = $val;
		}
	}
	
	private function build() {
	
		$isHorizontal = ($this->form->getOrientation() == "horizontal");
	
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
				$placeholder = (!empty($this->placeholder)) ? " placeholder=\"{$this->placeholder}\"" : "";
				$this->fieldClass .= ($this->submitOnReturn) ? " submit-on-return" : "";
				$this->labelHtml .= (!empty($this->label) || $isHorizontal) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}:</label>" : "";
				$this->fieldHtml .= "<input id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\" type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\"{$disabled}{$placeholder}{$this->buildAttributes()}>";
			break;
			
			case "textarea":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$disabled = ($this->disabled) ? " disabled" : "";
				$placeholder = (!empty($this->placeholder)) ? " placeholder=\"{$this->placeholder}\"" : "";
				$this->fieldClass .= ($this->submitOnReturn) ? " submit-on-return" : "";
				$this->labelHtml .= (!empty($this->label) || $isHorizontal) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}:</label>" : "";
				$this->fieldHtml .= "<textarea id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\" name=\"{$this->name}\"{$disabled}{$placeholder}{$this->buildAttributes()}>{$this->value}</textarea>";
			break;
			
			case "checkbox":
				if ($isHorizontal) $this->decoration .= "<div class=\"col-sm-offset-3 col-sm-9\">";
				$checked = ($this->checked) ? " checked" : "";
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldHtml .= "<div class=\"checkbox\"><label for=\"{$this->id}\" class=\"control-label\"><input id=\"{$this->id}\" class=\"{$this->fieldClass}\" type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\"{$checked}{$disabled}{$this->buildAttributes()}>{$this->label}</label></div>";
			break;
			
			case "static":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= (isset($this->label) && $this->label !== "") ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}:</label>" : "<label class=\"control-label {$this->labelClass}\"></label>";
				$this->fieldHtml .= "<div id=\"{$this->id}\" class=\"form-control-static {$this->fieldClass}\">{$this->value}</div>";
			break;
			
			case "radioGroup":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$this->label}:</label>";
				$disabled = ($this->disabled) ? " disabled" : "";
				if (!$this->choices) break;
				$counter = 0;
				foreach ($this->choices as $choice) {
					$counter++;
					$checked = (isset($choice["selected"]) && $choice["selected"] == "true") ? " checked" : "";
					$this->fieldHtml .= "<div class=\"radio\"><label><input type=\"radio\" id=\"{$this->id}_{$counter}\" value=\"{$choice['value']}\" name=\"{$this->name}\"{$disabled}{$checked}>{$choice['label']}</label></div>";
				}
			break;
			
			case "checkboxGroup":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$this->label}:</label>";
				$disabled = ($this->disabled) ? " disabled" : "";
				if (!$this->choices) break;
				$counter = 0;
				foreach ($this->choices as $choice) {
					$counter++;
					$checked = (isset($choice["selected"]) && $choice["selected"] == "true") ? " checked" : "";
					$this->fieldHtml .= "<div class=\"checkbox\"><label><input type=\"checkbox\" id=\"{$this->id}_{$counter}\" value=\"{$choice['value']}\" name=\"{$this->name}[]\"{$disabled}{$checked}>{$choice['label']}</label></div>";
				}
			break;
			
			case "select":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= (!empty($this->label) || $isHorizontal) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$this->label}:</label>" : "";
				$disabled = ($this->disabled) ? " disabled" : "";
				if (!$this->choices) {
					break;
				}
				$counter = 0;
				$this->fieldHtml .= "<select id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\" name=\"{$this->name}\"{$disabled}>";
				foreach ($this->choices as $choice) {
					$selected = (isset($choice["selected"]) && $choice["selected"] == "true") ? " selected" : "";
					$this->fieldHtml .= "<option value=\"{$choice['value']}\"{$selected}>{$choice['label']}</option>";
				}
				$this->fieldHtml .= "</select>";
			break;
			
			case "submit":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-offset-3 col-sm-9\">";
				}
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldHtml .= "<button type=\"submit\" data-loading-text=\"{$this->dataLoadingText}\" class=\"btn btn-submit {$this->fieldClass}{$disabled}\"{$disabled}>{$this->label}</button>";
			break;
		}
		
		return $this;
		
	}
	
	private function buildAttributes() {
		$html = array();
		$html[] = (isset($this->maxLength)) ? "maxlength=\"{$this->maxLength}\"" : "";
		$html[] = ($this->autoCapitalize) ? "autocapitalize=\"on\"" : "autocapitalize=\"off\"";
		$html[] = ($this->autoComplete) ? "autocomplete=\"on\"" : "autocomplete=\"off\"";
		$html[] = ($this->autoCorrect) ? "autocorrect=\"on\"" : "autocorrect=\"off\"";
		if (count($this->attr) > 0) {
			foreach ($this->attr as $k => $v) {
				$html[] = "{$k}=\"{$v}\"";
			}
		}
		return " ".join($html, " ");
	}
	
}