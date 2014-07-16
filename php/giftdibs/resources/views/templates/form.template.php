<?php include INCLUDE_PATH . "head.php"; ?>
<div class="sr-only">
	<ul>
		<li><a href="#content-primary">Skip to content</a></li>
	</ul>
</div>
<div class="page" id="template-form">
	<?php include INCLUDE_PATH . "header-forms.php"; ?>
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
				<div class="col-sm-12" id="content-primary" role="main">
					<?php
					if ($mainContent = $page->getContent("primary")) {
						foreach ($mainContent as $content) {
							include $content;
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php include INCLUDE_PATH . "footer-app.php"; ?>
</div>
<?php include INCLUDE_PATH . "foot.php"; ?>