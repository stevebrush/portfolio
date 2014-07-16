<footer id="footer">
	<div class="container">
		<nav>
			&copy; <?php echo date("Y"); ?> <?php echo $app->config("app", "name"); ?>
			<ul>
				<li><a href="<?php echo $app->config("page", "about"); ?>">About</a></li>
				<li><a href="<?php echo $app->config("page", "privacy"); ?>">Privacy Policy</a></li>
				<li><a href="#">Sitemap</a></li>
			</ul>
		</nav>
	</div>
</footer>