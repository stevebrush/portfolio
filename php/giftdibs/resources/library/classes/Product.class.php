<?php 
class Product {
	
	private $app;
	
	public $productId,
		$productIdType,
		$name,
		$description,
		$url,
		$price,
		$salePrice,
		$salePercentage;
		
	private $images = array();
	
	public function __construct( Application $app ) {
		$this->app = $app;
	}
	
	public function findByKeyword( $keywords, $limit, $offset = 0 ) {
		
		$keywords = urlencode($keywords);
		$items = array();
		
		/* Amazon */
		if ($xmlResponse = Amazon::fetchByKeyword( $keywords )) {
			foreach ($xmlResponse as $xml) {
				$items[] = self::setAmazonValues($xml);
			}
		}
		return (empty($items)) ? false : $items;
	}
	
	public function find( $limit ) {
	
		$items = array();
		
		switch ($this->productIdType) {
		
			case "asin":
			if ($xmlResponse = Amazon::fetchById( $this->productId )) {
				foreach ($xmlResponse as $xml) {
					$items[] = self::setAmazonValues($xml);
				}
				if (count($items) == 1) $items = array_shift($items); 
			}
			break;
			
			default:
			break;
			
		}
		
		return (empty($items)) ? false : $items;
	}
	
	public function getThumbnail() {
	
		$db = getDB();
		$sizes = array();
		
		$img = new Image($db);
		$img->set(array(
			"src" => $this->images["lg"]["src"],
			"width" => $this->images["lg"]["width"],
			"height" => $this->images["lg"]["height"]
			//"class" => $this->images["lg"]["class"]
		));
		$sizes["lg"] = $img;
		
		$img = new Image($db);
		$img->set(array(
			"src" => $this->images["md"]["src"],
			"width" => $this->images["md"]["width"],
			"height" => $this->images["md"]["height"]
			//"class" => $this->images["md"]["class"]
		));
		$sizes["md"] = $img;
		
		$img = new Image($db);
		$img->set(array(
			"src" => $this->images["sm"]["src"],
			"width" => $this->images["sm"]["width"],
			"height" => $this->images["sm"]["height"]
			//"class" => $this->images["sm"]["class"]
		));
		$sizes["sm"] = $img;
		
		$thumbnail = new Image($db);
		$thumbnail->set("sizes", $sizes);
		
		return $thumbnail;
		
	}
	
	public function urlHtml() {
		if (empty($this->url)) return "";
		$url = parse_url($this->url);
		return "<a class=\"vendor-link\" href=\"{$this->url}\" target=\"_blank\">{$url['host']}</a>";
	}
	
	public function isOnSale() {
		return ($this->salePrice && $this->salePrice < $this->price) ? true : false;
	}
	
	public function bestPrice() {
		return ($this->salePrice && $this->salePrice < $this->price) ? $this->salePrice : $this->price;
	}
	
	public function priceHtml() {
		
		$html = "";
		if ($this->salePrice && $this->salePrice < $this->price) {
			$html = "<span class=\"label label-success\">{$this->salePercentage()}</span>&nbsp;&nbsp;<del class=\"text-muted\">{$this->app->formatPrice($this->price)}</del>&nbsp;&nbsp;<span class=\"text-price\">{$this->app->formatPrice($this->salePrice)}</span>";
		} else {
			$html = "<span class=\"text-price\">{$this->app->formatPrice($this->price)}</span>";
		}
		return $html;
		
	}
	
	public function salePercentage() {
		$originalPrice = $this->price;
		$salePrice = $this->salePrice;
		if ($originalPrice > 0) {
			$formatted = round(((($originalPrice - $salePrice) / $originalPrice) * 100), 2) . "%";
		} else {
			$formatted = false;
		}
		return $formatted;
	}
	
	public function get( $property = "" ) {
		if (!isset($this->$property)) return false;
		return $this->$property;
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
	
	private function setAmazonValues( $xml ) {
		$temp = new $this( $this->app );
		$temp->productIdType 	= "asin";
		$temp->productId 		= (string) $xml->ASIN;
		$temp->name 			= (string) $xml->ItemAttributes->Title;
		$temp->description 		= (string) join( ", ", (array) $xml->ItemAttributes->Feature );
		$temp->category			= (string) $xml->ItemAttributes->ProductGroup;
		$temp->url				= (string) $xml->DetailPageURL;
		$temp->price			= $this->amazonPrice($xml);
		$temp->salePrice		= $this->amazonSalePrice($xml);
		$temp->images			= $this->amazonImages($xml);
		return $temp;
	}
	
	private function amazonPrice( $xml ) {
		if (isset($xml->ItemAttributes->ListPrice)) {
			$price = (string) $xml->ItemAttributes->ListPrice->Amount;
		} else {
			$price = self::amazonSalePrice($xml);
		}
		return $price;
	}
	
	private function amazonSalePrice( $xml ) {
		$price = 0;
		if (isset($xml->Offers->Offer->OfferListing)) {
			if ($xml->Offers->Offer->OfferListing->Price->Amount) {
				$price = (int) $xml->Offers->Offer->OfferListing->Price->Amount;
			}
		}
		if ($price == 0) {
			if (isset($xml->OfferSummary) && isset($xml->OfferSummary->LowestNewPrice) && is_numeric($xml->OfferSummary->LowestNewPrice)) {
				$price = $xml->OfferSummary->LowestNewPrice->Amount;
			} else {
				$price = $xml->ItemAttributes->ListPrice->Amount;
			}
		}
		return (string) $price;
	}
	
	private function amazonImages( $xml ) {
		
		$temp = array();
		$h = (int)$xml->SmallImage->Height;
		$w = (int)$xml->SmallImage->Width;
		//$class = ($h < $w) ? "force-width" : "force-height";
		
		$temp["sm"] = array(
			"src" 		=> (string)$xml->SmallImage->URL,
			"height" 	=> $h,
			"width" 	=> $w
			//"class" 	=> $class
		);
		
		$temp["md"] = array(
			"src" 		=> (string)$xml->MediumImage->URL,
			"height" 	=> (string)$xml->MediumImage->Height,
			"width" 	=> (string)$xml->MediumImage->Width
			//"class" 	=> $class
		);
		
		$temp["lg"] = array(
			"src" 		=> (string)$xml->LargeImage->URL,
			"height" 	=> (string)$xml->LargeImage->Height,
			"width" 	=> (string)$xml->LargeImage->Width
			//"class" 	=> $class
		);
		
		return $temp;
		
	}
	
}