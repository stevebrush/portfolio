<?php
class User extends DatabaseObject {
	
	protected $tableName = "User";
	
	protected $tableFields = array(
		"userId",
		"firstName",
		"lastName",
		"emailAddress",
		"emailConfirmed",
		"password",
		"gender",
		"birthday",
		"birthdayPrivate",
		"currencyId",
		"address1",
		"address2",
		"city",
		"state",
		"zip",
		"interests",
		"favoriteStores",
		"shirtSize",
		"shoeSize",
		"pantSize",
		"hatSize",
		"ringSize",
		"facebookUserId",
		"facebookAccessToken",
		"secret",
		"dateCreated",
		"dateLastLoggedIn",
		"timestamp",
		"roleId",
		"imageId"
	);
	
	protected $userId, 
		$firstName, 
		$lastName, 
		$emailAddress, 
		$emailConfirmed, 
		$password, 
		$gender, 
		$birthday, 
		$birthdayPrivate, 
		$currencyId, 
		$address1, 
		$address2, 
		$city, 
		$state, 
		$zip, 
		$interests,
		$favoriteStores,
		$shirtSize,
		$shoeSize,
		$pantSize,
		$hatSize,
		$ringSize,
		$facebookUserId, 
		$facebookAccessToken, 
		$secret, 
		$dateCreated, 
		$dateLastLoggedIn, 
		$timestamp, 
		$roleId,
		$imageId;
	
	protected $inputSettings = array();
	
	protected $thumbnail,
		$followers,
		$leaders,
		$numFollowers,
		$numFollowing,
		$numGifts;
	
