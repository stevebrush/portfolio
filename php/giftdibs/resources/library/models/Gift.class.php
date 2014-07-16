<?php
class Gift extends DatabaseObject {
	
	protected $tableName = "Gift";
	
	protected $tableFields = array(
		"giftId",
		"userId",
		"wishListId",
		"gradeId",
		"imageId",
		"priorityId",
		"productId",
		"productIdType",
		"name",
		"notes",
		"url",
		"price",
		"quantity",
		"isReceived",
		"dateCreated",
		"timestamp"
	);
	
	protected $giftId, 
		$userId, 
		$wishListId, 
		$gradeId,
		$imageId,
		$priorityId, 
		$productId, 
		$productIdType, 
		$name, 
		$notes, 
		$url, 
		$price, 
		$quantity, 
		$isReceived,
		$dateCreated, 
		$timestamp;
	
	protected $inputSettings = array();
	
	// References to other objects
	protected $thumbnail,
		$dib,
		$product,
		$follower;
	
	public function getInputs() {
		$app = getApp();
		$this->inputSettings = array(
			"wishListId" => array(
				"field" => array(
					"type" => "select",
					"name" => "wishListId",
					"label" => "Wish list",
					"required" => "true",
					"choices" => $this->wishListChoices()
				), 
				"rule" => array(
					"spacesAllowed" => "false"
				)
			),
			"gradeId" => array(
				"field" => array(
					"name" => "gradeId",
					"type" => "select",
					"label" => "Condition",
					"required" => "true",
					"choices" => Grade::choices($this->gradeId)
				),
				"rule" => array(
					"spacesAllowed" => "false",
					"default" => 1
				)
			),
			"name" => array(
				"field" => array(
					"type" => "text",
					"name" => "name",
					"label" => "Gift name",
					"placeholder" => "Keywords or product name",
					"maxLength" => "255",
					"required" => "true",
					"autoCapitalize" => "true",
					"value" => $this->printValue($this->name)
				),
				"rule" => array(
					"stringLength" => array(0, 255)
				)
			),
			"notes" => array(
				"field" => array(
					"type" => "textarea",
					"name" => "notes",
					"label" => "Additional notes",
					"maxLength" => "500",
					"placeholder" => "Sizes, colors, coupon codes, etc.",
					"autoCapitalize" => "true",
					"value" => $this->printValue($this->notes)
				),
				"rule" => array(
					"stringLength" => array(0, 500)
				)
			),
			"url" => array(
				"field" => array(
					"type" => "url",
					"name" => "url",
					"label" => "External link",
					"maxLength" => "550",
					"placeholder" => "http://",
					"value" => (isset($_GET["url"])) ? $_GET["url"] : $this->printValue($this->url)
				),
				"rule" => array(
					"spacesAllowed" => "false",
					"stringLength" => array(0, 550)
				)
			),
			"price" => array(
				"field" => array(
					"type" => "text",
					"name" => "price",
					"label" => "Price",
					"maxLength" => "13",
					"value" => $app->formatPrice($this->price, false, "")
				),
				"rule" => array(
					"stringLength" => array(0, 13),
					"spacesAllowed" => "false"
				)
			),
			"priorityId" => array(
				"field" => array(
					"type" => "select",
					"name" => "priorityId",
					"label" => "Priority",
					"choices" => Priority::choices( ($this->priorityId) ? $this->priorityId : 3 )
				),
				"rule" => array(
					"spacesAllowed" => "false",
					"default" => 3
				)
			),
			"quantity" => array(
				"field" => array(
					"type" => "text",
					"name" => "quantity",
					"label" => "Quantity",
					"maxLength" => "6",
					"required" => "true",
					"value" => ($this->quantity) ? $this->quantity : 1
				),
				"rule" => array(
					"integerRange" => array(0, 999999),
					"stringLength" => array(1, 13),
					"default" => 1,
					"spacesAllowed" => "false"
				)
			)
		);
	}
	
