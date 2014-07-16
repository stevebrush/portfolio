<?php
class Priority extends DatabaseObject {
	
	protected $tableName = "Priority";
	
	protected $tableFields = array(
		"priorityId",
		"label"
	);
	
	protected $priorityId,
		$label;
		
	public static function choices($selectedId = 1) {
		$db = getDB();
		$p = new self($db);
		$pids = $p->find();
		$temp = array();
		foreach ($pids as $p) {
			$selected = ($selectedId == $p->priorityId) ? "true" : "false";
			$temp[] = array("label" => $p->label, "value" => $p->priorityId, "selected" => $selected);
		}
		return $temp;
	}
	
}