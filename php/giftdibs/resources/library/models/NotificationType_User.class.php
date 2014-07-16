<?php
class NotificationType_User extends DatabaseObject {
	
	protected $tableName = "NotificationType_User";
	
	protected $tableFields = array(
		"notificationTypeUserId",
		"notificationTypeId",
		"userId",
		"notificationSent"
	);
	
	protected $notificationTypeUserId,
		$notificationTypeId,
		$userId,
		$notificationSent;
	
}