<h1>Find Friends</h1>
<?php if ($session->isLoggedIn()) : ?>
	<p>
		<a href="<?php echo $app->config("page", "join-me", array("leaderId" => $me->get("userId"))); ?>" class="btn btn-default btn-facebook btn-facebook-invite"><small class="glyphicon glyphicon-plus"></small>&nbsp;&nbsp;Invite a Friend</a>
	</p>
<?php endif; ?>
<ul class="media-list user-list">
	<?php
	$user = new User($db);
	$users = $user->find(null, array("userId", "firstName", "lastName", "imageId"));
	?>
	<?php foreach ($users as $follower) : ?>
		<li class="media">
			<a class="thumbnail" href="<?php echo $app->config("page", "profile", array( "userId" => $follower->get("userId") )); ?>">
				<img class="img-circle" src="<?php echo $follower->getThumbnail()->size("md")->get("src"); ?>" alt="<?php echo $follower->firstNamePossessive(); ?> thumbnail">
			</a>
			<div class="media-body">
				<h4 class="media-heading">
					<a href="<?php echo $app->config("page", "profile", array( "userId" => $follower->get("userId") )); ?>"><?php echo $follower->fullName(); ?></a>
				</h4>
				<div class="media-controls">
					<?php if ($session->isLoggedIn() && !$me->isAlso($follower)) : ?>
						<?php if ($me->isFollowing($follower)) : ?>
							<a href="<?php echo $app->config('ajax','follow'); ?>" class="btn btn-default btn-unfollow btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($follower->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $follower->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Unfollow</a>
						<?php else : ?>
							<a href="<?php echo $app->config('ajax','follow'); ?>" class="btn btn-default btn-follow btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($follower->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $follower->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Follow</a>
						<?php endif; ?>
					<?php elseif (!$session->isLoggedIn()) : ?>
						<a href="<?php echo $app->config('page','login',array('redirect'=>$app->currentUrl())); ?>" class="btn btn-default"><small class="glyphicon glyphicon-log-in"></small>&nbsp;&nbsp;Log in to follow</a>
					<?php endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
<!--
<ul class="media-list" ng-controller="usersController" ng-cloak>
	<li class="media" ng-repeat="user in users">
		<a class="thumbnail" href="{{ user.url.profile }}"><img class="img-circle" ng-src="{{user.thumbnail.sm.src}}"></a>
		<div class="media-body">
			<h3 class="media-heading"><a href="{{ user.url.profile }}">{{ user.firstName + " " + user.lastName }}</a></h3>
			<button gd-button data-action="{{ user.interactions.button.action }}" data-label="{{ user.interactions.button.label }}"></button>
		</div>
	</li>
</ul>
-->