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
	<div id="search">
		<form class="quicksearch-form" role="search" action="<?php echo $app->config("ajax", "search"); ?>">
			<input type="hidden" name="doSearchUsers" value="true">
			<div class="form-group">
				<div class="input-group">
					<input class="form-control" type="text" placeholder="Find someone, gift idea..." role="search" maxlength="90">
					<span class="input-group-btn">
						<button class="btn btn-default btn-search" type="submit"><span class="glyphicon glyphicon-search"></span><span class="sr-only">Submit</span></button>
					</span>
				</div>
				<button class="btn btn-danger btn-cancel" type="button">&times;</button>
				<div class="list-group search-results"></div>
			</div>
		</form>
	</div>
	<?php if ($session->isLoggedIn()) : ?>
		<div class="media user-card">
			<div class="thumbnail pull-left">
				<a href="<?php echo $myProfileUrl; ?>"><img src="<?php echo $me->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $me->firstNamePossessive(); ?> thumbnail"></a>
			</div>
			<div class="media-body">
				<h2 class="media-heading">
					<a href="<?php echo $myProfileUrl; ?>"><?php echo $me->fullName(); ?></a>
				</h2>
				<div class="control-panel">
					<button class="btn btn-default control-switch active" type="button">
						<span class="glyphicon glyphicon-envelope"></span>
						<span class="badge badge-danger">2</span>
						<span class="sr-only">Messages</span>
					</button> 
					<button class="btn btn-default control-switch" type="button">
						<span class="glyphicon glyphicon-star"></span>
						<span class="sr-only">Notifications</span>
					</button>
				</div>
			</div>
		</div>
	<?php else: ?>
		<?php 
		$formOptions = array(
			"orientation" => "vertical",
			"heading" => ""
		);
		include FORM_PATH . "login.form.php"; 
		?>
	<?php endif; ?>
	<nav id="nav" role="navigation">
		<?php if ($session->isLoggedIn()) : ?>
			<ul class="nav nav-stacked">
				<li<?php echo ("profile" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $myProfileUrl; ?>"><span class="glyphicon glyphicon-user"></span>Profile</a></li>
				<li<?php echo ("dibs" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "dibs"); ?>"><span class="glyphicon glyphicon-tags"></span>My Dibs (2) <span class="text-muted">&#8211;&nbsp;$350</span></a></li>
				<li<?php echo ("messages" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "messages"); ?>"><span class="glyphicon glyphicon-envelope"></span>Messages <?php echo ($numMessages > 0) ? "<span class=\"badge badge-danger pull-right\">{$numMessages}</span>" : ""; ?></a></li>
				<li<?php echo ("notifications" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "notifications"); ?>"><span class="glyphicon glyphicon-star"></span>Notifications <?php echo ($numNotifications > 0) ? "<span class=\"badge badge-danger pull-right\">{$numNotifications}</span>" : ""; ?></a></li>
			</ul>
			<ul id="nav-global" class="nav nav-stacked">
				<li<?php echo ("home" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "home"); ?>"><span class="glyphicon glyphicon-globe"></span>Newsfeed</a></li>
				<li<?php echo ("find-friends" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "find-friends"); ?>"><span class="glyphicon glyphicon-comment"></span>Community</a></li>
				<li<?php echo ("shop" === $page->getSlug()) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "shop"); ?>"><span class="glyphicon glyphicon-shopping-cart"></span>Shop</a></li>
			</ul>
			<ul class="nav nav-stacked">
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