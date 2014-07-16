<?php 
class NotificationTypeCustom extends DatabaseObject {

	protected $tableName = "NotificationTypeCustom";
	protected $tableFields = array(
		"notificationTypeCustomId",
		"userId",
		"label",
		"month",
		"day",
		"notificationSent",
		"dateCreated"
	);
	
	protected $notificationTypeCustomId,
		$userId,
		$label,
		$month,
		$day,
		$notificationSent,
		$dateCreated;
		
	protected $inputSettings = array();
	
	public function getInputs() {
		$app = getApp();
		$this->inputSettings = array(
			"label" => array(
				"field" => array(
					"type" => "text",
					"name" => "label",
					"label" => "Reminder name",
					"placeholder" => "e.g., Wedding Anniversary, Grandson's Birthday, etc.",
					"maxLength" => "90",
					"required" => "true",
					"autoCapitalize" => "true",
					"value" => $this->printValue($this->label)
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"month" => array(
				"field" => array(
					"type" => "select",
					"name" => "month",
					"label" => "Month",
					"required" => "true",
					"choices" => $this->dateChoices("month")
				),
				"rule" => array()
			),
			"day" => array(
				"field" => array(
					"type" => "select",
					"name" => "day",
					"label" => "Day",
					"required" => "true",
					"choices" => $this->dateChoices("day")
				),
				"rule" => array()
			)
		);
	}
	private function dateChoices($type) {
		$temp = array();
		switch ($type) {
			case "month":
			default:
				$temp[] = array("label" => "Select...", "value" => "", "selected" => "false");
				for ($i = 1; $i <= 12; $i++) {
					$isSelected = ($this->month == $i) ? "true" : "false";
					$temp[] = array("label" => $i, "value" => $i, "selected" => $isSelected);
				}
			break;
			case "day":
				$temp[] = array("label" => "Select...", "value" => "", "selected" => "false");
				for ($i = 1; $i <= 31; $i++) {
					$isSelected = ($this->day == $i) ? "true" : "false";
					$temp[] = array("label" => $i, "value" => $i, "selected" => $isSelected);
				}
			break;
		}
		return $temp;
	}
}