<?php
class WishList extends DatabaseObject {
	
	protected $tableName = "WishList";
	
	protected $tableFields = array(
		"wishListId",
		"userId",
		"privacyId",
		"name",
		"description",
		"isRegistry",
		"showAddress",
		"dateOfEvent",
		"dateCreated",
		"timestamp"
	);
	
	protected $wishListId, 
		$userId, 
		$privacyId, 
		$name, 
		$description, 
		$isRegistry, 
		$showAddress, 
		$dateOfEvent, 
		$dateCreated, 
		$timestamp;	
		
	protected $inputSettings = array();
	
	public function getInputs() {
		
		$this->inputSettings = array(
			"name" => array(
				"field" => array(
					"type" => "text",
					"name" => "name",
					"label" => "Wish list name",
					"placeholder" => "Enter name of event, or item category",
					"maxLength" => "90",
					"submitOnReturn" => "true",
					"autoCapitalize" => "true",
					"required" => "true",
					"value" => $this->name
				),
				"rule" => array(
					"stringLength" => array(1, 90)
				)
			),
			"typeOfList" => array(
				"field" => array(
					"type" => "checkbox",
					"name" => "typeOfList",
					"fieldClass" => "type-of-list",
					"checked" => ($this->isRegistry),
					"label" => "This is a registry",
					"value" => "yes",
					"helplet" => "Registries are unique in that they let you see which of your gifts have been dibbed and which haven't."
				), 
				"rule" => array()
			),
			"description" => array(
				"field" => array(
					"type" => "textarea",
					"name" => "description",
					"label" => "Additional notes",
					"maxLength" => "500",
					"autoCapitalize" => "true",
					"value" => $this->printValue($this->description)
				),
				"rule" => array(
					"stringLength" => array(0, 500)
				)
			),
			"privacyId" => array(
				"field" => array(
					"name" => "privacyId",
					"label" => "Privacy",
					"choices" => Privacy::choices($this->privacyId)
				),
				"rule" => array(
					"default" => 3
				)
			),
			"dateOfEvent" => array(
				"field" => array(
					"type" => "text",
					"name" => "dateOfEvent",
					"label" => "Date of event",
					"maxLength" => "10",
					"fieldClass" => "datepicker",
					"helplet" => "Format: mm/dd/yyyy",
					"value" => $this->formattedDateOfEvent()
				),
				"rule" => array()
			),
			"showAddress" => array(
				"field" => array(
					"type" => "checkbox",
					"name" => "showAddress",
					"label" => "Show followers my shipping address",
					"value" => "yes",
					"checked" => $this->showAddress
				),
				"rule" => array()
			)
		);
		return $this->inputSettings;
	}
	
	public function getDateOfEvent() {
		if (!isset($this->dateOfEvent)) return false;
		$d = new DateTime($this->dateOfEvent);
		$formatted = $d->format("F d, Y");
		return $formatted;
	}
	
	public function getType() {
		if ($this->isRegistry) {
			return "registry";
		} else {
			return "wish list";
		}
	}
	
	public function getTypeLabel() {
		if ($this->isRegistry) {
			return "Registry";
		} else {
			return "Wish list";
		}
	}
	
	public function getPrivateUsers() {
		$user = new User($this->db);
		$sql = "SELECT u.firstName, u.lastName, u.userId ";
		$sql .= "FROM WishList AS w, User AS u, WishList_User AS wu ";
		$sql .= "WHERE w.wishListId = '{$this->wishListId}' AND w.wishListId = wu.wishListId AND wu.userId = u.userId";
		$users = $user->query($sql);
		return $users;
	}
	
	public function findForFollower(User $u, $limit = null, $fields = "*", $offset = null, $suffix = "") {
		$wishLists = parent::find($limit, $fields, $offset, $suffix);
		if ($wishLists) {
			foreach ($wishLists as $k => $wishList) {
				if (!$wishList->userCanView($u)) {
					unset($wishLists[$k]);
				}
			}
			if (count($wishLists) === 0) {
				$wishLists = false;
			}
		}
		return $wishLists;
	}
	
	public function userCanView(User $u) {
	
		$userId = $u->get("userId");
		
		// public, user-owned
		if ($this->privacyId == 1 || $this->userId === $userId) {
			return true; 
		}
		
		// draft
		if ($this->privacyId == 2) {
			return false; 
		}
		
		// private wish list
		if ($this->privacyId == 4) {
			if ($okayUsers = $this->getPrivateUsers()) {
				foreach ($okayUsers as $user) {
					if ($user->get("userId") === $userId) {
						return true;
						break;
					}
				}
			}
		}
		
		// followers-only wish list
		if ($this->privacyId == 3) {
			$owner = new User($this->db);
			if ($owner = $owner->set("userId", $this->userId)->find(1, "userId")) {
				if ($u->isFollowing($owner)) {
					return true;
				}
			}
		}
		
		
		/* Check if user blocked */
		
		
		return false;
	}
	
	public function formattedDateOfEvent() {
		if (isEmpty($this->dateOfEvent) || !$this->isRegistry) {
			return false;
		}
		$dateVal = new DateTime($this->dateOfEvent);
		return $dateVal->format("m/d/Y");
	}
	
}
