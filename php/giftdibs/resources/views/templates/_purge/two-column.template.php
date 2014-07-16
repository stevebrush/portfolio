<?php include INCLUDE_PATH."head.php"; ?>
<div id="page" class="offcanvas">
	<div id="content-outer">
		<?php include INCLUDE_PATH."header.php"; ?>
		<div id="content-inner">
			<?php include INCLUDE_PATH."sidebar.php"; ?>
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
				<div class="panel-page-title">
					<div class="container">
						<?php if ($page->getContent(1)) include $page->getContent(1); ?>
					</div>
				</div>
				<div class="section-main">
				<div class="container">
					<div class="row">
						<div id="content-secondary" class="col-sm-3">
						<?php if ($page->getContent(2)) include $page->getContent(2); ?>
					</div>
						<div id="content-primary" class="col-sm-9">
							<?php if ($page->getContent(0)) include $page->getContent(0); ?>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
		<?php include INCLUDE_PATH."footer.php"; ?>
	</div>
</div>
<?php include INCLUDE_PATH."foot.php"; ?>