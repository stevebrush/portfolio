<?php
class Grade extends DatabaseObject {
	
	protected $tableName = "Grade";
	
	protected $tableFields = array(
		"gradeId",
		"label"
	);
	
	protected $gradeId,
		$label;
		
	public static function choices($selectedId = 1) {
		$db = getDB();
		$g = new self($db);
		$gids = $g->find();
		$temp = array();
		foreach ($gids as $g) {
			$selected = ($selectedId == $g->gradeId) ? "true" : "false";
			$temp[] = array("label" => $g->label, "value" => $g->gradeId, "selected" => $selected);
		}
		return $temp;
	}
	
}