	public function wishListChoices() {
	
		$session = getSession();
	
		if ($this->wishListId) {
			$selectedId = $this->wishListId;
		} else {
			$selectedId = (isset($_GET["wishListId"])) ? $_GET["wishListId"] : "";
		}
		
		$wishList = new WishList($this->db);
		$wishLists = $wishList->set("userId", $session->getUserId())->find(null, "wishListId,name");
		$choices = array();
		
		if ($wishLists) {
			$choices = array(
				array("label" => "Please select", "value" => "")
			);
			foreach ($wishLists as $wishList) {
				$wishListId = $wishList->get("wishListId");
				$selected = ($wishListId == $selectedId) ? "true" : "false";
				$choices[] = array(
					"label" => $wishList->get("name"), 
					"value" => $wishListId,
					"selected" => $selected
				);
			}
		}
		return $choices;
	}
	
	public function priorityHtml() {
		$html = "";
		for ($i=1; $i<=5; $i++) {
			if ($i <= $this->priorityId) {
				$html .= "<span class=\"glyphicon glyphicon-heart text-warning\"></span>";
			} else {
				$html .= "<span class=\"glyphicon glyphicon-heart text-muted\"></span>";
			}
		}
		return $html;
	}
	
	public function priorityLabel() {
		$priority = new Priority($this->db);
		$priority = $priority->set("priorityId", $this->priorityId)->find(1, array("label"));
		return $priority->get("label");
	}
	
	public function priceHtml() {
		$app = getApp();
		$html = "";
		$product = $this->product;
		if ($product) {
			$bestPrice = $product->bestPrice();
			if ($bestPrice < $this->price) {
				// {$product->urlHtml()} <span class=\"text-price\"><del class=\"text-muted\">{$app->formatPrice($this->price)}</del> <span class=\"text-best-price\">{$app->formatPrice($bestPrice)}</span></span>
				$html .= "<span class=\"text-price\"><del class=\"text-muted\">{$app->formatPrice($this->price)}</del> <span class=\"text-best-price\">{$app->formatPrice($bestPrice)}</span></span> {$product->urlHtml()}";
			} else {
				// {$product->urlHtml()} <span class=\"text-price\"><del class=\"text-muted\">{$app->formatPrice($this->price)}</del> <span class=\"text-best-price\">{$app->formatPrice($bestPrice)}</span></span>
				$html .= "<span class=\"text-price\"><span class=\"text-best-price\">{$app->formatPrice($bestPrice)}</span></span> {$product->urlHtml()}";
			}
		} else {
			$html .= "<span class=\"text-price\"><span class=\"text-best-price\">{$app->formatPrice($this->price)}</span></span> {$this->urlHtml()}";
		}
		return $html;
	}
	
	public function urlHtml() {
		if (empty($this->url)) {
			return "";
		}
		$url = parse_url($this->url);
		return "<a class=\"vendor-link\" href=\"{$this->url}\" target=\"_blank\">{$url['host']}</a>";
	}
	
	public function gradeLabel() {
		$sql = "SELECT label FROM Grade WHERE gradeId = '{$this->gradeId}' LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row[0]["label"];
	}
	
	public function isReceived() {
		return ($this->isReceived == 1) ? true : false;
	}
	
	public function isDibbedBy(User $u) {
		$dibs = $this->dib->get("dibs");
		if (!$dibs) return false;
		foreach ($dibs as $dib) {
			if ($dib->get("userId") == $u->get("userId")) {
				return true;
			}
		}
		return false;
	}
	
	public function getDibs() {
		return $this->dib;
	}
	
	public function getDibsBy(User $u) {
		// returns the Dib object for this gift by a certain user
		if ($this->dib->dibs) {
			foreach ($this->dib->dibs as $dib) {
				if ($dib->get("userId") == $u->get("userId")) {
					return $dib;
				}
			}
		}
		return false;
	}
	
