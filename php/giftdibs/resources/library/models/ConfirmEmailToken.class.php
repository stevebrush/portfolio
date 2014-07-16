<?php
class ConfirmEmailToken extends DatabaseObject {
	
	protected $tableName = "ConfirmEmailToken";
	
	protected $tableFields = array(
		"confirmEmailTokenId",
		"userId",
		"token"
	);
	
	protected $confirmEmailTokenId,
		$userId,
		$token;

	public function generateToken() {
		$randomString = randomString();
		return $randomString . $this->userId;
	}
	
	public function setToken($val) {
		$this->token = $this->encryptToken($val);
		return $this;
	}
	
	public function encryptToken($val) {
		return md5($val);
	}
}