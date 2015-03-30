<?php
abstract class WP_BlackbaudPlugin {

	protected $isAdmin;
	protected $settings = array ();
	protected $factory;

	public function CheckAdmin () {
		if (! isset ($this-> isAdmin)) {
			$this-> isAdmin = (function_exists ("is_admin") && is_admin ());
		}
		return $this-> isAdmin;
	}

	public function Get ($key) {
		if (isset ($this-> $key)) {
			return $this-> $key;
		}
		return false;
	}

	public function Set ($key, $val) {
		$this-> $key = $val;
	}

	protected function SetProperties () {
		foreach ($this->settings as $key => $val) {
			if (! isset ($val)) {
				continue;
			}
			$this-> $key = $val;
		}
	}

}
