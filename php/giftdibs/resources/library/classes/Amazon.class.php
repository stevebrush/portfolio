<?php
abstract class Amazon {
	
	static public function fetchByKeyword( $keywords, $responseGroup = "ItemAttributes,Images,Offers" ) {
		$app = getApp();
		$Operation = "ItemSearch"; 
		$Version = $app->config("amazon","api-version"); 
		$ResponseGroup = $responseGroup; 
		$request = $app->config("amazon","endpoint") . 
			"?Service=AWSECommerceService" . 
			"&AssociateTag=" . $app->config("amazon","associate-tag") . 
			"&AWSAccessKeyId=" . $app->config("amazon","access-key-id") . 
			"&Operation=" . $Operation . 
			"&Version=" . $Version . 
			"&SearchIndex=All" . 
			"&MerchantId=All" . 
			"&Keywords=" . $keywords . 
			"&ResponseGroup=" . $ResponseGroup;
		$request = self::formatRequest($request);
		$response = file_get_contents($request);
		$xml = simplexml_load_string($response);
		$items = $xml->Items->Item;
		if (empty($items)) return false;
		return $items;
	}
	
	static public function fetchById( $asin = "0", $responseGroup = "ItemAttributes,Images,Offers,Similarities" ) {
		$app = getApp();
		$Operation = "ItemLookup"; 
		$Version = $app->config("amazon","api-version"); 
		$ResponseGroup = $responseGroup;
		$request = $app->config("amazon","endpoint") . 
			"?Service=AWSECommerceService&" . 
			"&AssociateTag=" . $app->config("amazon","associate-tag") . 
			"&AWSAccessKeyId=" . $app->config("amazon","access-key-id") . 
			"&Operation=" . $Operation . 
			"&Version=" . $Version . 
			"&ItemId=" . $asin . 
			"&MerchantId=All" . 
			"&ResponseGroup=" . $ResponseGroup;
		$request = self::formatRequest($request);
		$response = file_get_contents($request);
		$xml = simplexml_load_string($response);
		$item = $xml->Items->Item;
		if (empty($item)) return false;
		return $item;
	}
	
