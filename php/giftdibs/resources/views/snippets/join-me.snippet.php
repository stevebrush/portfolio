<div class="jumbotron">
	<div class="media">
		<div class="thumbnail pull-left" style="width:150px;height:150px;"><a href="<?php echo $app->config('page','profile',array('userId'=>$they->get("userId"))); ?>"><img src="<?php echo $they->getThumbnail()->size('lg')->get("src"); ?>" alt=""></a></div>
		<div class="media-body">
			<h1>Join <?php echo $they->fullName(); ?> on <?php echo $app->config("app", "name"); ?></h1>
		</div>
	</div>
	<?php if (!$session->isLoggedIn()) : ?>
		<ul>
			<li>Create lists and share them with your family and friends</li>
			<li>Surprise your friends with something they really want</li>
			<li>Get better gifts for the people you care about</li>
			<li>Receive email notifications for upcoming holidays and birthdays</li>
		</ul>
		<p>
			<a href="#" class="btn btn-facebook btn-lg btn-facebook-signup" data-loading-text="Processing...">One-click Facebook Registration</a>
		</p>
		<p>
			<a href="<?php echo $app->config('page','signup',array('leaderId'=>$they->get("userId"))); ?>">Or, Register with your Email Address</a>
		</p>
	<?php else: ?>
		<p><a href="<?php echo $app->config('page','profile',array('userId'=>$they->get("userId"))); ?>"><?php echo $they->fullName(); ?></a> wants you to be a follower!</p>
		<?php if ($me->isFollowing($they)) : ?>
			<a href="<?php echo $app->config("ajax", "follow"); ?>" class="btn btn-default btn-unfollow btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Unfollow</a>
		<?php else : ?>
			<a href="<?php echo $app->config("ajax", "follow"); ?>" class="btn btn-default btn-follow btn-data" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Follow</a>
		<?php endif; ?>
	<?php endif; ?>
	<?php
	$gift = new Gift($db);
	$gifts = $gift->set("userId", $they->get("userId"))->find(10);
	?>
	<?php if ($gifts) : ?>
		<?php foreach ($gifts["gifts"] as $gift) : ?>
			<?php if ($gift->userCanView($me)) : ?>
				<div class="thumbnail" style="width:80px;height:80px;">
					<img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>">
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>