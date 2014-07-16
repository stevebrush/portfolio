<?php
class Reminder {
	
	protected $db;
	protected $app;
	
	protected $today;
	protected $duration;
	protected $year;
	
	public function __construct(Application $app) {
		$this->db = getDB();
		$this->app = $app;
		
		// Get current time
		$this->today = time();
		
		// Get number of seconds in 2-weeks
		$this->duration = 60 * 60 * 24 * 14;
		
		// Get current year
		$this->year = date("Y");
	}
	
	public function check() {
		$this->checkHolidays();
		$this->checkBirthdays();
		$this->checkCustomNotifications();
		$this->checkDibExpirations();
	}
	
	private function checkDibExpirations() {
		
		// If the dib's projected delivery date has been met,
		// send an email/notification to the dib owner to verify delivery.
		
		$nt = new NotificationType($this->db);
		$notificationType = $nt->set("slug", "gift-received")->find(1);
		
		// Get all users that own dibs that are reserved or purchased
		
		// Need:
		
		// dibberId
		// dibberFirstName
		// dibberLastName
		// dibberEmailAddress
		
		// giftId
		// giftName
		
		// dibDateProjected
		
		// ownerId
		// ownerFirstName
		// ownerLastName
		
		$sql = "SELECT dibber.userId AS dibberId, dibber.firstName AS dibberFirstName, dibber.lastName AS dibberLastName, dibber.emailAddress AS dibberEmailAddress, ";
		$sql .= "g.giftId, g.name, ";
		$sql .= "d.dateProjected, d.notificationSent, ";
		$sql .= "owner.userId AS ownerId, owner.firstName AS ownerFirstName, owner.lastName AS ownerLastName ";
		$sql .= "FROM User AS dibber, User AS owner, Dib AS d, DibStatus AS ds, NotificationType AS nt, NotificationType_User AS ntu, Gift AS g ";
		$sql .= "WHERE ";
		$sql .= "(ds.slug = 'reserved' OR ds.slug = 'purchased') ";
		$sql .= "AND nt.slug = 'gift-received' ";
		$sql .= "AND ntu.userId = dibber.userId ";
		$sql .= "AND ds.dibStatusId = d.dibStatusId ";
		$sql .= "AND d.notificationSent = '0' ";
		$sql .= "AND d.giftId = g.giftId ";
		$sql .= "AND g.userId = owner.userId ";
		$sql .= "GROUP BY d.dibId";
		
		$dib = new Dib($this->db);
		$dibs = $dib->query($sql);
		if ($dibs && $notificationType) {
			
			$notificationTypeId = $notificationType->get("notificationTypeId");
			$date = new DateTime(); 
			$dateUpdated = $date->format($this->app->config("date", "format"));
			
			while ($dib = array_pop($dibs)) {
				
				// Get time of projected date
				$notificationTime = strtotime($dib->get("dateProjected")) + (60 * 60 * 24); // Add 24 hours
				
				// If date projected is less than today,
				// send the notification
				if ($notificationTime < $this->today) {
				
					$notification = new Notification($this->db);
					$notification->set(array(
						"notificationTypeId" => $notificationTypeId,
						"userId" => $dib->get("dibberId"),
						"giftId" => $dib->get("giftId"),
						"followerId" => $dib->get("ownerId"),
						"dateCreated" => $dateUpdated
					))->create();
					
					// Update notificationSent so it never gets checked again
					$dib->set("notificationSent", "1")->update();
				}
			}
		}
	}
	
