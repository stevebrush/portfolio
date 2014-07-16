<?php
class Holiday_User extends DatabaseObject {
	protected $tableName = "Holiday_User";
	protected $tableFields = array(
		"holidayUserId",
		"userId",
		"holidayId"
	);
	protected $holidayUserId,
		$userId,
		$holidayId;
}