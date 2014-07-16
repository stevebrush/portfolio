<?php
class Rule {

	protected $defaults = array(
		"spacesAllowed" => "true",
		"default" => null,
		"filters" => null // 'toLowerCase'
	),
		$options,
		$stringLength,
		$integerRange,
		$spacesAllowed,
		$default,
		$filters;
		
	public function __construct($opts=array()) {
		$this->options = array_merge($this->defaults, $opts);
		$this->setProperties();
	}
	
	public function stringLength() {
		return $this->stringLength;
	}
	
	public function integerRange() {
		return $this->integerRange;
	}
	
	public function spacesAllowed() {
		return filter_var($this->spacesAllowed, FILTER_VALIDATE_BOOLEAN);
	}
		
	public function getDefaultValue() {
		return (!is_null($this->default)) ? $this->default : "";
	}
	
	public function filters() {
		return $this->filters;
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