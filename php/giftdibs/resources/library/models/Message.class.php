<?php
class Message extends DatabaseObject {
	protected $tableName = "Message";
	protected $tableFields = array(
		"messageId",
		"userId",
		"content",
		"dateCreated",
		"timestamp"
	);
	protected $messageId,
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
					"type" => "textarea",
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