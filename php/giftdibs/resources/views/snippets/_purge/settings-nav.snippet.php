<?php $pageName = $page->getSlug(); ?>
<ul class="nav nav-tabs">
	<li<?php echo ($pageName == "edit-profile") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-profile'); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Profile</a></li>
	<li<?php echo ($pageName == "edit-account") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','account-details'); ?>"><small class="glyphicon glyphicon-cog"></small>&nbsp;&nbsp;Account</a></li>
	<li<?php echo ($pageName == "edit-email-preferences") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','email-preferences'); ?>"><small class="glyphicon glyphicon-envelope"></small>&nbsp;&nbsp;Email</a></li>
	<li<?php echo ($pageName == "edit-reminders") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-reminders'); ?>"><small class="glyphicon glyphicon-calendar"></small>&nbsp;&nbsp;Reminders</a></li>
	<li<?php echo ($pageName == "edit-holidays") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-holidays'); ?>"><small class="glyphicon glyphicon-tree-conifer"></small>&nbsp;&nbsp;Holidays</a></li>
	<li<?php echo ($pageName == "edit-shipping-address") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','shipping-address'); ?>"><small class="glyphicon glyphicon-plane"></small>&nbsp;&nbsp;Shipping address</a></li>
	<li<?php echo ($pageName == "edit-privacy") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','privacy-settings'); ?>"><small class="glyphicon glyphicon-eye-open"></small>&nbsp;&nbsp;Privacy</a></li>
	<li<?php echo ($pageName == "edit-gift-guide") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-gift-guide'); ?>"><small class="glyphicon glyphicon-book"></small>&nbsp;&nbsp;Gift guide</a></li>
</ul>