	static private function formatRequest($request) {

		$app = getApp();

		$secret_key = $app->config("amazon","access-key-secret");
		$access_key = $app->config("amazon","access-key-id");
		$version = $app->config("amazon","api-version");
		
	    // Get a nice array of elements to work with
	    $uri_elements = parse_url($request);
	 
	    // Grab our request elements
	    $request = $uri_elements["query"];
	 
	    // Throw them into an array
	    parse_str($request, $parameters);
	 
	    // Add the new required paramters
	    $parameters["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
	    $parameters["Version"] = $version;
	    if (strlen($access_key) > 0) {
	        $parameters["AWSAccessKeyId"] = $access_key;
	    }   
	 
	    // The new authentication requirements need the keys to be sorted
	    ksort($parameters);
	 
	    // Create our new request
	    foreach ($parameters as $parameter => $value) {
	        // We need to be sure we properly encode the value of our parameter
	        $parameter = str_replace("%7E", "~", rawurlencode($parameter));
	        $value = str_replace("%7E", "~", rawurlencode($value));
	        $request_array[] = $parameter . "=" . $value;
	    }   
	 
	    // Put our & symbol at the beginning of each of our request variables and put it in a string
	    $new_request = implode("&", $request_array);
	 
	    // Create our signature string
	    $signature_string = "GET\n{$uri_elements['host']}\n{$uri_elements['path']}\n{$new_request}";
	 
	    // Create our signature using hash_hmac
	    $signature = urlencode(base64_encode(hash_hmac("sha256", $signature_string, $secret_key, true)));
	 
	    // Return our new request
	    return "http://{$uri_elements['host']}{$uri_elements['path']}?{$new_request}&Signature={$signature}";
	    
	}
	
}

/*
public function getTitle() {
	return (string)$this->data->ItemAttributes->Title;
}

public function getThumbnail($size="large") {
	$h = (int)$this->data->SmallImage->Height;
	$w = (int)$this->data->SmallImage->Width;
	$class = ($h < $w) ? "force-width" : "force-height";
	
	switch ($size) {
		case "small":
		$data = array(
			"src" => (string)$this->data->SmallImage->URL,
			"height" => $h,
			"width" => $w,
			"class" => $class
		);
		break;
		case "medium":
		$data = array(
			"src" => (string)$this->data->MediumImage->URL,
			"height" => (string)$this->data->MediumImage->Height,
			"width" => (string)$this->data->MediumImage->Width,
			"class" => $class
		);
		break;
		case "large":
		default:
		$data = array(
			"src" => (string)$this->data->LargeImage->URL,
			"height" => (string)$this->data->LargeImage->Height,
			"width" => (string)$this->data->LargeImage->Width,
			"class" => $class
		);
		break;
	}
	return $data;
}

public function getOffers() {
	return (array)$this->data->Offers;
}

public function getSimilarProducts() {
	return (array)$this->data->SimilarProducts;
}

public function isOnSale() {
	$originalPrice = $this->getOriginalPrice();
	$salePrice = $this->getPrice();
	
	if ($salePrice < $originalPrice) {
		return true;
	} else {
		return false;
	}
}

public function getSalePercentage() {
	$originalPrice = $this->getOriginalPrice();
	$salePrice = $this->getPrice();
	if ($originalPrice > 0) {
		$formatted = round(((($originalPrice - $salePrice) / $originalPrice) * 100), 2) . "%";
	} else {
		$formatted = false;
	}
	return $formatted;
}

public function getPrice() {
	
	$price = 0;
	
	if (isset($this->data->Offers->Offer->OfferListing)) {
		if ($this->data->Offers->Offer->OfferListing->Price->Amount) {
			$price = (int)$this->data->Offers->Offer->OfferListing->Price->Amount;
		}
	}
	if ($price == 0) {
		if (isset($this->data->OfferSummary) && isset($this->data->OfferSummary->LowestNewPrice) && is_numeric($this->data->OfferSummary->LowestNewPrice)) {
			$price = (int)$this->data->OfferSummary->LowestNewPrice->Amount;
		} else {
			$price = (int)$this->data->ItemAttributes->ListPrice->Amount;
		}
	}
	return $price;
}

public function getOriginalPrice() {
	return (int)$this->data->ItemAttributes->ListPrice->Amount;
}

public function getDescription() {
	return join(", ",(array)$this->data->ItemAttributes->Feature);
}

public function getCategory() {
	return $this->data->ItemAttributes->ProductGroup;
}

public function getMerchant() {
	return "Amazon";
}

public function getMerchantUrl() {
	if (isset($this->data->Offers->Offer->OfferListing)) {
		$listingId = $this->data->Offers->Offer->OfferListing->OfferListingId;
		$url = "http://www.amazon.com/gp/aws/cart/add.html?OfferListingId.1={$listingId}&Quantity.1=1&SubscriptionID={$app->config('amazon','associate-key-id')}";
		return $url;
	} else {
		return (string)$this->data->DetailPageURL;
	}
}

public function getDetailUrl() {
	return (string)$this->data->DetailPageURL;
}

public function getProductIdType() {
	return "asin";
}

public function getProductId() {
	return (string)$this->data->ASIN;
}

public function isEligibleForFreeShipping() {
	if (isset($this->data->Offers->Offer->OfferListing)) {
		if ((int)$this->data->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping == 1) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

public function findItemByProductId($asin="0", $responseGroup="ItemAttributes,Images,Offers,Similarities") {
	$Operation = "ItemLookup"; 
	$Version = $app->config('amazon','api-version'); 
	$ResponseGroup = $responseGroup;
	$request = $app->config('amazon','endpoint') . 
		"?Service=AWSECommerceService&" . 
		"&AssociateTag=" . $app->config('amazon','associate-tag') . 
		"&AWSAccessKeyId=" . $app->config('amazon','access-key-id') . 
		"&Operation=" . $Operation . 
		"&Version=" . $Version . 
		"&ItemId=" . $asin . 
		"&MerchantId=All" . 
		"&ResponseGroup=" . $ResponseGroup;
	$request = $this->formatRequest($request);
	$response = file_get_contents($request);
	$xml = simplexml_load_string($response);
	$data = $xml->Items->Item;
	if (empty($data)) return false;
	$item = new $this($app, array("data"=>$data));
	return $item;
}

public function findItemsByProductId($asins="0", $responseGroup="ItemAttributes,Images,Offers,Similarities") {
	$Operation = "ItemLookup"; 
	$Version = $app->config('amazon','api-version'); 
	$ResponseGroup = $responseGroup;
	$request = $app->config('amazon','endpoint') . 
		"?Service=AWSECommerceService&" . 
		"&AssociateTag=" . $app->config('amazon','associate-tag') . 
		"&AWSAccessKeyId=" . $app->config('amazon','access-key-id') . 
		"&Operation=" . $Operation . 
		"&Version=" . $Version . 
		"&ItemId=" . $asins . 
		"&MerchantId=All" . 
		"&ResponseGroup=" . $ResponseGroup;
	$request = $this->formatRequest($request);
	$response = file_get_contents($request);
	$xml = simplexml_load_string($response);
	$items = $xml->Items->Item;
	$allItems = array();
	foreach ($items as $item) {
		$allItems[] = new $this($app, array("data"=>$item));
	}
	return $allItems;
}

public function findItemsByKeyword($Keywords, $responseGroup="ItemAttributes,Images,Offers") {
	$Operation = "ItemSearch"; 
	$Version = $app->config('amazon','api-version'); 
	$ResponseGroup = $responseGroup; 
	$request = $app->config('amazon','endpoint') . 
		"?Service=AWSECommerceService" . 
		"&AssociateTag=" . $app->config('amazon','associate-tag') . 
		"&AWSAccessKeyId=" . $app->config('amazon','access-key-id') . 
		"&Operation=" . $Operation . 
		"&Version=" . $Version . 
		"&SearchIndex=All" . 
		"&MerchantId=All" . 
		"&Keywords=" . $Keywords . 
		"&ResponseGroup=" . $ResponseGroup;
	$request = $this->formatRequest($request);
	$response = file_get_contents($request);
	$xml = simplexml_load_string($response);
	$items = $xml->Items->Item;
	$allItems = array();
	foreach ($items as $item) {
		$allItems[] = new $this($app, array("data"=>$item));
	}
	return $allItems;
}


*/