<?php
class FeedbackReason extends DatabaseObject {
	
	protected $tableName = "FeedbackReason";
	
	protected $tableFields = array(
		"feedbackReasonId",
		"label"
	);
	
	protected $feedbackReasonId,
		$label;
	
	/*
	public function getReasons() {
		$sql = "SELECT * FROM FeedbackReason";
		$result = $this->db->runQuery($sql);
		$arr = array(array("label"=>"Reason for contact","value"=>""));
		while ($reasons = $this->db->fetchArray($result)) {
			$arr[] = array("label" => $reasons["label"], "value" => $reasons["feedbackReasonId"]);
		}
		return $arr;
	}
	*/
	
}