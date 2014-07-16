<?php 
class Notification extends DatabaseObject {
	
	protected $tableName = "Notification";
	
	protected $tableFields = array(
		"notificationId",
		"notificationTypeId",
		"userId",
		"followerId",
		"giftId",
		"dateCreated"
	);
	
	protected $notificationId,
		$notificationTypeId,
		$userId,
		$followerId,
		$giftId,
		$dateCreated;
		
}