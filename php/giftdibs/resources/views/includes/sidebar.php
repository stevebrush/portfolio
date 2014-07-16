<?php 
if ($session->isLoggedIn()) {
	
	// Save profile URL so we don't have to access it a dozen times
	$myProfileUrl = $app->config("page", "profile");
	
	// Get number of messages
	$message_user = new Message_User($db);
	$messages = $message_user->set(array(
		"userId" => $session->getUserId(),
		"messageStatusId" => "2"
	))->find();
	$numMessages = ($messages) ? count($messages) : 0;
	
	// Get number of notifications
	$notification = new Notification($db);
	$notifications = $notification->set("userId", $session->getUserId())->find();
	$numNotifications = ($notifications) ? count($notifications) : 0;
	
	// Get number of dibs
	// Get all dibs for this user
	/*
	$dib = new Dib($db);
	$dibs = $dib->query("SELECT Gift.price FROM Dib, DibStatus, Gift WHERE Dib.userId = {$me->get('userId')} AND Dib.giftId = Gift.giftId AND Dib.dibStatusId = DibStatus.dibStatusId AND DibStatus.slug != 'delivered'");
	print_f($dibs);
	$numDibs = 0;
	$totalDibsPrice = 0;
	if ($dibs) {
		$numDibs = count($dibs);
		foreach ($dibs as $dib) {
			$totalDibsPrice += $dib->get("price");
		}
	}
	echo $app->formatPrice($totalDibsPrice);
	*/
}
?>
<aside id="sidebar" role="complimentary">
	<nav id="nav" role="navigation">
		<ul id="nav-global" class="nav nav-stacked">
			<li<?php echo ("home" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "home"); ?>">Newsfeed</a></li>
			<li<?php echo ("find-friends" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "find-friends"); ?>">Community</a></li>
			<li<?php echo ("shop" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "shop"); ?>">Shop</a></li>
		</ul>
		<?php if ($session->isLoggedIn()) : ?>
			<ul class="nav nav-stacked">
				<li<?php echo ("profile" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $myProfileUrl; ?>"><span class="glyphicon glyphicon-user"></span>Profile</a></li>
				<li<?php echo ("dibs" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "dibs"); ?>"><span class="glyphicon glyphicon-tags"></span>My Dibs (2) <span class="text-muted">&#8211;&nbsp;$350</span></a></li>
				<li<?php echo ("messages" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "messages"); ?>"><span class="glyphicon glyphicon-envelope"></span>Messages <?php echo ($numMessages > 0) ? "<span class=\"badge badge-danger pull-right\">{$numMessages}</span>" : ""; ?></a></li>
				<li<?php echo ("notifications" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "notifications"); ?>"><span class="glyphicon glyphicon-star"></span>Notifications <?php echo ($numNotifications > 0) ? "<span class=\"badge badge-danger pull-right\">{$numNotifications}</span>" : ""; ?></a></li>
				<li<?php echo ("settings" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "settings"); ?>"><span class="glyphicon glyphicon-cog"></span>Settings</a></li>
				<li<?php echo ("contact" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "contact"); ?>"><span class="glyphicon glyphicon-warning-sign"></span>Report a problem</a></li>
				<li><a href="<?php echo $app->config("page", "logout"); ?>"><span class="glyphicon glyphicon-off"></span>Log out</a></li>
			</ul>
		<?php else: ?>
			<ul class="nav nav-stacked">
				<li<?php echo ("signup" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "signup"); ?>">Sign up</a></li>
				<li<?php echo ("login" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "login"); ?>">Log in</a></li>
			</ul>
		<?php endif; ?>
	</nav>
</aside>