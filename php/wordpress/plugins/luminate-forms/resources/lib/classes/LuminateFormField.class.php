<?php 
/*
 *	This class helps to create consistent form fields and interface.
 */
class LuminateFormField {

	private $defaults = array(
		"maxLength" => "90",
		"autoCapitalize" => "true",
		"autoComplete" => "true",
		"autoCorrect" => "false",
		"submitOnReturn" => "false",
		"disabled" => "false",
		"required" => "false",
		"dataLoadingText" => "Processing...",
		"fieldClass" => ""
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
		$dataLoadingText;	
		
	private $form,
		$labelHtml,
		$labelClass,
		$fieldHtml,
		$fieldClass,
		$decoration;
	
	public function __construct(LuminateForm $frm, $opts = array()) {
		$this->options = array_merge($this->defaults, $opts);
		$this->setProperties();
		$this->id = $this->type."_".uniqid()."_".$this->name;
		$this->form = $frm;
	}
	
	public function render( $what = "all" ) {
		echo $this->getRender($what);
		return $this;
	}
	
	public function getRender( $what = "all" ) {
		$html = "";
		if (!isset($this->fieldHtml)) $this->build();
		
		if ($what == "label") {
			$html .= $this->labelHtml;
			
		} else if ($what == "field") {
			$html .= $this->fieldHtml;
			
		} else {
		
			$html .= "<div class=\"form-group\">";
			$html .= $this->labelHtml;
			$html .= $this->getDecorationStart();
			$html .= $this->fieldHtml;
			$html .= $this->getDecorationStop();
			$html .= "</div>";
			
		}
		return $html;
	}
	
	public function decorationStart() {
		echo (!empty($this->decoration)) ? $this->decoration : "";
	}
	
	public function decorationStop() {
		echo (!empty($this->decoration)) ? "</div>" : "";
	}
	
	public function getDecorationStart() {
		return (!empty($this->decoration)) ? $this->decoration : "";
	}
	
	public function getDecorationStop() {
		return (!empty($this->decoration)) ? "</div>" : "";
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
		$required_marker = ($this->required == "true") ? "<span class=\"lo-required-field-marker\">*</span>" : "";
		$this->fieldClass .= ($this->required == "true") ? "lo-field-required" : "";
	
		switch ($this->type) {
		
			case "hidden":
				$this->fieldHtml = "<input type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\">";
			break;
		
			case "text":
			case "email":
			case "password":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$disabled = ($this->disabled) ? " disabled" : "";
				$placeholder = (!empty($this->placeholder)) ? " placeholder=\"{$this->placeholder}\"" : "";
				$this->fieldClass .= ($this->submitOnReturn) ? " submit-on-return" : "";
				$this->labelHtml .= (!empty($this->label)) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>" : "";
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
				$this->fieldClass .= ($this->required == "true") ? " lo-field-required" : "";
				$this->labelHtml .= (!empty($this->label)) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>" : "";
				$this->fieldHtml .= "<textarea id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\" name=\"{$this->name}\"{$disabled}{$placeholder}{$this->buildAttributes()}>{$this->value}</textarea>";
			break;
			
			case "checkbox":
				if ($isHorizontal) $this->decoration .= "<div class=\"col-sm-offset-3 col-sm-9\">";
				$checked = ($this->checked) ? " checked" : "";
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldClass .= ($this->required == "true") ? " lo-field-required" : "";
				$this->fieldHtml .= "<div class=\"checkbox\"><label for=\"{$this->id}\" class=\"control-label{$this->fieldClass}\"><input id=\"{$this->id}\" type=\"{$this->type}\" name=\"{$this->name}\" value=\"{$this->value}\"{$checked}{$disabled}>{$required_marker}{$this->label}</label></div>";
			break;
			
			case "radioGroup":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>";
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldClass .= ($this->required == "true") ? " lo-field-required" : "";
				if (!$this->choices) break;
				$counter = 0;
				foreach ($this->choices as $choice) {
					$counter++;
					$checked = (isset($choice["selected"]) && $choice["selected"] == "true") ? " checked" : "";
					$this->fieldHtml .= "<div class=\"radio\"><label><input type=\"radio\" id=\"{$this->id}_{$counter}\" class=\"{$this->fieldClass}\" value=\"{$choice['value']}\" name=\"{$this->name}\"{$disabled}{$checked}>{$choice['label']}</label></div>";
				}
			break;
			
			case "checkboxGroup":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>";
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldClass .= ($this->required == "true") ? " lo-field-required" : "";
				if (!$this->choices) break;
				$counter = 0;
				foreach ($this->choices as $choice) {
					$counter++;
					$checked = (isset($choice["selected"]) && $choice["selected"] == "true") ? " checked" : "";
					$this->fieldHtml .= "<div class=\"checkbox\"><label><input type=\"checkbox\" id=\"{$this->id}_{$counter}\" class=\"{$this->fieldClass}\" name=\"{$this->name}[]\"{$disabled}{$checked}>{$choice['label']}</label></div>";
				}
			break;
			
			case "interests":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>";
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldClass .= ($this->required == "true") ? " lo-field-required" : "";
				if (!$this->choices) break;
				$counter = 0;
				foreach ($this->choices as $choice) {
					$counter++;
					$checked = (isset($choice["selected"]) && $choice["selected"] == "true") ? " checked" : "";
					$this->fieldHtml .= "<div class=\"checkbox\"><label><input type=\"checkbox\" id=\"{$this->id}_{$counter}\" class=\"{$this->fieldClass}\" name=\"question_{$choice['value']}\"{$disabled}{$checked}>{$choice['label']}</label></div>";
				}
			break;
			
			case "select":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= (!empty($this->label)) ? "<label for=\"{$this->id}\" class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>" : "";
				$disabled = ($this->disabled) ? " disabled" : "";
				$this->fieldClass .= ($this->required == "true") ? " lo-field-required" : "";
				if (!$this->choices) break;
				$counter = 0;
				if ($this->choices) {
					$this->fieldHtml .= "<select id=\"{$this->id}\" class=\"form-control {$this->fieldClass}{$disabled}\" name=\"{$this->name}\"{$disabled}>";
					foreach ($this->choices as $choice) {
						$selected = (isset($choice["selected"]) && $choice["selected"] == "true") ? " selected" : "";
						$this->fieldHtml .= "<option value=\"{$choice['value']}\"{$selected}>{$choice['label']}</option>";
					}
					$this->fieldHtml .= "</select>";
				}
			break;
			
			case "dateSelect":
				if ($isHorizontal) {
					$this->decoration .= "<div class=\"col-sm-9\">";
					$this->labelClass .= " col-sm-3";
				}
				$this->labelHtml .= "<label class=\"control-label {$this->labelClass}\">{$required_marker}{$this->label}</label>";
				$monthArr = array(array("value"=>"","label"=>"Month","selected" => "true"));
				$dayArr = array(array("value"=>"","label"=>"Day","selected" => "true"));
				$yearArr = array(array("value"=>"","label"=>"Year","selected" => "true"));
				$currentYear = date('Y');
				for ($i=1; $i<=12; $i++) {
					$monthArr[] = array("value"=>$i,"label"=>$i,"selected" => "false");
				}
				for ($i=1; $i<=31; $i++) {
					$dayArr[] = array("value"=>$i,"label"=>$i,"selected" => "false");
				}
				for ($i=$currentYear+20; $i>=$currentYear-115; $i--) {
					$yearArr[] = array("value"=>$i,"label"=>$i,"selected" => "false");
				}
				$month = new $this($this->form, array(
					"type"=>"select",
					"name"=>$this->name."_month",
					"label"=>"Month",
					"required"=>$this->required,
					"choices"=>$monthArr
				));
				$day = new $this($this->form, array(
					"type"=>"select",
					"name"=>$this->name."_day",
					"label"=>"Day",
					"required"=>$this->required,
					"choices"=>$dayArr
				));
				$year = new $this($this->form, array(
					"type"=>"select",
					"name"=>$this->name."_year",
					"label"=>"Year",
					"required"=>$this->required,
					"choices"=>$yearArr
				));
				$this->fieldHtml .= "<div class=\"form-date-select-container\">";
				$this->fieldHtml .= "<input type=\"hidden\" name=\"".$this->name."\" value=\"\">";
					$this->fieldHtml .= $month->getRender("field");
					$this->fieldHtml .= $day->getRender("field");
					$this->fieldHtml .= $year->getRender("field");
				$this->fieldHtml .= "</div>";
			break;
			
			case "submit":
				if ($isHorizontal) $this->decoration .= "<div class=\"col-sm-offset-3 col-sm-9\">";
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
		return " ".join($html, " ");
	}
}
