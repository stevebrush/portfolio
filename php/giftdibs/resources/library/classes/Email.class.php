<?php
class Email {
	
	private $defaults = array(
		"subject" => "A message from GiftDibs",
		"title" => "A message from GiftDibs",
		"body" => "",
		"recipients" => array()
	);
	
	private $app,
		$title,
		$body,
		$recipients,
		$fromName,
		$fromAddress;
	
	public function __construct(Application $application, $opts=array()) {
		$this->app 						= $application;
		$this->defaults['fromName'] 	= $this->app->config('email', 'from-name');
		$this->defaults['fromAddress'] 	= $this->app->config('email', 'from-address');
		$this->options 					= array_merge($this->defaults, $opts);
		$this->setProperties();
	}
	
	public function create() {
	
		$mail = new PHPMailer();
		$mail->IsHTML(true);
		
	    $mail->IsSMTP();
		//$mail->SMTPDebug = 2;
		$mail->SMTPSecure 	= 'ssl';
		$mail->Host 		= $this->app->config('email','host');
		$mail->SMTPAuth 	= true; 
		$mail->Username 	= $this->app->config('email','username');
		$mail->Password 	= $this->app->config('email','password');
		$mail->Port 		= 465;
		$mail->From 		= $this->fromAddress;
		$mail->FromName 	= $this->fromName;
		$mail->Subject 		= $this->subject;
		$mail->Body 		= $this->getIncludeContents(TEMPLATE_PATH.'email.template.php', $this->options); // HTML -> PHP!
		
		// Send the same email to multiple recipients
		foreach ($this->recipients as $name => $email) {
			$mail->AddAddress($email, $name);
			if (!$mail->Send()) {
				return array("mailer-errors" => array(
					"message" => "Message could not be sent.",
					"error" => $mail->ErrorInfo
				));
			} else {
				$mail->clearAddresses();
				$mail->clearAttachments();
				return array("mailer-success" => array(
					"message" => "Email successfully sent."
				));
			}
		}
	}
	
	private function getIncludeContents($filename, $emailVars) {
	    extract($emailVars);
	    if (is_file($filename)) {
	        ob_start();
	        include $filename;
	        return ob_get_clean();
	    }
	    return false;
	}
	
	private function setProperties() {
		$this->options["emailPreferencesUrl"] = $this->app->config("page", "email-preferences");
		$this->options["privacyUrl"] = $this->app->config("page", "privacy");
		foreach ($this->options as $key => $val) {
			if (isset($val)) {
				$this->$key = $val;
			}
		}
	}
}