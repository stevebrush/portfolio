<?php
class Privacy extends DatabaseObject {

	protected $tableName = "Privacy";
	
	protected $tableFields = array(
		"privacyId",
		"label",
		"description"
	);
	
	protected $privacyId, 
		$label, 
		$description;
	
	public static function choices($selectedId = 1) {
		$db = getDB();
		$p = new self($db);
		$pids = $p->find();
		$temp = array();
		foreach ($pids as $p) {
			$selected = ($selectedId == $p->privacyId) ? "true" : "false";
			$temp[] = array("label" => $p->label, "value" => $p->privacyId, "selected" => $selected);
		}
		return $temp;
	}
}