	private function checkCustomNotifications() {
	
		$customNotification = new NotificationTypeCustom($this->db);
		$sql = "SELECT n.notificationTypeCustomId, n.userId, n.label, n.month, n.day, n.notificationSent, u.firstName, u.lastName, u.emailAddress FROM NotificationTypeCustom AS n, User AS u WHERE n.userId = u.userId ORDER BY n.userId";
		$notifications = $customNotification->query($sql);
		
		if ($notifications) {
			
			while ($notification = array_pop($notifications)) {
			
				// Get the notification's time
				$notificationTime = strtotime($notification->get("month") . "/" . $notification->get("day") . "/" . $this->year);
				
				// Notification hasn't been sent this year
				// If current time is within the 2-week interval, send the email
				if (($notification->get("notificationSent") === "0") && ($this->today + $this->duration > $notificationTime) && ($this->today < $notificationTime)) {
					
					// Send email
					$email = new Email($this->app, array(
						"title" => "{$this->app->config('app', 'name')} Reminder: {$notification->get('label')}",
						"subject" => "{$this->app->config('app', 'name')} Reminder: {$notification->get('label')}",
						"body" => "<p>This is just a friendly reminder from {$this->app->config('app','name')} that <strong>{$notification->get('label')}</strong> will be here in two weeks!</p><p><a href=\"{$this->app->config('page', 'profile')}\">Find the perfect gift&nbsp;&gt;</a></p>",
						"recipients" => array($notification->get("firstName") . " " . $notification->get("lastName") => $notification->get("emailAddress"))
					));
					$email->create();
				
					// Mark the notification as sent
					$notification->set("notificationSent", "1")->update();
				
				} else if ($notification->get("notificationSent") === "1") {
					
					// If current time is after the notification,
					// reset the notificationSent so it can be checked again
					if ($this->today > $notificationTime) {
						$notification->set("notificationSent", "0")->update();
					}
				}
			}
		}
	}
	
	private function checkBirthdays() {
	
		// Get all users that want to be alerted to their leader's birthdays
		$notificationType = new NotificationType($this->db);
		$notificationType = $notificationType->set("slug", "birthdays")->find(1);
		$notificationTypeId = $notificationType->get("notificationTypeId");
		
		// NEED:
		
		// followerId
		// followerEmailAddress
		
		// leaderId
		// leaderFirstName
		// leaderLastName
		// leaderBirthday
		
		$sql = "SELECT leader.userId AS leaderId, leader.firstName AS leaderFirstName, leader.lastName AS leaderLastName, leader.birthday AS leaderBirthday, ";
		$sql .= "follower.userId AS followerId, follower.firstName AS followerFirstName, follower.lastName AS followerLastName, follower.emailAddress AS followerEmailAddress, ";
		$sql .= "notify.notificationTypeUserId, notify.notificationSent ";
		$sql .= "FROM User AS follower, User AS leader, NotificationType_User AS notify, Follow AS follow ";
		$sql .= "WHERE notify.notificationTypeId = {$notificationTypeId} ";
		$sql .= "AND notify.userId = follower.userId ";
		$sql .= "AND follow.userId = follower.userId ";
		$sql .= "AND follow.leaderId = leader.userId ";
		$sql .= "AND leader.birthdayPrivate = '0' ";
		$sql .= "ORDER BY notify.userId";
		
		$notification = new NotificationType_User($this->db);
		$notifications = $notification->query($sql);
		
		if ($notifications) {
			while ($notification = array_pop($notifications)) {
			
				// yyyy-mm-dd
				$birthdayArray = explode("-", $notification->get("leaderBirthday"));
				$notificationTime = strtotime($birthdayArray[1] . "/" . $birthdayArray[2] . "/" . $this->year);
				
				// Notification hasn't been sent this year
				// If current time is within the 2-week interval, send the email
				if (($notification->get("notificationSent") === "0") && ($this->today + $this->duration > $notificationTime) && ($this->today < $notificationTime)) {
				
					$birthday = date('l, F j', $notificationTime);
					$leaderFirstName = $notification->get("leaderFirstName");
					
					// Send email
					$email = new Email($this->app, array(
						"subject" => "{$leaderFirstName}'s birthday is almost here!",
						"title" => "{$leaderFirstName}'s birthday is on <strong>{$birthday}</strong>.",
						"body" => "<p><a href=\"{$this->app->config('page', 'profile', array('userId' => $notification->get('leaderId')))}\">Take a look at {$leaderFirstName}'s gifts</a> and make it a special birthday!</p>",
						"recipients" => array($notification->get("followerFirstName") . " " . $notification->get("followerLastName") => $notification->get("followerEmailAddress"))
					));
					$email->create();
					
					// Mark the notification as sent
					$notification->set("notificationSent", "1")->update();
					
				} else if ($notification->get("notificationSent") === "1") {
					
					// If current time is after the notification,
					// reset the notificationSent so it can be checked again
					if ($this->today > $notificationTime) {
						$notification->set("notificationSent", "0")->update();
					}
				}
			}
		}
	}
	
