<?php
class Application {
	
	private $config,
		$minAgeAllowed = 13;
	
	public function __construct(array $config) {
		$this->config = $config;
	}
	
	public function redirectTo($location) {
		$location = ($location) ? $location : 'index.php';
		header('Location: ' . $location);
		exit();
	}

	public function currentUrl() {
		return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	public function config($key, $value, $attr=array()) {
		if (isset($this->config[$key]) && isset($this->config[$key][$value])) {
			if ($key == "page") {
				$page = $this->config[$key][$value];				
				$length = count($attr);
				$urlAttributes = "";
				if ($length > 0) {
					$stringStarter = (strpos($page,'?')) ? "&" : "?";
					$urlAttributes = $stringStarter;
					$counter = 0;
					foreach ($attr as $k=>$v) {
						$urlAttributes .= $k."=".$v;
						$counter++;
						if ($counter !== $length) {
							$urlAttributes .= '&';
						}
					}
				}
				return $page.$urlAttributes;
			} else {
				return $this->config[$key][$value];
			}
		} else {
			return "";
		}
	}

	public function validateAge($birthday) {
		if (is_string($birthday)) {
			$birthday = strtotime($birthday);
		}
		if (time() - $birthday < $this->minAgeAllowed * 31536000) { // 31536000 is the number of seconds in a 365 days year.
			return false;
		}
		return true;
	}
	
	public function getMinAgeAllowed() {
		return $this->minAgeAllowed;
	}
	
	public function getFacebookConfig() {
		return $this->config['facebook'];
	}

	public function setEmailAlertConfig(Array $emailAlerts) {
		if ($emailAlerts) {
			foreach ($emailAlerts as $alert) {
				$this->config['email-alert'][$alert->get("label")] = $alert->get("emailAlertId");
			}
		}
	}

	public function friendlyDate($dateTime) {
		if (is_null($dateTime)) {
			$dateTime = $this->config('date','format');
		}
	
		$currentTime = new DateTime('now');
		$compareTime = new DateTime($dateTime);
		
		$interval = $currentTime->diff($compareTime, true);
		
		$seconds = 	($interval->y * 365 * 24 * 60 * 60) + 
              		($interval->m * 30 * 24 * 60 * 60) + 
			  		($interval->d * 24 * 60 * 60) + 
			  		($interval->h * 60 * 60) + 
			  		($interval->i * 60) + 
			  		$interval->s;
		
		// Seconds
		if ($seconds < 60) {
			if ($seconds < 1) {
				$seconds = 1;
			}
			$phrase = ($seconds==1) ? $seconds." second ago" : $seconds." seconds ago";
		} 
		
		// Minutes
		else if ($seconds < 3599) {
			$minutes = round($seconds/60);
			$phrase = ($minutes==1) ? $minutes." minute ago" : $minutes." minutes ago";
		} 
		
		// Hours
		else if ($seconds < 86399) {
			$hours = round($seconds/60/60);
			$phrase = ($hours==1) ? $hours." hour ago" : $hours." hours ago";
		}
		
		// Days
		else if ($seconds < 2591999) {
			$days = round($seconds/60/60/24);
			$phrase = ($days==1) ? $days." day ago" : $days." days ago";
		} 
		
		// Months
		else if ($seconds < 31103999) {
			$months = round($seconds/60/60/24/30);
			$phrase = ($months==1) ? $months." month ago" : $months." months ago";
		} 
		
		// Years
		else {
			$years = round($seconds/60/60/24/30/12);
			$phrase = ($years==1) ? $years." year ago" : $years." years ago";
		}
		
		return $phrase;
	}
	
	public function formatDate( $date, $format = "m/d/Y" ) {
		$timestamp = strtotime( $date );
		return date($format, $timestamp);
	}
	
	public function formatPrice($int = 000, $round = false, $symbol = "$") {
	
		if ($int != 0) {
		
			$formattedPrice = $symbol;
			$int = substr($int, 0, -2) . "." . substr($int, -2) + 0;
			
			if ($round) {
				$formattedPrice .= round($int);
			} else {
				$formattedPrice .= number_format($int, 2, '.', ',');
			}
			
		} else {
			$formattedPrice = "Free";
		}
		
		return $formattedPrice;
	}
	
	public function friendlyUrl($url) {
		$arr = parse_url($url);
		return $arr["host"];
	}
	
	public function check() {
		
	}
}