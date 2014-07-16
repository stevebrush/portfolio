<?php
class RememberMe extends DatabaseObject {
	
	protected $tableName = "RememberMe";
	
	protected $tableFields = array(
		"rememberMeId",
		"userId",
		"cookieValue"
	);
	
	public function findByCookieValue($val = "") {
		$encryptedCookie = $this->encryptCookie($val);
		$sql = "SELECT * FROM {$this->tableName} WHERE cookieValue=:cookie";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(":cookie", $encryptedCookie, PDO::PARAM_STR);
		$stmt->execute();
		$object = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		return !empty($object) ? array_shift($object) : false;
	}
	
	public function generateCookieValue() {
		return randomString() . $this->userId;
	}
	
	public function setCookieValue($val) {
		$this->cookieValue = $this->encryptCookie($val);
		return $this;
	}
	
	private function encryptCookie($val) {
		return md5($val);
	}
}