	private function checkHolidays() {
	
		// Get all users that want to get emailed for certain holidays
		$sql = "SELECT h.holidayId, h.label, h.slug, h.month, h.day, h.notificationSent, ";
		$sql .= "u.userId, u.firstName, u.lastName, u.emailAddress ";
		$sql .= "FROM User AS u, Holiday AS h, Holiday_User AS hu ";
		$sql .= "WHERE hu.userId = u.userId ";
		$sql .= "AND hu.holidayId = h.holidayId ";
		$sql .= "ORDER BY h.holidayId";
		
		$holiday = new Holiday($this->db);
		$holidays = $holiday->query($sql);
		
		if ($holidays) {
		
			while ($holiday = array_pop($holidays)) {
				
				// Get holiday's time
				$holidayTime = strtotime( $holiday->get("month") . "/" . $holiday->get("day") . "/" . $this->year );
				
				// Notification hasn't been sent this year
				// If current time is within the 2-week interval, send the email
				if (($holiday->get("notificationSent") === "0") && ($this->today + $this->duration > $holidayTime) && ($this->today < $holidayTime)) {
				
					// Send email
					$email = new Email($this->app, array(
						"title" => "{$holiday->get('label')} is just around the corner!",
						"subject" => "{$holiday->get('label')} is just around the corner!",
						"body" => "<p>This is just a friendly reminder from {$this->app->config('app','name')} that <strong>{$holiday->get('label')}</strong> will be here in two weeks!</p><p><a href=\"{$this->app->config('page', 'profile')}\">Find the perfect gift&nbsp;&rarr;</a></p><p><strong>If you do not wish to receive an email notification for this holiday, you can turn it off in your <a href=\"{$this->app->config('page', 'email-preferences')}\">email preferences</a>.</strong></p>",
						"recipients" => array($holiday->get("firstName") . " " . $holiday->get("lastName") => $holiday->get("emailAddress"))
					));
					$email->create();
					
					// Mark the holiday as sent,
					// and update the month/day, if necessary
					$holidayDate = $this->getHolidayDate($holiday->get("slug"));
					if ($holidayDate) {
						$holiday->set(array(
							"notificationSent" => "1",
							"month" => $holidayDate["month"],
							"day" => $holidayDate["day"]
						))->update();
					} else {
						$holiday->set("notificationSent", "1")->update();
					}
					
				} else if ($holiday->get("notificationSent") === "1") {
					
					// If current time is after the holiday,
					// reset the notificationSent so it can be checked again,
					// and update the month/day, if necessary
					if ($this->today > $holidayTime) {
						$holidayDate = $this->getHolidayDate($holiday->get("slug"));
						if ($holidayDate) {
							$holiday->set(array(
								"notificationSent" => "0",
								"month" => $holidayDate["month"],
								"day" => $holidayDate["day"]
							))->update();
						} else {
							$holiday->set("notificationSent", "0")->update();
						}
					}
				}
			}
		}
	}
	
	
	
	/**
	 * Holiday-specific methods
	 **/
	
