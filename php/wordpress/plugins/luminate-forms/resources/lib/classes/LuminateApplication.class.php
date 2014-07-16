<?php
/*
	Methods prefixed with an underscore (_) are directly correlated to Convio Open API methods.
*/
class LuminateApplication {

	private $config;

	public function __construct(array $config) {
		$this->config = $config;
	}
	
	public function config($key, $value) {
		/*
		 * Returns information from the config file
		 */
		if (isset($this->config[$key]) && isset($this->config[$key][$value])) {
			return $this->config[$key][$value];
		} else {
			return false;
		}
	}
	
	public function url($sPath = "", $aVars = array()) {
		
		/* Create an appropriate URL */
		
		if (empty($sPath)) return false;
		
		$i = 0;
		$len = count($aVars);
		$sVars = "";
		
		if ($len) {
			$sVars = (strpos($sPath, "?")) ? "&" : "?";
			foreach ($aVars as $k => $v) {
				$sVars .= $k."=".$v;
				if ($i++ !== $len) $sVars .= "&";
			}
		}
		
		return $sPath.$sVars;
	}
	
	public function currentUrl($includeArgs = false) {
		$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$urlArray = explode('?', $currentUrl);
		return ($includeArgs) ? $urlArray[0].$this->getQueryString() : $urlArray[0];
	}
	
	public function getQueryString() {
		// collect url variables that are unrelated to API 
		// and append them to the API redirects (so they are preserved)
		parse_str($_SERVER['QUERY_STRING'], $output);
		$temp = array();
		foreach ($output as $k => $v) {
			switch ($k) {
				case "code":
				case "cons_id":
				case "message":
				case "signature":
				//case "success":
				//case "thankYouPageContent":
				//case "nextUrl":
				case "ts":
				case "login_test_url":
					break;
				default:
					$temp[] = $k."=".$v;
					break;
			}
		}
		if (!count($temp)) {
			return "";
		}
		return "&" . join('&', $temp);
	}
	
	public function commonParameters($login = true) {
		$loginInfo = ($login) ? "&login_name={$this->config('api','login-name')}&login_password={$this->config('api','login-password')}" : "";
		return "&api_key={$this->config('api','key')}&v={$this->config('api','version')}&response_format=json&suppress_response_codes=true&sign_redirects=true{$loginInfo}";
	}
	
	public function redirectTo($location) {
		$location = ($location) ? $location : 'index.php';
		header('Location: ' . $location);
		exit();
	}
	
	public function fetchFieldChoices($name) {

		$fields = $this->_listUserFields("update");

		for ($i=0, $length = count($fields); $i < $length; $i++) {

			$field = $fields[$i];
			$thisName = $field['name'];

			if ($thisName == $name) {

				$temp = array(
					array("label" => "Select...", "value" => "")
				);
				foreach ($field["choices"]["choice"] as $k => $v) {
					$temp[] = array("label" => $v, "value" => $k);
				}
				return $temp;
			}

		}

		return false;
	}
	
	public function _login() {}
	
	public function _loginTest() {
		$urlVars = $this->getQueryString();
		$successRedirect = urlencode($this->currentUrl().'?cons_id=${loginResponse/cons_id}&login_test_url=${loginResponse/login_test_url}'.$urlVars);
		$errorRedirect = urlencode($this->currentUrl().'?code=${errorResponse/code}&message=${errorResponse/message}'.$urlVars);
		$url = $this->config('api','http').'CRConsAPI/?method=loginTest&api_key='.$this->config('api','key').'&v='.$this->config('api','version').'&response_format=json&success_redirect='.$successRedirect.'&error_redirect='.$errorRedirect;
		$this->redirectTo($url);
	}
	
	public function _getSingleSignOnToken($consId=0) {
		$url = "{$this->config('api','https')}SRConsAPI";
		$args = "method=getSingleSignOnToken";
		if ($consId !== 0) {
			$args .= "&cons_id={$consId}";
		}
		$args .= $this->commonParameters();
		$result = $this->postApiData($url, $args);
		return $result["getSingleSignOnTokenResponse"]["token"];
	}
	
	public function _getUser($consId) {	
		$url 	= "{$this->config('api','https')}SRConsAPI";
		$vars 	= "method=getUser&cons_id={$consId}{$this->commonParameters()}";
		$userResult = $this->postApiData($url, $vars);
		$userResult = $userResult["getConsResponse"];
		return $userResult;
	}
	
	public function _listUserFields($accessType) {
		$url = "{$this->config('api','https')}CRConsAPI?method=listUserFields&access={$accessType}&include_choices=true&sort_order=group{$this->commonParameters(false)}";
		$array = $this->getApiData($url);
		return $array['listConsFieldsResponse']['field'];
	}
	
	public function _getSurvey($surveyId=0) {
		$loConst = getLoConst();
		$url = "{$this->config('api','https')}CRSurveyAPI?method=getSurvey&survey_id={$surveyId}{$this->commonParameters()}&sso_auth_token={$loConst->getAuthToken()}";
		return $this->getApiData($url);
	}
	
	public function _getDonationFormInfo($formId=0) {
		$url = "{$this->config('api','https')}CRDonationAPI?method=getDonationFormInfo&form_id={$formId}{$this->commonParameters()}";
		return $this->getApiData($url);
	}
	
	private function getApiData($url) {
		if ($result = @file_get_contents($url)) {
			return json_decode($result, true);
		} else {
			return false;
		}
	}
	
	private function postApiData($url, $args) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		curl_setopt($ch, CURLOPT_HEADER, 0);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = json_decode(curl_exec($ch), true);
		curl_close($ch);
		return $result;
	}
}