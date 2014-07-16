<?php $pageName = $page->getSlug(); ?>
<ul class="nav nav-tabs">
	<li<?php echo ($pageName == "profile") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','profile', array('userId'=>$they->get("userId"))); ?>"><small class="glyphicon glyphicon-list-alt"></small>&nbsp;&nbsp;Wish lists</a></li>
	<li<?php echo ($pageName == "followers") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','followers', array('userId'=>$they->get("userId"))); ?>"><?php echo $they->numFollowers(); ?>&nbsp;Followers</a></li>
	<li<?php echo ($pageName == "following") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','following', array('userId'=>$they->get("userId"))); ?>"><?php echo $they->numFollowing(); ?>&nbsp;Following</a></li>
	<?php if ($me->isAlso($they)) : ?>
	<li<?php echo ($pageName == "dibs") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','dibs', array('userId'=>$they->get("userId"))); ?>"><small class="glyphicon glyphicon-tag"></small>&nbsp;&nbsp;Dibs</a></li>
	<li<?php echo ($pageName == "notifications") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','notifications'); ?>"><small class="glyphicon glyphicon-bullhorn"></small>&nbsp;&nbsp;Notifications</a></li>
	<li<?php echo ($pageName == "messages") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','messages'); ?>"><small class="glyphicon glyphicon-envelope"></small>&nbsp;&nbsp; Messages</a></li>
	<?php endif; ?>
</ul>