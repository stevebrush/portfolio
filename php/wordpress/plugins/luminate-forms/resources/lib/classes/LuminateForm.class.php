<?php
/*
 *	This class helps to create consistent forms and form components.
 */
class LuminateForm {
	
	private $defaults = array(
		"method" => "post",
		"allowUpload" => "false",
		"orientation" => "vertical",
		"enctype" => ""
	);
	
	private $id,
		$action,
		$method,
		$heading,
		$allowUpload,
		$orientation,
		$enctype,
		$cssClass,
		$luminateExtendData;
	
	public function __construct(LuminateApplication $app, $opts = array()) {
		$this->app = $app;
		$this->options = array_merge($this->defaults, $opts);
		$this->setProperties();
		$this->id = "luminate-form_".uniqid();
	}
	
	public function start() {
		$enctype = (!empty($this->enctype)) ? "enctype=\"{$this->enctype}\" " : "";
		$orientation = ($this->orientation == "horizontal") ? " form-horizontal" : "";
		$cssClass = ($this->cssClass) ? " {$this->cssClass}" : "";
		$this->luminateExtendData = (string)json_encode($this->luminateExtendData);
		$html = "<div class=\"form-wrapper lo-form-wrapper\"><form id=\"{$this->id}\" class=\"luminateApi lo-form{$orientation}{$cssClass}\" {$enctype}action=\"{$this->action}\" method=\"{$this->method}\" data-luminateApi='{$this->luminateExtendData}'>";
		print($html);
	}
	
	public function stop() {
		echo "</form></div>";
	}
	
	public function heading() {
		echo "<h2 class=\"form-heading lo-form-heading\">{$this->heading}</h2>";
	}
	
	public function getFormId() {
		return $this->id;
	}
	
	public function alert($message="", $type="success") {
		$style = ($message == "") ? ' style="display:none;"' : '';
		switch ($type) {
			case "success":
			default:
				$alertClass = " alert-success lo-alert lo-alert-success";
				break;
			case "error":
				$alertClass = " alert-danger lo-alert lo-alert-danger";
				break;
		}
		print("<div id=\"form_message_{$this->id}\" class=\"alert lo-alert{$alertClass} form-alert clearfix\"{$style}>{$message}</div>");
	}
	
	public function getOrientation() {
		return $this->orientation;
	}
	
	public function hiddenFields($method) {
		$html = "<input type=\"hidden\" name=\"method\" value=\"{$method}\">";
		$html .= "<input type=\"hidden\" name=\"api_key\" value=\"{$this->app->config('api','key')}\">";
		$html .= "<input type=\"hidden\" name=\"v\" value=\"{$this->app->config('api','version')}\">";
		$html .= "<input type=\"hidden\" name=\"sign_redirects\" value=\"true\">";
		print($html);
	}
	
	/*
	public function successRedirect($url) {
		return $url.'?cons_id=${loginResponse/cons_id}';
	}
	
	public function errorRedirect($url) {
		return $url.'?code=${errorResponse/code}&message=${errorResponse/message}';
	}
	
	public function deliverResponse() {
	
		$code = isset($_GET['code']) ? $_GET['code'] : "";
		$message = isset($_GET['message']) ? $_GET['message'] : "";
		$type = "error";
		
		// Survey
		if (isset($_GET["success"]) && $_GET["success"] == "false") {
			$message = "Something went wrong with the submission of the form. Please double-check the fields and try again.";
			$type = "error";
		} else if (isset($code)) {
			switch ($code) {
			
				case "204":
				$message = ""; // user not logged in, not necessary to display
				break;
			
				case "10":
				$message = stripslashes($message);
				break;
			
				case "11":
				$message = "You entered information that matches an existing profile. You can <a href=\"{$this->app->config('url','login')}\">login</a>, or <a href=\"{$this->app->config('url','reset-password')}\">reset your password</a> if you forgot it.";
				break;
			
				case "14":
				$message = "You are already logged in and cannot create a new record at this time. Please log out, and then attempt to create a new registration.";
				break;
				
				case "22":
				$message = "The email address you entered is not a valid format.";
				break;
				
				case '202':
				$message = "Wrong username/password combination. Did you <a href=\"{$this->app->config('url','reset-password')}\">forget your password</a>?";
				break;
				
				case '1725':
				$message = "You're already submitted this survey.";
				break;
				
				default:
				//$message = "";
				break;
				
			}
		}
		
		if ($message != "") $this->alert($message, $type);
	}
	*/
	
	private function setProperties() {
		foreach ($this->options as $key => $val) {
			if (!isset($val)) continue;
			if ($val === "true" || $val === "yes") $val = true;
			if ($val === "false" || $val === "no") $val = false;
			$this->$key = $val;
		}
	}
	
}
