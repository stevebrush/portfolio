<?php
class Comment extends DatabaseObject {
	protected $tableName = "Comment";
	protected $tableFields = array(
		"commentId",
		"userId",
		"giftId",
		"content",
		"dateCreated",
		"timestamp"
	);
	protected $commentId,
		$userId,
		$giftId,
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
					"label" => "",
					"maxLength" => "2500",
					"autoCapitalize" => "true",
					"required" => "true",
					"value" => $this->printValue($this->content)
				),
				"rule" => array(
					"stringLength" => array(1, 2500)
				)
			)
		);
	}
}