<?php
class Holiday extends DatabaseObject {
	protected $tableName = "Holiday";
	protected $tableFields = array(
		"holidayId",
		"slug",
		"label",
		"month",
		"day",
		"notificationSent"
	);
	protected $holidayId,
		$slug,
		$label,
		$month,
		$day,
		$notificationSent;
}