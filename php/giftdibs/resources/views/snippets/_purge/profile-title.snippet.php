<div class="media">
	<div class="thumbnail pull-left"><img src="<?php echo $they->getThumbnail()->size("md")->get("src"); ?>"></div>
	<div class="media-body">
		<h1><?php echo $they->fullName(); ?></h1>
		<?php if (($me->isFollowing($they) && !$they->birthdayIsPrivate()) || ($me->isAlso($they))) : ?>
			<p><span class="text-muted">Birthday</span>&nbsp; <?php echo $they->formattedBirthday(); ?><?php echo ($they->birthdayIsPrivate()) ? " <em class=\"text-muted\">(private)</em>" : ""; ?></p>
		<?php endif; ?>
		<div class="controls">
			<?php if ($me->isAlso($they)) : ?>
				<a href="<?php echo $app->config("page", "edit-profile"); ?>" class="btn btn-default"><small class="glyphicon glyphicon-cog"></small>&nbsp;&nbsp;Settings</a>
			<?php endif; ?>
			<?php if ($me->isFollowing($they) || $me->isAlso($they)) : ?>
				<div class="btn-group">
					<?php $btnLabel = (!$me->isAlso($they)) ? "{$they->firstNamePossessive()} gift guide" : "My gift guide"; ?>
					<a href="#gift-guide-modal" data-toggle="modal" class="btn btn-default"><small class="glyphicon glyphicon-book"></small>&nbsp;&nbsp;<?php echo $btnLabel; ?></a>
					<?php if ($me->isAlso($they)) : ?>
						<a href="<?php echo $app->config("page", "edit-gift-guide"); ?>" class="btn btn-default"><small class="glyphicon glyphicon-pencil"></small><span class="sr-only">Edit gift guide</span></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ($session->isLoggedIn()) : ?>
				<?php if (!$me->isAlso($they)) : ?>
					<div class="btn-group">
						<?php if ($me->isFollowing($they)) : ?>
							<a href="<?php echo $app->config("ajax", "follow"); ?>" class="btn btn-default btn-unfollow btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Unfollow</a>
						<?php else : ?>
							<a href="<?php echo $app->config("ajax", "follow"); ?>" class="btn btn-default btn-follow btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Follow</a>
						<?php endif; ?>
						<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></a>
						<ul class="dropdown-menu" role="menu">
							<?php if (!$they->isFollowing($me)) : ?>
								<li><a href="<?php echo $app->config("ajax","follow-request"); ?>" class="btn-follow-request btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $they->get("userId"); ?>" data-leader-id="<?php echo $me->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-comment"></small>&nbsp;&nbsp;Ask <?php echo $they->get("firstName"); ?> to follow you</a></li>
							<?php endif; ?>
							<li>
								<?php if ($me->hasBlocked($they)) : ?>
									<a href="<?php echo $app->config("ajax","block-user"); ?>" class="btn-unblock-user btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-user-id="<?php echo $me->get("userId"); ?>" data-blocked-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-ok-circle"></small>&nbsp;&nbsp;Unblock <?php echo $they->get("firstName"); ?></a>
								<?php else : ?>
									<a href="<?php echo $app->config("ajax","block-user"); ?>" class="btn-block-user btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-user-id="<?php echo $me->get("userId"); ?>" data-blocked-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-remove-circle"></small>&nbsp;&nbsp;Block <?php echo $they->get("firstName"); ?></a>
								<?php endif; ?>
							</li>
							<li class="divider"></li>
							<li><a href="<?php echo $app->config("page", "privacy-settings"); ?>">Blocked users...</a></li>
						</ul>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<a href="<?php echo $app->config("page", "login", array("redirect" => $app->currentUrl())); ?>" class="btn btn-default"><small class="glyphicon glyphicon-log-in"></small>&nbsp;&nbsp;Log in to follow</a>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php if ($me->isFollowing($they) || $me->isAlso($they)) : ?>
	<div class="modal" id="gift-guide-modal">
		<?php include MODAL_PATH."gift-guide.modal.php"; ?>
	</div>
<?php endif; ?>