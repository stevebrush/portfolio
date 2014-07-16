<?php
/*
 *	This class stores information about the currently logged-in 
 *	Luminate Constituent record (derived from the API)
 */
class LuminateConstituent {

	public $isLoggedIn = false;

	private $app;
	private $consId;
	private $signature;
	private $timestamp;
	private $testLoginUrl;
	private $authToken;
	private $userData;

	public function __construct(LuminateApplication $app) {
		$this->app = $app;
	}
	
	public function getConsId() {
		return $this->consId;
	}
	
	public function getUserData() {
		return $this->userData;
	}
	
	public function getFieldValue($name="") {
		$nameArray = explode(".", $name);
		$selectedValue = "";
		if (count($nameArray) == 1) {
			if (isset($this->userData[$nameArray[0]])) {
				$selectedValue = $this->userData[$nameArray[0]];
			}
		} else {
			if (isset($this->userData[$nameArray[0]][$nameArray[1]])) {
				$selectedValue = $this->userData[$nameArray[0]][$nameArray[1]];
			}
		}
		return $selectedValue;
	}
	
	public function getAuthToken() {
		if (is_null($this->authToken)) {
			$this->authToken = $this->app->_getSingleSignOnToken($this->consId);
		}
		return $this->authToken;
	}
	
	public function logout() {
		if ($this->isLoggedIn) {
			$this->app->_logout();
		}
	}
	
	public function checkLogin() {
	
		// Before doing a login test, make sure the appropriate API fields are set:
		$http = $this->app->config("api","http");
		$https = $this->app->config("api","https");
		if (empty($http) || empty($https)) {
			$this->isLoggedIn = false;
			return $this->isLoggedIn;
		}
		
		// Run the test
		if (!$this->isLoggedIn) {
			$this->consId 		= isset($_GET['cons_id']) ? $_GET['cons_id'] : '';
			$this->signature 	= isset($_GET['signature']) ? $_GET['signature'] : '';
			$this->timestamp 	= isset($_GET['ts']) ? $_GET['ts'] : '';
			$this->loginTestUrl = isset($_GET['login_test_url']) ? $_GET['login_test_url'] : '';
			$codeExists 		= isset($_GET['code']) ? true : false;
			
			if (!empty($this->loginTestUrl)) {
				if ($xml = simplexml_load_file($this->loginTestUrl)) {
					if (!empty($xml->cons_id)) {
						$this->consId = $xml->cons_id;
						$this->isLoggedIn = true;
					}
				} else {
					// the loginTestUrl expired, so try a new login test
					$this->app->_loginTest();
				}
			} 
			else if ($codeExists) {
				$this->isLoggedIn = false;
			}
			else {
				$this->app->_loginTest();
			}
		}
		
		if ($this->isLoggedIn) {
			$this->fetchUserData();
		}
		
		return $this->isLoggedIn;
	}
	
	private function fetchUserData() {
		$this->userData = $this->app->_getUser($this->consId);
		if ($this->userData) {
			foreach ($this->userData as $key=>$val) {
				if ($val != "") {
					$this->$key = $val;
				}
			}
		} else {
			$this->userData = array();
		}
	}
	
}