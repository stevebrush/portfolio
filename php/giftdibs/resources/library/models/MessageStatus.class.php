<?php
class MessageStatus extends DatabaseObject {
	protected $tableName = "MessageStatus";
	protected $tableFields = array(
		"messageStatusId",
		"slug"
	);
	protected $messageStatusId;
	protected $slug;
}