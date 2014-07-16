<?php
class MessageReply extends DatabaseObject {
	protected $tableName = "MessageReply";
	protected $tableFields = array(
		"messageReplyId",
		"messageId",
		"userId",
		"content",
		"dateCreated",
		"timestamp"
	);
	protected $messageReplyId,
		$messageId,
		$userId,
		$content,
		$dateCreated,
		$timestamp;
	
	protected $inputSettings = array();
	
	public function getInputs() {
		$app = getApp();
		$this->inputSettings = array(
			"content" => array(
				"field" => array(
					"type" => "text",
					"name" => "content",
					"label" => "Message",
					"maxLength" => "5000",
					"autoCapitalize" => "true",
					"required" => "true",
					"value" => $this->printValue($this->content)
				),
				"rule" => array(
					"stringLength" => array(1, 5000)
				)
			)
		);
	}
}