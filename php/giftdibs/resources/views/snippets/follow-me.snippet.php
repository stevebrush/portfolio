<?php if ($me->isFollowing($they)) : ?>
	<button type="button" class="btn btn-default btn-unfollow" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" data-action="<?php echo $app->config('ajax','follow'); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Unfollow</button>
<?php else : ?>
	<button type="button" class="btn btn-default btn-follow" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" data-action="<?php echo $app->config('ajax','follow'); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Follow</button>
<?php endif; ?>