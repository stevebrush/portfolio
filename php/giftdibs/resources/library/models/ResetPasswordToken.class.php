<?php
class ResetPasswordToken extends DatabaseObject {
	
	protected $tableName = "ResetPasswordToken";
	
	protected $tableFields = array(
		"resetPasswordTokenId",
		"userId",
		"token"
	);
	
	protected $resetPasswordTokenId,
		$userId,
		$token;
	
	public function generateToken() {
		$randomString = randomString();
		return $randomString . $this->userId;
	}
	
	public function setToken($val) {
		$this->token = $this->encryptToken($val);
	}
	
	public function findByToken($val = "") {
		$encryptedToken = $this->encryptToken($val);
		$sql = "SELECT * FROM {$this->tableName} WHERE token = :token";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(":token", $encryptedToken, PDO::PARAM_STR);
		$stmt->execute();
		$object = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		return !empty($object) ? array_shift($object) : false;
	}
	
	private function encryptToken($val) {
		return md5($val);
	}
}