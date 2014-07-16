<div class="panel panel-default panel-page-title">
	<div class="panel-body">
		<h1><a href="<?php echo $app->config("page", "profile"); ?>"><?php echo $me->get("firstName"); ?></a> / Settings</h1>
	</div>
	<div class="panel-nav">
		<?php $pageName = $page->getSlug(); ?>
		<ul class="nav nav-tabs">
			<li<?php echo ($pageName == "edit-profile") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-profile'); ?>"><small class="glyphicon glyphicon-user"></small>Profile</a></li>
			<li<?php echo ($pageName == "edit-account") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','account-details'); ?>"><small class="glyphicon glyphicon-cog"></small>Account</a></li>
			<li<?php echo ($pageName == "edit-email-preferences") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','email-preferences'); ?>"><small class="glyphicon glyphicon-envelope"></small>Email</a></li>
			<li<?php echo ($pageName == "edit-reminders") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-reminders'); ?>"><small class="glyphicon glyphicon-calendar"></small>Reminders</a></li>
			<li<?php echo ($pageName == "edit-holidays") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-holidays'); ?>"><small class="glyphicon glyphicon-tree-conifer"></small>Holidays</a></li>
			<li<?php echo ($pageName == "edit-shipping-address") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','shipping-address'); ?>"><small class="glyphicon glyphicon-plane"></small>Shipping Address</a></li>
			<li<?php echo ($pageName == "edit-privacy") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','privacy-settings'); ?>"><small class="glyphicon glyphicon-eye-open"></small>Blocked Users</a></li>
			<li<?php echo ($pageName == "edit-gift-guide") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-gift-guide'); ?>"><small class="glyphicon glyphicon-book"></small>Gift Guide</a></li>
		</ul>
	</div>
</div>