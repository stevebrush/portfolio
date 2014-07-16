<?php include INCLUDE_PATH."head.php"; ?>
<div id="form">
	<div id="content-outer">
		<div id="content-inner">
			<div id="main">
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
				<section class="section-main">
					<div class="container">
						<?php 
						if (!isEmpty($page->getContent(0))) {
							include $page->getContent(0); 
						}
						?>
					</div>
				</section>
			</div>
		</div>
		<?php include INCLUDE_PATH."footer.php"; ?>
	</div>
</div>
<?php include INCLUDE_PATH."foot.php"; ?>