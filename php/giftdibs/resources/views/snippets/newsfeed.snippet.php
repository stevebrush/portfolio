<?php
/* Send email reminders */
$reminder = new Reminder($app);
$reminder->check();
?>
<h1>Recent Gifts</h1>
<?php
$gift = new Gift($db);
$sql = "SELECT Gift.giftId, Gift.imageId, Gift.name, Gift.priorityId, Gift.userId, Gift.isReceived, User.firstName, User.lastName FROM Gift, User, Follow WHERE Follow.userId = {$me->get('userId')} AND (Follow.leaderId = User.userId OR Follow.userId = User.userId) AND User.userId = Gift.userId GROUP BY Gift.giftId ORDER BY Gift.timestamp DESC LIMIT 25";
$gifts = $gift->query($sql);
?>
<?php if ($gifts) : ?>
	<ol class="media-list product-list">
		<?php foreach ($gifts as $gift) : ?>
			<?php if ($gift->userCanView($me)) : ?>
				<li class="media">
					<a class="thumbnail" href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>">
						<img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>" alt="Gift thumbnail">
					</a>
					<div class="media-body">
						<h4 class="media-heading">
							<a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>">
								<?php echo $gift->get("name"); ?>
							</a>
						</h4>
						<a class="product-info on-sale" href="#">
							<span class="text-price">
								<del class="text-muted">$45.99</del> <span class="text-danger">$39.88</span>
							</span>
							<span class="text-muted text-vendor">Walmart.com</span>
						</a>
						
						<div class="user-info">
							<a class="text-muted" href="<?php echo $app->config("page", "profile", array("userId" => $gift->get("userId"))); ?>"><?php echo $gift->get("firstName") . " " . $gift->get("lastName"); ?></a> 
							<a href="#">Edit</a> 
							<a href="#">Remove</a>
							<span class="priority" title="Priority: <?php echo $gift->priorityLabel(); ?>">
								<span class="sr-only">Priority: <?php echo $gift->priorityLabel(); ?></span>
								<?php echo $gift->priorityHtml(); ?>
							</span>
						</div>
						
						<div class="media-controls">
							<?php //include SNIPPET_PATH . "gift-controls.snippet.php"; ?>
						</div>
					</div>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>