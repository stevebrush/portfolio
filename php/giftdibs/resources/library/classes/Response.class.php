<?php 
class Response {
	
	private $defaults = array(
		"status" => "success"
	);
	
	private $app,
		$formattedResponse;
	
	private $successMessage, 
		$successRedirect, 
		$successPackage,
		$errorMessage, 
		$errorRedirect, 
		$errorPackage;
	
	public function __construct(Application $app, $opts=array()) {
		$this->app = $app;
		$this->options = array_merge($this->defaults, $opts);
		//$this->setProperties($this->options);
		//$this->formatResponse();
	}
	
	public function sendIt() {
		echo json_encode($this->options);
	}
	
	/*
	private function formatResponse() {
		
		$arr = array(
			"message" => "",
			"onsuccess" => array(),
			"onerror" => array(),
			"package" => array(
				"list" => array(),
				"target" => ""
			),
			"redirect" => "",
			"status" => ""
		);
		
		$arr = array();
		
		// Set status
		if (isset($this->status) && $this->status === "error") {
			$arr["status"] = "error";
		} else {
			$arr["status"] = "success";
		}
		
		// Set message
		if (isset($this->message)) {
			$arr["message"] = $this->message;
		}
		
		// Set redirect
		if (isset($this->redirect)) {
			$arr["redirect"] = $redirect;
		}
		
		// Set package
		if (isset($this->package)) {
			$arr["package"] = array();
			if (isset($this->package["list"])) {
				$arr["package"]["list"] = $this->package["list"];
			}
		}
		
		/*
		if (isset($this->successMessage)) {
			if (!isset($arr["success"])) $arr["success"] = array();
			$arr["success"]["message"] = $this->successMessage;
		}
		
		if (isset($this->successRedirect)) {
			if (!isset($arr["success"])) $arr["success"] = array();
			$arr["success"]["redirect"] = $this->successRedirect;
		}
		
		if (isset($this->successPackage)) {
			if (!isset($arr["success"])) $arr["success"] = array();
			$arr["success"]["package"] = $this->successPackage;
		}
		
		if (isset($this->errorMessage)) {
			if (!isset($arr["error"])) $arr["error"] = array();
			$arr["error"]["message"] = $this->errorMessage;
		}
		if (isset($this->errorRedirect)) {
			if (!isset($arr["error"])) $arr["error"] = array();
			$arr["error"]["redirect"] = $this->errorRedirect;
		}
		if (isset($this->errorPackage)) {
			if (!isset($arr["error"])) $arr["error"] = array();
			$arr["error"]["package"] = $this->errorPackage;
		}
		
		$this->formattedResponse = $arr;
	}
	*/
	/*
	private function setProperties($opts=array()) {
		foreach ($opts as $key => $val) {
			if (isset($val)) {
				$this->$key = $val;
			}
		}
	}
	*/
	
}
