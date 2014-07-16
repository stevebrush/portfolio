<?php
class DibStatus extends DatabaseObject {
	
	protected $tableName = "DibStatus";
	
	protected $tableFields = array(
		"dibStatusId",
		"slug",
		"label"
	);
	
	protected $dibStatusId,
		$slug,
		$label;
	
}