<?php
class Cookie {

	private $defaults = array(
		"name" 		=> null,
		"value"		=> null,
		"duration"	=> null
	);
	
	private $options,
		$name,
		$value,
		$duration;
	
	public function __construct($opts = array()) {
		$this->defaults["duration"] = time()+60*60*24*30;
		$this->options = array_merge($this->defaults, $opts);
		$this->setProperties();
	}
	
	public function setName($val) {
		$this->name = $val;
	}
	
	public function getValue() {
		if (is_null($this->value)) {
			$this->value = isset($_COOKIE[$this->name]) ? $_COOKIE[$this->name] : null;
		}
		return (is_null($this->value)) ? false : $this->value;
	}
	
	public function create() {
		setcookie($this->name, $this->value, $this->duration, "/");
	}
	
	public function destroy() {
		setcookie($this->name, "", time() - 3600, "/");
	}
	
	private function setProperties() {
		foreach ($this->options as $key => $val) {
			if (isset($val)) {
				$this->$key = $val;
			}
		}
	}
}