<?php
class Dib extends DatabaseObject {
	
	protected $tableName = "Dib";
	
	protected $tableFields = array(
		"dibId",
		"giftId",
		"userId",
		"dibStatusId",
		"quantity",
		"isPrivate",
		"dateCreated",
		"dateProjected",
		"dateDelivered",
		"notificationSent",
		"timestamp"
	);
	
	protected $dibId,
		$giftId,
		$userId,
		$dibStatusId,
		$quantity,
		$isPrivate,
		$dateCreated,
		$dateProjected,
		$dateDelivered,
		$notificationSent,
		$timestamp;
		
	protected $numPossible,
		$numAvailable,
		$numCommitted;
		
	public function hasMultiple() {
		return ($this->numPossible > 1) ? true : false;
	}	
	
	public function numPossible() {
		return (int) $this->numPossible;
	}
	
	public function numAvailable() {
		return (int) $this->numAvailable;
	}
	
	public function numCommitted() {
		return (int) $this->numCommitted;
	}
	
}