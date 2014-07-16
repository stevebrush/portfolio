<?php
class Session {

	private $loggedIn,
		$userId,
		$message,
		$messageType;

	public function __construct() {
		session_start();
		$this->checkLogin();
		$this->checkMessage();
		$this->checkUrl();
	}
	
	public function isLoggedIn() {
		return $this->loggedIn;
	}
	
	public function login($user) {
		if ($user) {
			$this->userId = $_SESSION['userId'] = $user->get("userId");
			$this->loggedIn = true;
		}
	}
	
	public function logout() {
		
		// Unset instance variables
		$this->loggedIn = false;
		unset($this->userId);
		
		// Make sure it's been initialized already
		if (!isset($_SESSION)) {
			session_start();
		}
		
		// Unset all of the session variables.
		$_SESSION = array();
		
		// Delete session cookie
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		
		// Kill it with fire
		session_destroy();
	}
	
	public function setMessage($str) {
		$_SESSION['message'] = $str;
		$this->message = $str;
	}
	
	public function setMessageType($str) {
		$_SESSION['messageType'] = $str;
		$this->messageType = $str;
	}
	
	public function getMessage() {
		return !empty($this->message) ? $this->message : false;
	}
	
	public function getMessageType() {
		return !empty($this->messageType) ? $this->messageType : null;	
	}
	
	public function getUserId() {
		return isset($this->userId) ? $this->userId : false;
	}
	
	public function getLastUrl() {
		return $this->lastUrl;
	}
	
	private function checkLogin() {
		if (isset($_SESSION['userId'])) {
			$this->userId = $_SESSION['userId'];
			$this->loggedIn = true;
		} else {
			unset($this->userId);
			$this->loggedIn = false;
		}
	}
	
	private function checkMessage() {
		if (isset($_SESSION['message'])) {
			$this->message = $_SESSION['message'];
		}
		unset($_SESSION['message']);
		if (isset($_SESSION['messageType'])) {
			$this->messageType = $_SESSION['messageType'];
		}
		unset($_SESSION['messageType']);
	}
	
	private function checkUrl() {
		$this->lastUrl = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'index.php';
	}
}
