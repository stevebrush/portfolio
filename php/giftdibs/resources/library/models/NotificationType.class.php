<?php
class NotificationType extends DatabaseObject {
	
	protected $tableName = "NotificationType";
	
	protected $tableFields = array(
		"notificationTypeId",
		"slug",
		"label"
	);
	
	protected $notificationTypeId,
		$slug,
		$label;
}