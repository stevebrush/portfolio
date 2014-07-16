<?php
$wl = new WishList($db);
$wishLists = $wl->set(array(
	"userId" => $they->get("userId"),
	"follower" => $me
))->find(null, array("wishListId", "name"), 0, " ORDER BY timestamp DESC");
?>
<?php if ($me->isAlso($they)) : ?>
	<!--
	<a class="btn btn-primary" href="<?php echo $app->config('page','new-wish-list'); ?>"><small class="glyphicon glyphicon-plus"></small>New Wish List</a>
	-->
<?php endif; ?>	
<?php if ($wishLists) : ?>
	<?php foreach($wishLists as $wishList) : ?>
		<?php
		$gift = new Gift($db);
		$package = $gift->set(array(
			"wishListId" => $wishList->get("wishListId"),
			"isReceived" => 0
		))->findPackage(5, array("userId, giftId, wishListId, name, imageId, priorityId"), 0, " ORDER BY timestamp DESC");
		?>
		<?php if ($gifts = $package["gifts"]) : ?>
			<div class="panel panel-summary">
				<div class="panel-heading">
					<h2 class="panel-title">
						<?php echo $wishList->get("name"); ?>
					</h2>
					<a class="link-more" href="<?php echo $app->config("page", "wish-list", array("wishListId" => $wishList->get("wishListId"))); ?>">View all</a>
				</div>
				<div class="panel-body">
					<ol class="media-list product-list">
						<?php foreach ($gifts as $gift) : ?>
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
										<a class="text-muted" href="<?php echo $app->config("page", "profile", array("userId" => $they->get("userId"))); ?>"><?php echo $they->fullName(); ?></a> 
										<?php if ($me->isAlso($they)) : ?>
											<a href="<?php echo $app->config("page", "edit-gift", array("giftId" => $gift->get("giftId"))); ?>">Edit</a> 
											<a href="<?php echo $app->config("ajax", "delete-gift"); ?>" class="btn-data" data-gift-id="<?php echo $gift->get("giftId"); ?>" data-signature="<?php echo $me->createSignature($gift->get("giftId")); ?>">Remove</a>
										<?php endif; ?>
										<span class="priority" title="Priority: <?php echo $gift->priorityLabel(); ?>">
											<span class="sr-only">Priority: <?php echo $gift->priorityLabel(); ?></span>
											<?php echo $gift->priorityHtml(); ?>
										</span>
									</div>
									
									<div class="media-controls">
										<?php include SNIPPET_PATH . "gift-controls.snippet.php"; ?>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
<?php if (!$wishLists) : ?>
	<div class="alert alert-info">No wish lists found.</div>
<?php endif; ?>