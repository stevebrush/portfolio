<?php
class Message_User extends DatabaseObject {
	protected $tableName = "Message_User";
	protected $tableFields = array(
		"messageUserId",
		"messageId",
		"userId",
		"messageStatusId"
	);
	protected $messageId,
		$userId,
		$messageStatusId;
}