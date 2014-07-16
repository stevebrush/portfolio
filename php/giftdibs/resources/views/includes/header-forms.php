<header class="navbar navbar-inverse navbar-static-top" id="header" role="banner">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?php echo $app->config("page", "home"); ?>">
				<img src="<?php echo IMG_URL; ?>logo.png" alt="<?php echo $app->config("app", "name"); ?> logo">
				<span class="sr-only"><?php echo $app->config("app", "name"); ?></span>
			</a>
		</div>
		<nav class="navbar-right" id="utility">
			<button type="button" class="btn btn-default btn-go-back">Cancel</button>
		</nav>
	</div>
</header>