<?php
abstract class DatabaseObject {

	protected $db,
		$tableName,
		$tableFields,
		$inputSettings;
	
	public function __construct( PDO $db ) {
		$this->db = $db;
	}
	
	public function query($sql) {
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$object = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		return ($stmt->rowCount()) ? $object : false;
	}
	
	public function create() {
	
		/* CREATE SQL VARS */
		$idLabel = $this->tableFields[0];
		
		$pairs = $this->propertyPairs();
		$keys = array_keys($pairs);
		$values = array_values($pairs);
		
		$valuePairs = array();
		$usedKeys = array();
		for ($i = 0, $len = count($keys); $i < $len; $i++) {
			if (!is_null($this->$keys[$i])) {
				$usedKeys[] = $keys[$i];
				$valuePairs[ ":" . $keys[$i] ] = $values[$i];
			}
		}
		
		/* PREPARE STATEMENT */
		$sql = "INSERT INTO {$this->tableName} (" . join($usedKeys, ",") . ") VALUES (:" . join($usedKeys, ",:") . ")";
		$stmt = $this->db->prepare($sql);
		$stmt->execute($valuePairs);
		
		if (!$rowCount = $stmt->rowCount()) return false;
		
		/* ASSIGN NEW ID TO OBJECT AND SEND BACK */
		$object = $this->set( $idLabel, $this->db->lastInsertId() );
		
		return (!empty($object)) ? $object : false;
		
	}
	
	public function find( $limit = null, $fields = "*", $offset = null, $suffix = "" ) {
		
		
		if (gettype($fields) === "array") {
			$fields = join($fields, ",");
		}
		
		/* CREATE SQL VARS */
		$pairs = $this->propertyPairs();
		$keys = array_keys($pairs);
		$values = array_values($pairs);
		
		$whereArray = array();
		$valuePairs = array();
		for ($i = 0, $len = count($keys); $i < $len; $i++) {
			if (!is_null($this->$keys[$i])) {
				$whereArray[] = $keys[$i]. "=:" . $keys[$i];
				$valuePairs[":" . $keys[$i]] = $values[$i];
			}
		}
		
		/* SET THE LIMIT */
		$limitStr = "";
		if (isset($limit)) {
			if (isset($offset)) {
				$limitStr .= " LIMIT " . $offset . "," . $limit;
			} else {
				$limitStr .= " LIMIT " . $limit;
			}
		}
		
		/* PREPARE STATEMENT */
		if (!count($whereArray)) {
			$sql = "SELECT " . $fields . " FROM {$this->tableName}" . $suffix . $limitStr; // Find all
		} else {
			$sql = "SELECT " . $fields . " FROM {$this->tableName} WHERE " . join($whereArray, " AND ") . $suffix . $limitStr; // Find by set
		}
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute($valuePairs);
		$object = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		$rowCount = $stmt->rowCount();
		
		/* RETURN CLASS OBJECT */
		if ($limit == 1 && $rowCount == 1) {
			return array_shift($object);
		}
		return ($rowCount) ? $object : false;
		
	}
	
	public function update() {
	
		/* CREATE SQL VARS */
		$idLabel = $this->tableFields[0];
		$id = $this->$idLabel;
		
		$pairs = $this->propertyPairs();
		$keys = array_keys($pairs);
		$values = array_values($pairs);
		
		$setArray = array();
		$valuePairs = array();
		
		for ($i = 0, $len = count($keys); $i < $len; $i++) {
			if ($keys[$i] == $idLabel) {
				continue; // don't include the table id
			}
			$setArray[] = $keys[$i]. "=:" . $keys[$i];
			$valuePairs[":" . $keys[$i]] = $values[$i];
		}
		
		/* PREPARE STATEMENT */
		$sql = "UPDATE {$this->tableName} SET " . join($setArray, ",") . " WHERE {$idLabel}={$id}";
		$stmt = $this->db->prepare($sql);
		/*
		for ($i = 0, $len = count($keys); $i < $len; $i++) {
			if ($keys[$i] == $idLabel) continue; // don't include the table id
			$type = $this->getPDOType($values[$i]);
			$stmt->bindParam(":".$keys[$i], $values[$i], $type);
		}
		*/
		$stmt->execute($valuePairs);
		$object = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		
		/* RETURN CLASS OBJECT */
		return ($rowCount = $stmt->rowCount()) ? array_shift($object) : false;
		
	}
	
	public function delete() {
	
		/* CREATE SQL VARS */
		$idLabel = $this->tableFields[0];
		
		$pairs = $this->propertyPairs();
		$keys = array_keys($pairs);
		$values = array_values($pairs);
		
		$whereArray = array();
		$valuePairs = array();
		for ($i = 0, $len = count($keys); $i < $len; $i++) {
			if (!is_null($this->$keys[$i])) {
				$whereArray[] = $keys[$i] . "=:" . $keys[$i];
				$valuePairs[":" . $keys[$i]] = $values[$i];
			}
		}
		
		/* PREPARE STATEMENT */
		if (!count($whereArray)) return false;
		$sql = "DELETE FROM {$this->tableName} WHERE " . join($whereArray, " AND ");
		$stmt = $this->db->prepare($sql);
		$stmt->execute($valuePairs);
		
		return ($affectedRows = $stmt->rowCount()) ? true : false;
	}
	
	public function get( $property = "" ) {
		if (!isset($this->$property)) return false;
		if (gettype($this->$property) === "array") return $this->$property;
		return $this->printValue($this->$property);
	}
	
	public function set( $property, $value = "" ) {
		if (gettype($property) === "array") {
			foreach ($property as $k => $v) {
				$this->$k = $v;
			}
			return $this;
		}
		$this->$property = $value;
		return $this;
	}
	
	public function printValue( $value = "" ) {
		return htmlentities($value);
	}
	
	public function getInputs() {
		return $this->inputSettings;
	}
	
	public function getInput($name) {
		return (isset($this->inputSettings[$name])) ? $this->inputSettings[$name] : false;
	}
	
	public function getField($name) {
		return $this->inputSettings[$name]["field"];
	}
	
	public function getRule($name) {
		return $this->inputSettings[$name]["rule"];
	}
	
	public function getTableName() {
		return $this->tableName;
	}
	
	private function propertyPairs() {
		$pairs = array();
		foreach ($this->tableFields as $f) {
			if ( property_exists($this, $f) && gettype($this->$f) !== "NULL") {
				$pairs[$f] = $this->$f;
			}
		}
		return $pairs;
	}
	
	/*
	private function getPDOType($var) {
		$type = gettype($var);
		///echo $type." [".$var."]<br>";
		switch ($type) {
			case null:
			case "null":
			case "NULL":
				return PDO::PARAM_NULL;
			break;
			case "integer":
				return PDO::PARAM_INT;
			break;
			case "string":
			default:
				return PDO::PARAM_STR;
			break;
		}
	}
	*/
}