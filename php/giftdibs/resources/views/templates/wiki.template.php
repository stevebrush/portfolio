<?php include INCLUDE_PATH . "head.php"; ?>
<div class="sr-only">
	<ul>
		<li><a href="#content-primary">Skip to content</a></li>
	</ul>
</div>
<div class="page" id="template-wiki">
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
			<div class="row">
				<div class="col-sm-9 col-sm-push-3" id="content-primary" role="main">
					<?php
					if ($mainContent = $page->getContent("primary")) {
						foreach ($mainContent as $content) {
							include $content;
						}
					}
					?>
				</div>
				<aside class="col-sm-3 col-sm-pull-9" id="content-secondary" role="complimentary">
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
	</div>
	<?php include INCLUDE_PATH . "footer.php"; ?>
</div>
<?php include INCLUDE_PATH . "foot.php"; ?>