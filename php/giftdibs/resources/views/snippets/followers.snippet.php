<?php 
if ($page->getSlug() == "followers") {
	$followers = $they->getFollowers(); 
} else {
	$followers = $they->getLeaders(); 
}
?>
<?php if ($followers || ($me->isAlso($they))) : ?>
	<?php if ($me->isAlso($they)) : ?>
		<!--
		<a href="<?php echo $app->config("page", "join-me", array("leaderId" => $me->get("userId"))); ?>" class="btn btn-default btn-facebook btn-facebook-invite">Invite a Friend</a>
		-->
	<?php endif; ?>
	<?php if ($followers) : ?>
		<ul class="media-list user-list">
			<?php foreach ($followers as $follower) : ?>
				<li class="media">
					<a class="thumbnail" href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>">
						<img class="img-circle" src="<?php echo $follower->getThumbnail()->size("md")->get("src"); ?>" alt="<?php echo $follower->firstNamePossessive(); ?> thumbnail">
					</a>
					<div class="media-body">
						<h3 class="media-heading"><a href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>"><?php echo $follower->fullName(); ?><?php echo ($me->isAlso($follower)) ? " (me)" : ""; ?></a></h3>
						<div class="media-controls">
							<?php if ($session->isLoggedIn()) : ?>
								<?php if (!$me->isAlso($follower)) : ?>
									<?php if ($me->isFollowing($follower)) : ?>
										<a href="<?php echo $app->config("ajax", "follow"); ?>" class="btn btn-primary btn-sm btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($follower->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $follower->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">Unfollow</a>
									<?php else : ?>
										<a href="<?php echo $app->config("ajax", "follow"); ?>" class="btn btn-primary btn-sm btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($follower->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $follower->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">Follow</a>
									<?php endif; ?>
								<?php endif; ?>
							<?php else : ?>
								<a href="<?php echo $app->config("page", "login", array("redirect" => $app->currentUrl())); ?>" class="btn btn-default btn-block">Log in to follow</a>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
<?php else : ?>
	<?php if ($page->getSlug() === "followers") : ?>
		<div class="alert alert-info">
			<p><?php echo $they->get("firstName"); ?> has no followers.</p>
			<?php if (!$me->isAlso($they)) : ?>
				<?php if ($session->isLoggedIn()) : ?>
					<p><a href="#" class="btn btn-default btn-follow" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" data-action="<?php echo $app->config("ajax","follow"); ?>">Follow <?php echo $they->get("firstName"); ?></a></p>
				<?php else : ?>
					<p><a href="<?php echo $app->config("page", "login", array("redirect" => urlencode($app->currentUrl()))); ?>" class="btn btn-default">Log in to follow <?php echo $they->get("firstName"); ?></a></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<div class="alert alert-info">
			<p><?php echo $they->get("firstName"); ?> isn't following anyone.</p> 
			<?php if ($session->isLoggedIn() && !$me->isAlso($they)) : ?>
				<p><a href="#" class="btn btn-default btn-ask-to-be-followed" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $they->get("userId"); ?>" data-leader-id="<?php echo $me->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" data-action="<?php echo $app->config("ajax","ask-to-follow"); ?>">Ask <?php echo $they->get("firstName"); ?> to follow you</a></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>