	public function findPackage($limit = null, $fields = "*", $offset = null, $suffix = "") {
	
		// Get the gifts from the database
		$gifts = parent::find($limit, $fields, $offset, $suffix);
		
		if (!$gifts) {
			return false;
		}
		
		// only return the gifts the current user may view
		if (isset($this->follower) && $gifts) {
			foreach ($gifts as $k => $gift) {
				if ($gift->userCanView($this->follower)) {
					continue;
				}
				unset($gifts[$k]);
			}
			if (count($gifts) === 0) {
				$gifts = false;
			}
		}
		
		// Only one object set...
		if ($limit == 1) {
			if (!$gifts) {
				return false;
			}
			$gifts->setDibInfo()->setProductInfo();
			return $gifts;
		}
		
		// Get dib information
		if ($gifts) {
			foreach ($gifts as $gift) {
				$gift->setDibInfo()->setProductInfo();
			}
		}
		
		// Get all gifts for the count
		$allItems = parent::find(null, "giftId", null, $suffix);
		$numTotalItems = count($allItems);
		
		// Auto-set limit if null
		if (!isset($limit)) {
			$limit = $numTotalItems;
		}
		
		// Set pagination variables
		$page = ($offset > 0) ? ceil($offset / $limit) + 1 : 1;
		$numRemainingItems = $numTotalItems - ($limit * $page);
		$numTotalPages = ceil($numTotalItems / $limit);
		$numTotalItemsThisPage = ($numRemainingItems > $limit || $numRemainingItems === 0) ? $limit : $numRemainingItems;
		
		// Put it all together
		$data = array(
			"totalItems" => $numTotalItems,
			"remainingItems" => $numRemainingItems,
			"hasMore" => ($numRemainingItems > 0) ? 1 : 0,
			"totalPages" => $numTotalPages,
			"totalItemsThisPage" => $numTotalItemsThisPage,
			"page" => $page,
			"limit" => $limit
		);
		
		// Send it with our blessings!
		return array(
			"gifts" => $gifts,
			"data" => $data
		);
	}
	
	public function userCanView(User $u) {
	
		// For every gift, we are:
		// 1. Retrieving the gift information
		// 2. Getting the associated wish list
		// 4. Getting the wish list's associated users
		// 5. Checking if the user in question is also following the wish list owner
		// (a total of 4 database queries...)
	
		$wishList = new WishList($this->db);
		$found = $wishList->set("wishListId", $this->wishListId)->find(1, "wishListId,userId,privacyId");
		if ($found) {
			return $found->userCanView($u);
		} else {
			return false;
		}
	}
	
	public function getProduct() {
		return $this->product;
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
			"uploadPath" => GIFT_UPLOAD_PATH,
			"uploadUrl" => GIFT_UPLOAD_URL,
			"dateCreated" => $dateCreated,
			"timestamp" => $dateCreated,
			"fillType" => "inside"
		));
		
		// assign new user thumbnail and delete the old one
		$thumbnail = $img->create();
		if ($thumbnail->isCreated()) {
			$this->set("imageId", $thumbnail->get("imageId"))->update();
			$oldThumbnail->delete();
		}
		
		return $thumbnail;
	}
	
	public function getThumbnail() {
	
		$img = new Image($this->db);
	
		// Attempt to find gift image
		if (!isEmpty($this->imageId) && $this->imageId != 0) {
			$img->set(array(
				"imageId" => $this->imageId,
				"uploadUrl" => GIFT_UPLOAD_URL,
				"uploadPath" => GIFT_UPLOAD_PATH
			));
			if (!$thumbnail = $img->find(1)) {
				$this->set("imageId", 0)->update(); // remove id if image not found
			}
		}
	
		// No image found, use default instead
		if (!isset($thumbnail) || !$thumbnail) {
			$thumbnail = $img->set(array(
				"name" => DEFAULT_GIFT_IMAGE,
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
	
	public function setDibInfo() {
		
		// set dib info
		$bigDib = new Dib($this->db);
	
		// get all dib objects for this gift
		$d = new Dib($this->db);
		$dibs = $d->set("giftId", $this->giftId)->find();
		
		// get the number of committed dibs
		$totalCommittedDibs = 0;
		if ($dibs) {
			foreach ($dibs as $dib) {
				$totalCommittedDibs += $dib->get("quantity");
			}
		}
		
		// setup the 'big' dib wrapper to hold all the dibs
		$bigDib->set(array(
			"numPossible" 	=> $this->quantity,
			"numCommitted" 	=> $totalCommittedDibs,
			"numAvailable" 	=> (int) $this->quantity - (int) $totalCommittedDibs,
			"dibs" => $dibs
		));
		
		// set the dib property of the gift
		$this->dib = $bigDib;
			
		return $this;
	}
	
	public function setProductInfo() {
		if (isset($this->productId) && isset($this->productIdType)) {
			$product = new Product(getApp());
			$product = $product->set(array(
				"productId" => $this->productId,
				"productIdType" => $this->productIdType
			))->find(1);
			$this->product = $product;
		}
		return $this;
	}
	
}