<?php 
$pageNumber = (isset($_GET["page"])) ? $_GET["page"] : 1;
$limit 	= 12;
$offset = ($pageNumber - 1) * $limit;
$suffix = " ORDER BY timestamp DESC";

$gift = new Gift($db);
$package = $gift->set(array(
	"userId" => $they->get("userId"),
	"isReceived" => "0",
	"follower" => $me
))->findPackage($limit, array("*"), $offset, $suffix);

// If no gifts found, double-check that it's not related to a non-existant page number
if (!$package || !isset($package["gifts"]) && $page > 1) {
	$package = $gift->findPackage($limit);
}
?>

<?php if ($package["gifts"]) : ?>
	<ul class="product-list">
		<?php foreach ($package["gifts"] as $gift) : ?>
			<li class="product-list-item">
				<div class="media">
					<a class="thumbnail" href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("lg")->get("src"); ?>" alt="Gift thumbnail"></a>
					<div class="media-body">
						<h3 class="media-heading">
							<a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>"><?php echo $gift->get("name"); ?></a>
						</h3>
						<div class="product-info">
							<?php echo $gift->priceHtml(); ?>
						</div>
					</div>
					<div class="media-footer">
						<div class="media">
							<a class="thumbnail"><img class="img-circle" src="<?php echo $they->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $they->firstNamePossessive(); ?> thumb"></a>
							<div class="media-body">
								<h4 class="media-heading"><a href="#"><?php echo $they->fullName(); ?></a></h4>
								<p><a href="#">Christmas 2014</a></p>
								<?php $priorityLabel = $gift->priorityLabel(); ?>
								<div class="priority" title="Priority: <?php echo $priorityLabel; ?>">
									<span class="sr-only">Priority: <?php echo $priorityLabel; ?></span>
									<?php echo $gift->priorityHtml(); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="control-panel">
						<?php if ($me->isAlso($they)) : ?>
							<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>Mark Received</button> 
							<a class="btn btn-default" href="<?php echo $app->config("page", "edit-gift", array("giftId" => $gift->get("giftId"))); ?>">Edit</a> 
							<a class="btn btn-default" href="#">Remove</a>
						<?php elseif ($me->isFollowing($they)) : ?>
							<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-tag"></span>Dib this</button> 
							<button class="btn btn-default" type="button">Add to...</button> 
						<?php elseif ($session->isLoggedIn()) : ?>
							<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-user"></span>Follow to dib this</button> 
							<button class="btn btn-default" type="button">Add to...</button> 
						<?php else : ?>
							<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-log-in"></span>Log in to dib this</button> 
						<?php endif; ?>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if ($package) : /* PAGINATION */ ?>
	<?php
	$currentPage = $package["data"]["page"];
	$totalPages = $package["data"]["totalPages"];
	?>
	<?php if ($totalPages > 1) : ?>
		<div class="section-pagination">
			<ol class="pagination">
				<?php for ($i=1, $length=$package["data"]["totalPages"]; $i<=$length; $i++) : ?>
					<?php 
					$class = "pagination-item";
					$class .= ($currentPage == $i) ? " active" : "";
					?>
					<?php if ($i == 1) : ?>
						<?php $prevPage = ($currentPage-1>0) ? $currentPage-1 : 1; ?>
						<li<?php echo ($currentPage == 1) ? " class=\"disabled\"" : ""; ?>><a href="<?php echo $app->config("page", "wish-list", array("wishListId"=>$wishList->get("wishListId"), "page"=>$prevPage, "filter"=>(isset($_GET["filter"])) ? $_GET["filter"] : "recent")); ?>">Prev</a></li>
					<?php endif; ?>
					<li class="<?php echo $class; ?>"><a href="<?php echo $app->config("page", "wish-list", array("wishListId"=>$wishList->get("wishListId"), "page"=>$i, "filter"=>(isset($_GET["filter"])) ? $_GET["filter"] : "recent")); ?>"><?php echo $i; ?></a></li>
					<?php if ($i == $totalPages) : ?>
						<?php $nextPage = ($currentPage+1<$totalPages+1) ? $currentPage+1 : $totalPages; ?>
						<li<?php echo ($currentPage == $totalPages) ? " class=\"disabled\"" : ""; ?>><a href="<?php echo $app->config("page", "wish-list", array("wishListId"=>$wishList->get("wishListId"), "page"=>$nextPage, "filter"=>(isset($_GET["filter"])) ? $_GET["filter"] : "recent")); ?>">Next</a></li>
					<?php endif; ?>
				<?php endfor; ?>
			</ol>
		</div>
	<?php endif; ?>
<?php endif; ?>