	private function getHolidayDate($slug) {
		$date = array();
		switch ($slug) {
			case "christmas":
				$date["month"] = "12";
				$date["day"] = "25";
			break;
			case "kwanzaa":
				$date["month"] = "12";
				$date["day"] = "26";
			break;
			case "valentines_day":
				$date["month"] = "2";
				$date["day"] = "14";
			break;
			case "easter":
				$time = $this->getEasterSunday(date("Y"));
				$date["month"] = date("n", $time);
				$date["day"] = date("j", $time);
			break;
			case "mothers_day":
				$time = $this->getMothersDay(date("Y"));
				$date["month"] = date("n", $time);
				$date["day"] = date("j", $time);
			break;
			case "fathers_day":
				$time = $this->getFathersDay(date("Y"));
				$date["month"] = date("n", $time);
				$date["day"] = date("j", $time);
			break;
			case "veterans_day":
				$date["month"] = "11";
				$date["day"] = "11";
			break;
			case "grandparents_day":
				$time = $this->getGrandparentsDay(date("Y"));
				$date["month"] = date("n", $time);
				$date["day"] = date("j", $time);
			break;
			case "hanukkah":
				$date = $this->getHanukkah(date("Y"));
			break;
		}
		return $date;
	}
	
	private function getMothersDay($year) {
		
		// Generate the first day of May
		$first_day = mktime(0, 0, 0, 5, 1, $year);
	
		// Determine what day of the week the first falls on
		$day_of_week = date('D', $first_day);
	
		// Add the appropriate number of days to get to the second Sunday
		switch ($day_of_week) { 
			case "Sun": 
				$add = 7;
			break;
			case "Mon":
				$add = 13;
			break; 
			case "Tue":
				$add = 12;
			break; 
			case "Wed":
				$add = 11;
			break; 
			case "Thu":
				$add = 10;
			break; 
			case "Fri":
				$add = 9; 
			break; 
			case "Sat":
				$add = 8;
			break;
		}
		$day = 1 + $add;
		return strtotime("5/" . $day . "/" . $year);
	}
	
	private function getFathersDay($year) {
	
		// Generate the first day of June
		$first_day = mktime(0, 0, 0, 6, 1, $year);
	
		// Determine what day of the week the first falls on
		$day_of_week = date('D', $first_day);
	
		// Add the appropriate number of days to get to the second Sunday
		switch ($day_of_week) {
			case "Sun": 
				$add = 14;
			break;
			case "Mon":
				$add = 20;
			break; 
			case "Tue":
				$add = 19;
			break; 
			case "Wed":
				$add = 18;
			break; 
			case "Thu":
				$add = 17;
			break; 
			case "Fri":
				$add = 16;
			break; 
			case "Sat":
				$add = 15;
			break;
		}
		$day = 1 + $add;
		return strtotime("6/" . $day . "/" . $year);
	}
	
	private function getGrandparentsDay($year) {
		
		// Get Labor day
		$labor_day = $this->ordinalDay(1, "Mon", 9, $year);
		
		// Determine what day of the week Labor Day falls on
		$day_of_week = date('D', $labor_day);
	
		// Add the appropriate number of days to get to the second Sunday
		switch ($day_of_week) {
			case "Sun": 
				$add = 0;
			break;
			case "Mon":
				$add = 6;
			break; 
			case "Tue":
				$add = 5;
			break; 
			case "Wed":
				$add = 4;
			break; 
			case "Thu":
				$add = 3;
			break; 
			case "Fri":
				$add = 2; 
			break; 
			case "Sat":
				$add = 1;
			break;
		}
		$day = 1 + $add;
		return strtotime("9/" . $day . "/" . $year);
	}
	
	private function isLeapYear($nYEAR) {
		if ((($nYEAR % 4 == 0) && !($nYEAR % 100 == 0)) && ($nYEAR % 400 != 0)) {
			return true;
		}
		return false;
	}
	
	private function div($a, $b) { 
		return ($a - ($a % $b)) / $b;
	}
	
