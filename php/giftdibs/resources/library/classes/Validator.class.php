<?php 
class Validator {

	protected $inputs = array();
	protected $errors = array();
	
	public function addInput(Array $input, $value) {
		$this->inputs[ $input["field"]["name"] ] = array( "params" => $input, "rule" => new Rule($input["rule"]), "value" => trim($value) );
	}
	
	public function run() {
	
		/*
			Check: 
			------
			1. Apply string filters
			2. String lengths
			3. Possible choices (for selects, radios, and checkboxes)
			4. Spaces allowed
			5. Special chars allowed
		*/
		foreach ($this->inputs as $input) {
			
			$value = $input["value"];
			$rule = $input["rule"];
			$label = (isset($input['params']['field']['label'])) ? $input['params']['field']['label'] : "Form field";
			
			// Apply Filters...
			if ($filters = $rule->filters()) {
				foreach ($filters as $f) {
					if ($f == "toLowerCase") {
						$value = strtolower($value);
						$this->inputs[ $input["params"]["field"]["name"] ]["value"] = $value;
					}
				}
			}
			
			// Required...
			if (isset($input["params"]["field"]["required"])) {
				$isRequired = filter_var($input["params"]["field"]["required"], FILTER_VALIDATE_BOOLEAN);
				if ($isRequired && ( isEmpty($value) || is_null($value) )) {
					$this->errors[] = "'{$label}' is required.";
					continue;
				}
			}
			
			// String length...
			if ($strLengthRange = $rule->stringLength()) {
				if (!$this->stringLengthBetween( $value, $strLengthRange )) {
					$this->errors[] = "The '{$input['params']['field']['label']}' you entered isn't the correct length. It must be between ({$strLengthRange[0]}) and ({$strLengthRange[1]}) characters.";
				}
			}
			
			// Spaces allowed...
			$spacesAllowed = $rule->spacesAllowed();
			if (!$spacesAllowed && $this->containsSpaces( $value )) {
				$this->errors[] = "The '{$input['params']['field']['label']}' field cannot contain spaces.";
			}
			
			// Integer range...
			$integerRange = $rule->integerRange();
			if ($integerRange && !$this->integerValueBetween( $value, $integerRange )) {
				$this->errors[] = "The '{$input['params']['field']['label']}' you entered isn't within the acceptable bounds. It must be between ({$integerRange[0]}) and ({$integerRange[1]}).";
			}
			
			// Choices...
			if (isset($input["params"]["field"]["choices"]) && isset($input["params"]["field"]["choices"]) && !isEmpty($value)) {
				$found = false;
				foreach ($input["params"]["field"]["choices"] as $choice) {
					if ($choice["value"] == $value) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$this->errors[] = "The item you selected for '{$input['params']['field']['label']}' is not a valid option. You supplied: \"{$value}\"";
				}
			}
			
			// Email...
			if (isset($input["params"]["field"]["type"]) && $input["params"]["field"]["type"] == "email") {
				// Formatted correctly?
				if (!$this->emailAddressValid($value)) {
					$this->errors[] = "The email address you entered was formatted incorrectly.";
				}
			}
			
		}
		
	}

	public function getErrors() {
		return (count($this->errors)) ? join($this->errors,"<br>") : false;
	}
	
	public function addError($message = "") {
		$this->errors[] = (string)$message;
	}
	
	public function getInputValue($name) {
		if (!isset($this->inputs[$name])) {
			$val = "";
		}
		else if (isset($this->inputs[$name]["value"]) && !isEmpty($this->inputs[$name]["value"])) {
			$val = $this->inputs[$name]["value"];
		} else {
			$val = $this->inputs[$name]["rule"]->getDefaultValue();
		}
		return $val;
	}
	
	public function emailAddressValid($email = "") {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	private function stringLengthBetween( $str = "", $range = array(0, 0) ) {
		$len = strlen($str);
		$min = $range[0];
		$max = $range[1];
		return ($len <= $max && $len >= $min);
	}
	
	private function integerValueBetween( $val = 0, $range = array(0, 0) ) {
		$val = (int)$val;
		$min = $range[0];
		$max = $range[1];
		return ($val <= $max && $val >= $min);
	}
	
	private function containsSpaces( $str = "" ) {
		return ( preg_match('/\s/', $str) );
	}
	
}