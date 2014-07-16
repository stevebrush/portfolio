<nav>
<h2>Settings</h2>
<?php $pageName = $page->getSlug(); ?>
<ul class="nav nav-stacked">
	<li<?php echo ($pageName == "edit-profile") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-profile'); ?>">Profile</a></li>
	<li<?php echo ($pageName == "edit-gift-guide") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-gift-guide'); ?>">Interests and Gift Ideas</a></li>
	<!-- li<?php /*echo ($pageName == "edit-shipping-address") ? ' class="active"' : "" ;*/ ?>><a href="<?php /* echo $app->config('page','shipping-address');*/ ?>">Shipping Address</a></li -->
	<li class="divider"></li>
	<li<?php echo ($pageName == "edit-account") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','account-details'); ?>">Account</a></li>
	<li<?php echo ($pageName == "edit-email-preferences") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','email-preferences'); ?>">Email Preferences</a></li>
	<li<?php echo ($pageName == "edit-reminders") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-reminders'); ?>">Email: Custom Reminders</a></li>
	<li<?php echo ($pageName == "edit-holidays") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','edit-holidays'); ?>">Email: Holiday Reminders</a></li>
	<li class="divider"></li>
	<li<?php echo ($pageName == "edit-privacy") ? ' class="active"' : "" ; ?>><a href="<?php echo $app->config('page','privacy-settings'); ?>">Blocked Users</a></li>
	<li<?php echo ($pageName == "delete-account") ? ' class="active"' : "" ; ?>><a class="text-danger" href="<?php echo $app->config('page','delete-account'); ?>">Delete Account</a></li>
</ul>