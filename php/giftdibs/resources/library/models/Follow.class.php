<?php
class Follow extends DatabaseObject {
	
	protected $tableName = "Follow";
	
	protected $tableFields = array(
		"userId",
		"leaderId",
		"dateCreated",
		"timestamp",
	);
	
	protected $userId,
		$leaderId,
		$dateCreated,
		$timestamp;
	
	/*
	public function findByUserId($id=0) {
		if ($id !== 0) {
			$sql = "SELECT * FROM Follow WHERE userId = {$id}";
			$result = $this->runSql($sql);
			return !empty($result) ? $result : false;
		} else {
			return false;
		}
	}
	
	public function findByLeaderId($id=0) {
		if ($id !== 0) {
			$sql = "SELECT * FROM Follow WHERE leaderId = {$id}";
			$result = $this->runSql($sql);
			return !empty($result) ? $result : false;
		} else {
			return false;
		}
	}
	
	public function delete() {
		$sql = "DELETE FROM {$this->tableName} WHERE userId = '{$this->userId}' AND leaderId = {$this->leaderId} LIMIT 1";
		return ($this->db->runQuery($sql)) ? true : false;
	}
	*/
}
