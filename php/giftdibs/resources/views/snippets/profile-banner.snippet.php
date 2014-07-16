<div class="panel panel-default panel-page-title panel-profile-heading">
	<div class="panel-body">
		<div class="media">
			<a class="thumbnail" href="<?php echo $app->config("page", "profile", array("userId" => $they->get("userId"))); ?>">
				<img src="<?php echo $they->getThumbnail()->size("md")->get("src"); ?>" alt="<?php echo $they->firstNamePossessive(); ?> thumbnail">
			</a>
			<div class="media-body">
				<h2 class="media-heading"><?php echo $they->fullName(); ?></h2>
				<div class="control-panel">
					<?php if ($session->isLoggedIn()) : ?>
						<?php if (!$me->isAlso($they)) : ?>
							<div class="btn-group">
								<?php if ($me->isFollowing($they)) : ?>
									<button class="btn btn-primary btn-data" type="button" data-src="<?php echo $app->config("ajax", "follow"); ?>" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" title="Click to follow <?php echo $they->get("firstName"); ?>">
										Unfollow
									</button>
								<?php else : ?>
									<button class="btn btn-primary btn-data" type="button" data-src="<?php echo $app->config("ajax", "follow"); ?>" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" title="Click to stop following <?php echo $they->get("firstName"); ?>">
										Follow
									</button>
								<?php endif; ?>
								<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
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
							<a class="btn btn-primary" href="<?php echo $app->config("page", "edit-profile"); ?>">Preferences</a>
						<?php endif; ?>
					<?php else: ?>
						<a href="<?php echo $app->config("page", "login", array("redirect" => $app->currentUrl())); ?>" class="btn btn-primary">Log in to Follow</a>
					<?php endif; ?>
				</div>
				<!--
				<div class="definition-group">
					<?php if (!$they->birthdayIsPrivate() || $me->isAlso($they)) : ?>
						<dl class="definition">
							<dt class="definition-term">Birthday:</dt>
							<dd class="definition-value"><?php echo $they->formattedBirthday(); ?><?php echo ($they->birthdayIsPrivate()) ? " <em class=\"text-muted\">(private)</em>" : ""; ?></dd>
						</dl>
					<?php endif; ?>
				</div>
				-->
			</div>
		</div>
	</div>
	<div class="panel-nav">
		<ul class="nav nav-tabs">
			<?php $pageName = $page->getSlug(); ?>
			<li<?php echo ($pageName == "profile") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "profile", array("userId" => $they->get("userId"))); ?>">Wish Lists</a></li>
			<li<?php echo ($pageName == "most-wanted") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "most-wanted", array("userId" => $they->get("userId"))); ?>">Most Wanted</a></li>
			<li<?php echo ($pageName == "following") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "following", array("userId" => $they->get("userId"))); ?>">Following</a></li>
			<li<?php echo ($pageName == "followers") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "followers", array("userId" => $they->get("userId"))); ?>">Followers</a></li>
			<li<?php echo ($pageName == "profile-about") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config("page", "profile-about", array("userId" => $they->get("userId"))); ?>">About</a></li>
		</ul>
	</div>
</div>