<?php
if (!isset($they)) {
	$they = $me;
}
?>
<div id="profile-info">
	<a class="thumbnail" href="<?php echo $app->config("page", "profile", array("userId" => $they->get("userId"))); ?>">
		<img src="<?php echo $they->getThumbnail()->size("md")->get("src"); ?>" alt="<?php echo $they->firstNamePossessive(); ?> thumbnail">
	</a>
	<h1><a href="<?php echo $app->config("page", "profile", array("userId" => $they->get("userId"))); ?>"><?php echo $they->fullName(); ?></a></h1>
	<?php if (!$they->birthdayIsPrivate() || $me->isAlso($they)) : ?>
		<p class="text-muted text-birthday"><?php echo $they->formattedBirthday(); ?><?php echo ($they->birthdayIsPrivate()) ? " <em class=\"text-muted\">(private)</em>" : ""; ?></p>
	<?php endif; ?>
	
	<div class="control-panel">
		<?php if ($session->isLoggedIn()) : ?>
			<?php if (!$me->isAlso($they)) : ?>
				<?php if ($me->isFollowing($they)) : ?>
					<button class="btn btn-primary btn-data" type="button" data-src="<?php echo $app->config("ajax", "follow"); ?>" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" title="Click to follow <?php echo $they->get("firstName"); ?>">
						Unfollow
					</button>
				<?php else : ?>
					<button class="btn btn-primary btn-data" type="button" data-src="<?php echo $app->config("ajax", "follow"); ?>" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" title="Click to stop following <?php echo $they->get("firstName"); ?>">
						Follow
					</button>
				<?php endif; ?>
				<div class="btn-group">
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="sr-only">Toggle dropdown menu</span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="#">Send message</a></li>
						<li class="divider"></li>
						<?php if (!$they->isFollowing($me)) : ?>
							<li>
								<a href="<?php echo $app->config("ajax","follow-request"); ?>" class="btn-follow-request btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $they->get("userId"); ?>" data-leader-id="<?php echo $me->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">
									Ask <?php echo $they->get("firstName"); ?> to follow you
								</a>
							</li>
							<li class="divider"></li>
						<?php endif; ?>
						<li>
							<?php if ($me->hasBlocked($they)) : ?>
								<a href="<?php echo $app->config("ajax", "block-user"); ?>" class="btn-unblock-user btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-user-id="<?php echo $me->get("userId"); ?>" data-blocked-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">
									Unblock <?php echo $they->get("firstName"); ?>
								</a>
							<?php else: ?>
								<a href="<?php echo $app->config("ajax", "block-user"); ?>" class="btn-block-user btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-user-id="<?php echo $me->get("userId"); ?>" data-blocked-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">
									Block <?php echo $they->get("firstName"); ?>
								</a>
							<?php endif; ?>
						</li>
						<li><a href="<?php echo $app->config("page", "privacy-settings"); ?>">Blocked users...</a></li>
					</ul>
				</div>
			<?php else: ?>
				<a class="btn btn-primary" href="<?php echo $app->config("page", "edit-profile"); ?>">Edit Profile</a>
			<?php endif; ?>
		<?php else: ?>
			<a href="<?php echo $app->config("page", "login", array("redirect" => $app->currentUrl())); ?>" class="btn btn-primary">Log in to Follow</a>
		<?php endif; ?>
	</div>
	<nav>
		<?php $pageName = $page->getSlug(); ?>
		<ul class="nav nav-stacked">
			<li<?php echo ($pageName === "profile" || $pageName === "gifts") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "gifts", array("userId" => $they->get("userId"))); ?>">Gifts</a></li>
			<li<?php echo ($pageName === "wish-lists") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "wish-lists", array("userId" => $they->get("userId"))); ?>">Wish lists</a></li>
			<li<?php echo ($pageName === "following") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "following", array("userId" => $they->get("userId"))); ?>">Following</a></li>
			<li<?php echo ($pageName === "followers") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "followers", array("userId" => $they->get("userId"))); ?>">Followers</a></li>
			<?php if ($me->isAlso($they)) : ?>
				<li class="divider"></li>
				<li<?php echo ($pageName === "dibs") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "dibs"); ?>">My dibs</a></li>
				<li<?php echo ($pageName === "messages") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "messages"); ?>">Messages</a></li>
				<li<?php echo ($pageName === "notifications") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "notifications"); ?>">Notifications</a></li>
				<li class="divider"></li>
				<li<?php echo ($pageName === "settings") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "settings"); ?>">Settings</a></li>
				<li<?php echo ($pageName === "contact") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "contact"); ?>">Report a problem</a></li>
				<li><a href="<?php echo $app->config("page", "logout"); ?>">Log out</a></li>
			<?php endif; ?>
		</ul>
	</nav>
</div>