	public function getInputs() {
		
		$this->inputSettings = array(
			"firstName" => array(
				"field" => array(
					"type" => "text",
					"name" => "firstName",
					"label" => "First Name",
					"maxLength" => "35",
					"required" => "true",
					"autoCapitalize" => "true",
					"value" => $this->printValue($this->firstName)
				),
				"rule" => array(
					"stringLength" => array(1, 35),
					"spacesAllowed" => "false",
					"default" => null
				)
			),
			"lastName" => array(
				"field" => array(
					"type" => "text",
					"name" => "lastName",
					"label" => "Last Name",
					"maxLength" => "90",
					"required" => "true",
					"autoCapitalize" => "true",
					"value" => $this->printValue($this->lastName)
				),
				"rule" => array(
					"stringLength" => array(1, 35),
					"default" => null
				)
			),
			"emailAddress" => array(
				"field" => array(
					"type" => "email",
					"name" => "emailAddress",
					"label" => "Email Address",
					"autoCapitalize" => "false",
					"maxLength" => "255",
					"required" => "true",
					"submitOnReturn" => "true",
					"helplet" => "Your email address will always remain private.",
					"value" => $this->printValue($this->emailAddress)
				),
				"rule" => array(
					"stringLength" => array(1, 255),
					"spacesAllowed" => "false",
					"filters" => array("toLowerCase")
				)
			),
			"gender" => array(
				"field" => array(
					"type" => "radioGroup",
					"name" => "gender",
					"label" => "Gender",
					"required" => "true",
					"choices" => $this->genderChoices()
				),
				"rule" => array()
			),
			"password" => array(
				"field" => array(
					"type" => "password",
					"name" => "password",
					"label" => "New Password",
					"maxLength" => "25",
					"autoComplete" => "false",
					"autoCapitalize" => "false",
					"required" => "true",
					"submitOnReturn" => "true"
				),
				"rule" => array(
					"stringLength" => array(7, 25)
				)
			),
			"birthdayMonth" => array(
				"field" => array(
					"type" => "select",
					"name" => "birthdayMonth",
					"label" => "Birthday",
					"required" => "true",
					"choices" => $this->birthdayChoices("month")
				),
				"rule" => array()
			),
			"birthdayDay" => array(
				"field" => array(
					"type" => "select",
					"name" => "birthdayDay",
					"label" => "Birthday Day",
					"required" => "true",
					"choices" => $this->birthdayChoices("day")
				),
				"rule" => array()
			),
			"birthdayYear" => array(
				"field" => array(
					"type" => "select",
					"name" => "birthdayYear",
					"label" => "Birthday Year",
					"required" => "true",
					"choices" => $this->birthdayChoices("year")
				),
				"rule" => array()
			),
			"interests" => array(
				"field" => array(
					"type" => "textarea",
					"name" => "interests",
					"label" => "Gift Ideas and Interests",
					"placeholder" => "Fishing, cycling, painting, video games, etc.",
					"maxLength" => 500,
					"autoCapitalize" => "true",
					"value" => $this->interests
				),
				"rule" => array(
					"stringLength" => array(0, 500)
				)
			),
			"favoriteStores" => array(
				"field" => array(
					"type" => "text",
					"name" => "favoriteStores",
					"label" => "Likes Gift Cards From",
					"placeholder" => "amazon.com, Target, Lowe's",
					"helplet" => "These are vendors that you would enjoy receiving gift cards for.",
					"maxLength" => 90,
					"value" => $this->favoriteStores
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"shirtSize" => array(
				"field" => array(
					"type" => "text",
					"name" => "shirtSize",
					"label" => "Shirt Size",
					"placeholder" => "small, medium, large, etc.",
					"maxLength" => 90,
					"value" => $this->shirtSize
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"shoeSize" => array(
				"field" => array(
					"type" => "text",
					"name" => "shoeSize",
					"label" => "Shoe Size",
					"placeholder" => "Size 7, Size 9 1/2, etc.",
					"maxLength" => 90,
					"value" => $this->shoeSize
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"pantSize" => array(
				"field" => array(
					"type" => "text",
					"name" => "pantSize",
					"label" => ($this->gender == "female") ? "Pant/dress Size" : "Pant Size",
					"placeholder" => ($this->gender == "female") ? "Size 6, petite, etc." : "34 / 36, etc.",
					"maxLength" => 90,
					"value" => $this->pantSize
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"hatSize" => array(
				"field" => array(
					"type" => "text",
					"name" => "hatSize",
					"label" => "Hat Size",
					"placeholder" => "6 3/8, XL, 24 inches, etc.",
					"maxLength" => 90,
					"value" => $this->hatSize
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"ringSize" => array(
				"field" => array(
					"type" => "text",
					"name" => "ringSize",
					"label" => "Ring Size",
					"placeholder" => "size 7 (ring finger), size 9 (thumb), etc.",
					"maxLength" => 90, 
					"value" => $this->ringSize
				),
				"rule" => array(
					"stringLength" => array(0, 90)
				)
			),
			"facebookUserId" => array(
				"field" => array(
					"name" => "facebookUserId",
					"label" => "Facebook User ID",
					"required" => "true"
				),
				"rule" => array(
					"stringLength" => array(1, 255),
					"spacesAllowed" => "false"
				)
			),
			"facebookAccessToken" => array(
				"field" => array(
					"name" => "facebookAccessToken",
					"label" => "Facebook Access Token",
					"required" => "true"
				),
				"rule" => array(
					"stringLength" => array(1, 500),
					"spacesAllowed" => "false"
				)
			),
			"currencyId" => array(
				"field" => array(
					"type" => "select",
					"name" => "currencyId",
					"label" => "Preferred Currency",
					"choices" => $this->currencyChoices()
				),
				"rule" => array()
			),
			"address1" => array(
				"field" => array(
					"type" => "text",
					"name" => "address1",
					"label" => "Address Line 1",
					"maxLength" => "150",
					"autoCapitalize" => "true",
					"value" => $this->address1
				),
				"rule" => array(
					"stringLength" => array(0, 150)
				)
			),
			"address2" => array(
				"field" => array(
					"type" => "text",
					"name" => "address2",
					"label" => "Address Line 2",
					"maxLength" => "150",
					"autoCapitalize" => "true",
					"value" => $this->address2
				),
				"rule" => array(
					"stringLength" => array(0, 150)
				)
			),
			"city" => array(
				"field" => array(
					"type" => "text",
					"name" => "city",
					"label" => "City",
					"maxLength" => "150",
					"autoCapitalize" => "true",
					"value" => $this->city
				),
				"rule" => array(
					"stringLength" => array(0, 150)
				)
			),
			"state" => array(
				"field" => array(
					"type" => "text",
					"name" => "state",
					"label" => "State/province/region",
					"maxLength" => "5",
					"autoCapitalize" => "on",
					"value" => $this->state
				),
				"rule" => array(
					"stringLength" => array(0, 5)
				)
			),
			"zip" => array(
				"field" => array(
					"type" => "text",
					"name" => "zip",
					"label" => "Postal Code/ZIP",
					"maxLength" => "15",
					"value" => $this->zip
				),
				"rule" => array(
					"stringLength" => array(0, 15)
				)
			)
		);
		return $this->inputSettings;
	}
	
	public function authenticate( $emailAddress, $password ) {
		$u = new $this($this->db);
		$foundUser = $u->set("emailAddress", $emailAddress)->find(1, array("userId", "password"));
		if (!empty($foundUser)) {
			$hashedPassword = $foundUser->password;
			$hasher = new PasswordHash(8, false);
			$passwordsMatch = $hasher->CheckPassword($password, $hashedPassword);
			return ($passwordsMatch) ? $foundUser : false;
		} else {
			return false;
		}
	}
	
	public function facebookAuthenticateByEmailAddress( $fbEmailAddress ) {
		$sql = 	"SELECT userId FROM User WHERE LCASE(emailAddress) = :email LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(":email", $fbEmailAddress, PDO::PARAM_STR);
		$stmt->execute();
		$rowCount = $stmt->rowCount();
		$object = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		return ($rowCount) ? array_shift($object) : false;
	}
	
	public function firstNamePossessive() {
		$firstName = $this->printValue($this->firstName);
		$lastLetter = substr($firstName, -1);
		return ($lastLetter == "s") ? $firstName."'" : $firstName."'s";
	}
	
	public function fullName() {
		return $this->printValue($this->firstName . " " . $this->lastName);
	}
	
	public function createSignature( $unique ) {
		return sha1($unique . $this->userId . $this->secret);
	}
	
	public function birthdayMonth() {
		if (isset($this->birthday)) {
			$date = new DateTime($this->birthday);
			return $date->format("n");
		}
		return false;
	}
	
	public function birthdayDay() {
		if (isset($this->birthday)) {
			$date = new DateTime($this->birthday);
			return $date->format("j");
		}
		return false;
	}
	
	public function birthdayYear() {
		if (isset($this->birthday)) {
			$date = new DateTime($this->birthday);
			return $date->format("Y");
		}
		return false;
	}
	
	public function formattedBirthday() {
		$date = new DateTime($this->birthday);
		return $date->format("F j");
	}
	
	public function birthdayChoices( $type = "month" ) {
		$temp = array();
		switch ($type) {
			case "month":
			default:
				$temp[] = array(
					"label" => "Month",
					"value" => "",
					"selected" => "false"
				);
				$month = $this->birthdayMonth();
				for ($i = 1; $i <= 12; $i++) {
					$isSelected = ($month == $i) ? "true" : "false";
					$temp[] = array(
						"label" => $i,
						"value" => $i,
						"selected" => $isSelected
					);
				}
				break;
			case "day":
				$temp[] = array(
					"label" => "Day",
					"value" => "",
					"selected" => "false"
				);
				$day = $this->birthdayDay();
				for ($i = 1; $i <= 31; $i++) {
					$isSelected = ($day == $i) ? "true" : "false";
					$temp[] = array(
						"label" => $i,
						"value" => $i,
						"selected" => $isSelected
					);
				}
				break;
			case "year":
			default:
				$temp[] = array(
					"label" => "Year",
					"value" => "",
					"selected" => "false"
				);
				$currentYear = date('Y');
				$maxYear = $currentYear - 110;
				$year = $this->birthdayYear();
				for ($i = $currentYear; $i >= $maxYear; $i--) {
					$isSelected = ($year == $i) ? "true" : "false";
					$temp[] = array(
						"label" => $i,
						"value" => $i,
						"selected" => $isSelected
					);
				}
				break;
		}
		return $temp;
	}

	public function currencyChoices() {
		$sql = "SELECT * FROM Currency";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$temp = array();
		foreach ($result as $item) {
			$selected = ($item['currencyId'] == $this->currencyId) ? "true" : "false";
			$temp[] = array("label" => "{$item['label']} ({$item['symbol']})", "value" => $item['currencyId'], "selected" => $selected);
		}
		return $temp;
	}

	public function genderChoices() {
		$genderChoices = array(
			array("label"=>"Male", "value"=>"male"),
			array("label"=>"Female", "value"=>"female")
		);
		for ($i = 0; $i < 2; $i++) {
			$selected = "false";
			if ($genderChoices[$i]["value"] == $this->gender) {
				$selected = "true";
			}
			$genderChoices[$i]["selected"] = $selected;
		}
		return $genderChoices;
	}
	
	public function formattedAddress() {
		$address = "";
		if ($this->address1) $address .= $this->address1 . "<br>";
		if ($this->address2) $address .= $this->address2 . "<br>";
		if ($this->city) $address .= $this->city;
		if ($this->city && $this->state) $address .= ", ";
		if ($this->state) $address .= $this->state . " ";
		if ($this->zip) $address .= $this->zip;
		return (isEmpty($address)) ? false : $address;
	}
	
	public function pronoun( $type = "" ) {
		switch ($type) {
			case "him":
				return ($this->gender == "male") ? "him" : "her";
				break;
			case "his":
				return ($this->gender == "male") ? "his" : "her";
				break;
			case "he":
				return ($this->gender == "male") ? "he" : "she";
			default:
				return "";
				break;
		}
	}
	
	public function isAlso( User $user ) {
		return ($this->userId == $user->userId);
	}
	
	public function hasBlocked( User $user ) {
		$sql = "SELECT * FROM User_Blocked WHERE userId={$this->userId} AND blockedId={$user->userId} LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return ($stmt->rowCount() > 0) ? true : false;
	}
	
	public function emailExists( $email = "" ) {
		if (isset($this->userId)) {
			$sql = "SELECT emailAddress FROM User WHERE LCASE(emailAddress) = :email AND userId != :userId LIMIT 1";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(":email", $email, PDO::PARAM_STR);
			$stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);
		} else {
			$sql = "SELECT emailAddress FROM User WHERE LCASE(emailAddress) = :email LIMIT 1";
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue(":email", $email, PDO::PARAM_STR);
		}
		$stmt->execute();
		$rowCount = $stmt->rowCount();
		return $rowCount ? true : false;
	}
	
	public function acceptsEmailFor( $notificationTypeSlug = "" ) {
		$sql = "SELECT NotificationType_User.userId FROM NotificationType, NotificationType_User WHERE NotificationType.slug = :slug AND NotificationType.notificationTypeId = NotificationType_User.notificationTypeId AND NotificationType_User.userId = :userId LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(":slug", $notificationTypeSlug, PDO::PARAM_INT);
		$stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);
		$stmt->execute();
		$rowCount = $stmt->rowCount();
		return $rowCount ? true : false;
	}
	
	public function emailConfirmed() {
		return filter_var($this->emailConfirmed, FILTER_VALIDATE_BOOLEAN);
	}
	
	public function generateSecret() {
		return sha1(randomString() . $this->userId);
	}
	
	public function birthdayIsPrivate() {
		return filter_var($this->birthdayPrivate, FILTER_VALIDATE_BOOLEAN);
	}
	
	public function isFollowing( User $user ) {
		if ($user->userId == $this->userId) return true;
		$follow = new Follow($this->db);
		$follow = $follow->set(array( "userId" => $this->userId, "leaderId" => $user->userId ))->find(1);
		return $follow;
	}
	
	public function getFollowers() {
		if (is_null($this->followers)) {
			$this->followers = $this->fetchFollowers();
		}
		return $this->followers;
	}
	
	public function getLeaders() {
		if (is_null($this->leaders)) {
			$this->leaders = $this->fetchLeaders();
		}
		return $this->leaders;
	}
	
	public function numFollowers() {
		if (is_null($this->numFollowers)) {
			$this->followers = $this->fetchFollowers();
		}
		return $this->numFollowers;
	}
	
	public function numFollowing() {
		if (is_null($this->numFollowing)) {
			$this->leaders = $this->fetchLeaders();
		}
		return $this->numFollowing;
	}
	
	public function numGifts() {
		if (is_null($this->numGifts)) {
			$gift = new Gift($this->db);
			$gifts = $gift->set("userId", $this->userId)->find();
			$this->numGifts = count($gifts);
		}
		return $this->numGifts;
	}
	
	public function createThumbnail($file = "") {
	
		$app = getApp();
		$date = new DateTime(); 
		$dateCreated = $date->format($app->config("date","format"));
	
		$oldThumbnail = $this->getThumbnail(); // preserve old thumbnail
	
		// build new thumbnail
		$img = new Image($this->db);
		$img->set(array(
			"userId" => $this->userId,
			"file" => $file,
			"uploadPath" => USER_UPLOAD_PATH,
			"uploadUrl" => USER_UPLOAD_URL,
			"dateCreated" => $dateCreated,
			"timestamp" => $dateCreated,
			"fillType" => "outside"
		));
		
		// assign new user thumbnail and delete the old one
		$thumbnail = $img->create();
		if ($thumbnail->isCreated()) {
			$this->set( "imageId", $thumbnail->get("imageId") )->update();
			$oldThumbnail->delete();
		}
		
		return $thumbnail;
	}
	
	public function getThumbnail() {
	
		$img = new Image($this->db);
	
		// Attempt to find profile image
		if (!isEmpty($this->imageId) && $this->imageId != 0) {
			$img->set(array(
				"imageId" => $this->imageId,
				"uploadUrl" => USER_UPLOAD_URL,
				"uploadPath" => USER_UPLOAD_PATH
			));
			if (!$thumbnail = $img->find(1)) {
				$this->set("imageId", 0)->update(); // remove id if image not found
			}
		}
	
		// No image found, use default instead
		if (!isset($thumbnail) || !$thumbnail) {
			$thumbnail = $img->set(array(
				"name" => DEFAULT_PROFILE_IMAGE,
				"extension" => "png",
				"uploadUrl" => IMG_URL
			))->fetchFiles();
		}
	
		return $thumbnail;
	}
	
	public function deleteThumbnail() {
		$thumbnail = $this->getThumbnail();
		$thumbnail->delete();
	}
	
	public function delete() {
		$this->deleteThumbnail();
		return parent::delete();
	}
	
	public function findByKeyword($keywords, $limit = 5, $fields = array("*")) {
		$result = "";
		if (!empty($keywords)) {
		
			$keywordArray = explode(" ",$keywords);
			
			$keywords = "%".$keywords."%";
			$keyword1 = "%".$keywordArray[0]."%";
			
			if (count($keywordArray) > 1) {
				$keyword2 = "%".$keywordArray[1]."%";
				$sql = "SELECT ".join($fields,",")." FROM {$this->tableName} WHERE CONCAT(firstName, ' ', lastName) LIKE ? OR lastName LIKE ? OR firstName LIKE ? OR firstName LIKE ? OR lastName LIKE ? LIMIT {$limit}";
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(1, $keywords, PDO::PARAM_STR);
				$stmt->bindParam(2, $keywords, PDO::PARAM_STR);
				$stmt->bindParam(3, $keywords, PDO::PARAM_STR);
				$stmt->bindParam(4, $keyword1, PDO::PARAM_STR);
				$stmt->bindParam(5, $keyword2, PDO::PARAM_STR);
				$stmt->execute();
			} else {
				$sql = "SELECT ".join($fields,",")." FROM {$this->tableName} WHERE CONCAT(firstName, ' ', lastName) LIKE ? OR lastName LIKE ? OR firstName LIKE ? OR firstName LIKE ? LIMIT {$limit}";
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(1, $keywords, PDO::PARAM_STR);
				$stmt->bindParam(2, $keywords, PDO::PARAM_STR);
				$stmt->bindParam(3, $keywords, PDO::PARAM_STR);
				$stmt->bindParam(4, $keyword1, PDO::PARAM_STR);
				$stmt->execute();
			}
			$result = $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
		}
		return !empty($result) ? $result : false;
	}
	
	private function fetchFollowers() {
		$user = new User($this->db);
		$sql = "SELECT u.userId, u.firstName, u.lastName, u.birthday, u.birthdayPrivate, u.imageId ";
		$sql .= "FROM User AS u, Follow AS f ";
		$sql .= "WHERE f.leaderId = {$this->userId} AND f.userId = u.userId";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$this->numFollowers = $stmt->rowCount();
		return $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
	}
	
	private function fetchLeaders() {
		$user = new User($this->db);
		$sql = "SELECT u.userId, u.firstName, u.lastName, u.birthday, u.birthdayPrivate, u.imageId ";
		$sql .= "FROM User AS u, Follow AS f ";
		$sql .= "WHERE f.userId = {$this->userId} AND f.leaderId = u.userId";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$this->numFollowing = $stmt->rowCount();
		return $stmt->fetchAll(PDO::FETCH_CLASS, $this->tableName, array($this->db));
	}
	/*
	public function jsonSerialize() {
		return [
			"userId" => $this->userId,
			"firstName" => $this->firstName,
			"lastName" => $this->lastName
		];
	}
	*/
}