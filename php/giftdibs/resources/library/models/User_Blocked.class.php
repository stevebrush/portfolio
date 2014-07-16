<?php
class User_Blocked extends DatabaseObject {
	
	protected $tableName = "User_Blocked";
	
	protected $tableFields = array(
		"userId",
		"blockedId",
		"dateCreated"
	);
	
	protected $userId,
		$blockedId,
		$dateCreated;
	
}