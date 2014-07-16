<header class="navbar navbar-inverse navbar-static-top" id="header" role="banner">
	<div class="container">
		<a class="navbar-brand" href="<?php echo $app->config("page", "home"); ?>">
			<img src="<?php echo IMG_URL; ?>logo.png" alt="<?php echo $app->config("app", "name"); ?> logo">
			<span class="sr-only"><?php echo $app->config("app", "name"); ?></span>
		</a>
		<?php include FORM_PATH . "search.form.php"; ?>
		<ul class="nav navbar-nav">
			<li><a href="<?php echo $app->config("page", "home"); ?>">Home</a></li>
			<li><a href="<?php echo $app->config("page", "find-friends"); ?>">People</a></li>
		</ul>
		<nav class="navbar-right" id="utility">
			<?php if ($session->isLoggedIn()) : ?>
				<?php
				// Get number of messages
				$message_user = new Message_User($db);
				$messages = $message_user->set(array(
					"userId" => $session->getUserId(),
					"messageStatusId" => "2" 
				))->find(null, array("messageId"));
				$numMessages = ($messages) ? count($messages) : 0;
				
				// Get number of notifications
				$notification = new Notification($db);
				$notifications = $notification->set("userId", $session->getUserId())->find(null, array("notificationId"));
				$numNotifications = ($notifications) ? count($notifications) : 0;
				
				$numTotalAlerts = $numMessages + $numNotifications;
				?>
				<div class="btn-group">
					<a class="btn btn-default" href="<?php echo $app->config("page", "profile"); ?>">
						<?php echo $me->fullName(); ?>
					</a>
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
						<?php if ($numTotalAlerts > 0) : ?>
							<span class="badge badge-danger"><?php echo $numTotalAlerts; ?></span>
						<?php endif; ?>
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu">
						<?php $slug = $page->getSlug(); ?>
						<li<?php echo ("profile" === $slug) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "profile"); ?>">Profile</a></li>
						<li<?php echo ("dibs" === $slug) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "dibs"); ?>">My Dibs (2) <span class="text-muted">&#8211;&nbsp;$350</span></a></li>
						<li role="presentation" class="divider"></li>
						<li<?php echo ("messages" === $slug) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "messages"); ?>">Messages <?php echo ($numMessages > 0) ? "<span class=\"badge badge-danger\">{$numMessages}</span>" : ""; ?></a></li>
						<li<?php echo ("notifications" === $slug) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "notifications"); ?>">Notifications <?php echo ($numNotifications > 0) ? "<span class=\"badge badge-danger\">{$numNotifications}</span>" : ""; ?></a></li>
						<li role="presentation" class="divider"></li>
						<li<?php echo ("settings" === $slug) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "settings"); ?>">Settings</a></li>
						<li<?php echo ("contact" === $slug) ? " class=\"active\"" : ""; ?>><a href="<?php echo $app->config("page", "contact"); ?>">Report a problem</a></li>
						<li><a href="<?php echo $app->config("page", "logout"); ?>">Log out</a></li>
					</ul>
				</div>
				<a class="btn btn-primary" href="<?php echo $app->config("page", "new-gift"); ?>">New Gift</a>
			<?php else: ?>
				<a class="btn btn-default" href="<?php echo $app->config("page", "signup"); ?>">Sign Up</a>
				<a class="btn btn-default" href="<?php echo $app->config("page", "login"); ?>">Log In</a>
			<?php endif; ?>
		</nav>
	</div>
</header>