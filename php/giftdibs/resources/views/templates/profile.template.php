<?php include INCLUDE_PATH . "head.php"; ?>
<div class="sr-only">
	<ul>
		<li><a href="#content-primary">Skip to content</a></li>
	</ul>
</div>
<div class="page" id="template-profile">
	<?php include INCLUDE_PATH . "header.php"; ?>
	<div id="content">
		<div class="container">
			<?php 
			if ($message = $session->getMessage()) {
				$page->addAnnouncement(array(
					"html" => $message,
					"type" => $session->getMessageType()
				)); 
			}
			if ($page->hasAnnouncements()) {
				$page->printAnnouncements();
			}
			?>
			<div id="content-primary" role="main">
				<?php include SNIPPET_PATH . "profile-sidebar.snippet.php"; ?>
				<div id="content-pane">
					<?php
					if ($mainContent = $page->getContent("primary")) {
						foreach ($mainContent as $content) {
							include $content;
						}
					}
					?>
				</div>
			</div>
			<aside id="content-secondary" role="complementary">
				<?php
				if ($mainContent = $page->getContent("secondary")) {
					foreach ($mainContent as $content) {
						include $content;
					}
				}
				?>
			</aside>
		</div>
	</div>
	<?php include INCLUDE_PATH . "footer.php"; ?>
</div>
<?php include INCLUDE_PATH . "foot.php"; ?>