	private function getEasterSunday($nYEAR) {
		// The function is able to calculate the date of eastersunday back to the year 325, 
		// but mktime() starts at 1970-01-01! 
		if ($nYEAR < 1970) {
			$dtEasterSunday = mktime( 1, 1, 1, 1, 1, 1970 );
		} else {
		
			$nGZ = ($nYEAR % 19) + 1;
			$nJHD = $this->div($nYEAR, 100) + 1;
			$nKSJ = $this->div(3 * $nJHD, 4) - 12;
			$nKORR = $this->div(8 * $nJHD + 5, 25) - 5;
			$nSO = $this->div(5 * $nYEAR, 4) - $nKSJ - 10;
			$nEPAKTE = (( 11 * $nGZ + 20 + $nKORR - $nKSJ ) % 30);
			
			if (($nEPAKTE == 25 || $nGZ == 11) && $nEPAKTE == 24) {
				$nEPAKTE = $nEPAKTE + 1;
			}
			
			$nN = 44 - $nEPAKTE;
			
			if ($nN < 21) {
				$nN = $nN + 30; 
			}
			
			$nN = $nN + 7 - (($nSO + $nN) % 7 );
			$nN = $nN + $this->isLeapYear($nYEAR);
			$nN = $nN + 59;
			$nA = $this->isLeapYear($nYEAR);
			
			// Month
			$nNM = $nN;
			if ($nNM > (59 + $nA)) {
				$nNM = $nNM + 2 - $nA;
			}
			$nNM = $nNM + 91;
			$nMONTH = $this->div(20 * $nNM, 611) - 2;
			
			// Day 
			$nNT = $nN;
			$nNT = $nN;
			
			if ($nNT > (59 + $nA)) {
				$nNT = $nNT + 2 - $nA;
			}
			
			$nNT = $nNT + 91;
			$nM = $this->div(20 * $nNT, 611);
			$nDAY = $nNT - $this->div(611 * $nM, 20);
			
			$dtEasterSunday = mktime(0,0,0,$nMONTH,$nDAY,$nYEAR);
		
		}
	
		return $dtEasterSunday;
	}
	
	private function ordinalDay($ord, $day, $month, $year) {
	
		/**
		 * $ord – The number of days. Ex. “The fourth Thursday in November” (Thanksgiving)
		 * $day – The short abbreviation of the day of the week. Ex. “Thu”
		 * $month – The month in number form. Ex. “11″
		 * $year – The year in four-digit form. Ex. 2007
		 **/
	
		$targetDay = date("w", strtotime("next {$day}"));
		$earliestDate = 1 + 7 * ($ord - 1);
		$weekday = date("w", mktime(0, 0, 0, $month, $earliestDate, $year));
		
		if ($targetDay == $weekday) {
			$offset = 0;
		} else {
			if ($targetDay < $weekday) {
				$offset = $targetDay + (7 - $weekday);
			} else {
				$offset = ($targetDay + (7 - $weekday)) - 7;
			}
		}
		
		// Calculate the actual date of the holiday
		$holidayDate = mktime(0, 0, 0, $month, $earliestDate + $offset, $year);
		
		return $holidayDate;
	}
	
	private function getHanukkah($year) {
		$hanukkah = array();
		switch ($year) {
			case "2014":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "16";
			break;
			case "2015":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "6";
			break;
			case "2016":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "24";
			break;
			case "2017":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "12";
			break;
			case "2018":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "2";
			break;
			case "2019":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "22";
			break;
			case "2020":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "10";
			break;
			case "2021":
				$hanukkah["month"] = "11";
				$hanukkah["day"] = "28";
			break;
			case "2022":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "18";
			break;
			case "2023":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "7";
			break;
			case "2024":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "25";
			break;
			case "2025":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "14";
			break;
			case "2026":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "4";
			break;
			case "2027":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "24";
			break;
			case "2028":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "12";
			break;
			case "2029":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "2";
			break;
			case "2030":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "20";
			break;
			case "2031":
				$hanukkah["month"] = "12";
				$hanukkah["day"] = "10";
			break;
			case "2032":
				$hanukkah["month"] = "11";
				$hanukkah["day"] = "28";
			break;
		}
		return $hanukkah;
	}

}