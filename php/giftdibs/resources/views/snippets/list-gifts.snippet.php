<?php
// Set the page number
$pageNumber = (isset($_GET["page"])) ? $_GET["page"] : 1;
$limit = 20;
$offset = ($pageNumber - 1) * $limit;

// Set the options
$gift = new Gift($db);
$gift->set($packageOptions);
$gift->set("isReceived", 0);

// Set the query's suffix and filter
$suffix = " ORDER BY timestamp DESC";
if (isset($_GET["filter"])) {
	switch ($_GET["filter"]) {
		case "priority":
			$suffix = " ORDER BY priorityId DESC, timestamp DESC";
		break;
		case "received":
			$gift->set("isReceived", 1);
		break;
	}
}

// Retrieve the package
$package = $gift->findPackage($limit, array("giftId", "name", "priorityId", "imageId", "isReceived"), $offset, $suffix);

// If no gifts found, double-check that it's not related to a non-existant page number
if (!$package || !isset($package["gifts"]) && $page > 1) {
	$package = $gift->findPackage($limit, array("giftId", "name", "priorityId", "imageId", "isReceived"), null, $suffix);
}
?>

<?php if (!$package || !$package["gifts"]) : ?>
	<div class="alert alert-info">
		Gifts could not be found.
	</div>
<?php endif; ?>

<?php if ($package["gifts"]) : ?>
	<ol class="media-list product-list">
		<?php foreach ($package["gifts"] as $gift) : ?>
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
							<a href="<?php echo $app->config("ajax", "delete-gift"); ?>" class="btn-data" data-gift-id="<?php echo $gift->get("giftId"); ?>" data-signature="<?php echo $me->createSignature($gift->get("giftId")); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">Remove</a>
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
<?php endif; ?>

<?php /* PAGINATION */ ?>
<?php if ($package) : ?>
	<?php
	$currentPage = $package["data"]["page"];
	$totalPages = $package["data"]["totalPages"];
	if (isset($wishList)) {
		$pageAlias = "wish-list";
		$params = array(
			"wishListId" => $wishList->get("wishListId"), 
			"filter" => (isset($_GET["filter"])) ? $_GET["filter"] : "recent"
		);
	} else {
		$pageAlias = "gifts";
		$params = array(
			"userId" => $they->get("userId"), 
			"filter" => (isset($_GET["filter"])) ? $_GET["filter"] : "recent"
		);
	}
	?>
	<?php if ($totalPages > 1) : ?>
		<div class="section-pagination">
			<ol class="pagination">
				<?php for ($i = 1, $length = $package["data"]["totalPages"]; $i <= $length; $i++) : ?>
					<?php if ($i === 1) : ?>
						<?php $params["page"] = ($currentPage - 1 > 0) ? ($currentPage - 1) : 1; ?>
						<li<?php echo ($currentPage === 1) ? " class=\"disabled\"" : ""; ?>>
							<a href="<?php echo $app->config("page", $pageAlias, $params); ?>">Prev</a>
						</li>
					<?php endif; ?>
					<?php 
					/* Page items */
					$class = ($currentPage == $i) ? " active" : "";
					$params["page"] = $i; 
					?>
					<li class="pagination-item<?php echo $class; ?>">
						<a href="<?php echo $app->config("page", $pageAlias, $params); ?>"><?php echo $i; ?></a>
					</li>
					<?php if ($i == $totalPages) : ?>
						<?php $params["page"] = ($currentPage + 1 < $totalPages + 1) ? ($currentPage + 1) : $totalPages; ?>
						<li<?php echo ($currentPage == $totalPages) ? " class=\"disabled\"" : ""; ?>>
							<a href="<?php echo $app->config("page", $pageAlias, $params); ?>">Next</a>
						</li>
					<?php endif; ?>
				<?php endfor; ?>
			</ol>
		</div>
	<?php endif; ?>
<?php endif; ?>