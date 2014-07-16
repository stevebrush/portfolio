<?php include INCLUDE_PATH . "head.php"; ?>
<div class="sr-only">
	<ul>
		<li><a href="#content-primary">Skip to content</a></li>
	</ul>
</div>
<div class="page" id="template-main">
	<?php include INCLUDE_PATH . "header.php"; ?>
	<div id="content">
		<div class="container">
			<div class="row">
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
				<?php if ($mainContent = $page->getContent("page-heading")) : ?>
					<div class="col-sm-12" id="page-heading">
						<?php
						foreach ($mainContent as $content) {
							include $content;
						}
						?>
					</div>
				<?php endif; ?>
				<div class="col-sm-9" id="content-primary" role="main">
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
				<aside class="col-sm-3" id="content-secondary" role="complementary">
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