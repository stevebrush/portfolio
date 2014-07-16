<?php
include "functions.php";

/* Vendors */
include VENDOR_PATH."phpass-0.3".DS."PasswordHash.php";
include VENDOR_PATH."PHPMailer".DS."class.phpmailer.php";
include VENDOR_PATH."facebook".DS."src".DS."facebook.php";
include VENDOR_PATH."wideimage".DS."lib".DS."WideImage.php";
include VENDOR_PATH."APICache".DS."api_cache".DS."API_cache.php";

/* Interfaces */
// include INTERFACE_PATH."Merchant.interface.php";

/* Classes */
include CLASS_PATH."Application.class.php";
include CLASS_PATH."Session.class.php";
include CLASS_PATH."Reminder.class.php";
include CLASS_PATH."Cookie.class.php";
include CLASS_PATH."Page.class.php";
include CLASS_PATH."Email.class.php";

include CLASS_PATH."Form.class.php";
include CLASS_PATH."FormField.class.php";
include CLASS_PATH."Validator.class.php";
include CLASS_PATH."Rule.class.php";
include CLASS_PATH."Response.class.php";

include CLASS_PATH."DatabaseObject.class.php";

include CLASS_PATH."Product.class.php";
include CLASS_PATH."Amazon.class.php";


/* Models */
include MODEL_PATH."User.class.php";
include MODEL_PATH."User_Blocked.class.php";
include MODEL_PATH."RememberMe.class.php";
include MODEL_PATH."ConfirmEmailToken.class.php";
include MODEL_PATH."ResetPasswordToken.class.php";
include MODEL_PATH."Feedback.class.php";
include MODEL_PATH."FeedbackReason.class.php";
include MODEL_PATH."Follow.class.php";

//include MODEL_PATH."EmailAlert.class.php";
//include MODEL_PATH."EmailAlert_User.class.php";

include MODEL_PATH."WishList.class.php";
include MODEL_PATH."WishList_User.class.php";
include MODEL_PATH."Gift.class.php";
include MODEL_PATH."Comment.class.php";
include MODEL_PATH."Image.class.php";
include MODEL_PATH."Privacy.class.php";
include MODEL_PATH."Grade.class.php";
include MODEL_PATH."Priority.class.php";
include MODEL_PATH."Dib.class.php";
include MODEL_PATH."DibStatus.class.php";
include MODEL_PATH."Notification.class.php";
include MODEL_PATH."NotificationType.class.php";
include MODEL_PATH."NotificationType_User.class.php";
include MODEL_PATH."NotificationTypeCustom.class.php";
include MODEL_PATH."Holiday.class.php";
include MODEL_PATH."Holiday_User.class.php";
include MODEL_PATH."Message.class.php";
include MODEL_PATH."MessageStatus.class.php";
include MODEL_PATH."Message_User.class.php";
include MODEL_PATH."MessageReply.class.php";

/* Initialize app and destroy config array */
$app = new Application($config);
unset($config);


/* Start PDO */
$db = new PDO("mysql:host={$app->config('database','host')};dbname={$app->config('database','name')};charset=utf8", "{$app->config('database','username')}", "{$app->config('database','password')}");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


/* Check cookie and login status */
$session = new Session();
if (!$session->isLoggedIn()) {
	$cookie = new Cookie();
	$cookie->setName("rememberMe");
	$cookieValue = $cookie->getValue();
	if ($cookieValue) {
		$rm = new RememberMe($db);
		$rm = $rm->findByCookieValue($cookieValue);
		if ($rm) {
			$user = new User($db);
			if ($foundUser = $user->set("userId", $rm->get("userId"))->find(1)) {
				$session->login($foundUser);
			} else {
				$cookie->destroy();
				$rm->delete();
			}
		} else {
			$cookie->destroy();
		}
	}
}


/* Set 'me' */
$me = new User($db);
if ($session->isLoggedIn()) {
	$me = $me->set("userId", $session->getUserId())->find(1);
	if (!$me) {
		$session->logout();
	}
} else {
	$me->set("userId", 0);
}

/* Initialize Facebook Object */
$facebook = new Facebook($app->getFacebookConfig());