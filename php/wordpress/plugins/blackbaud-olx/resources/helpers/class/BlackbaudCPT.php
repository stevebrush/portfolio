<?php
if (class_exists ("BlackbaudCPT")) exit ();
class BlackbaudCPT {

	private $config;

	public function __construct (Array $options = array ()) {
		$this->config = $options;
	}

	public function Config ($key, $value) {
		return $this->config [$key] [$value];
	}

	public function Create ($className, $options) {
		return new $className ($options, $this);
	}

}
