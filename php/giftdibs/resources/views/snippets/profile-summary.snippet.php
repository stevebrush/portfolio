<?php 
// Get field labels for profile information
$they->getInputs();
?>

<?php if ($me->isAlso($they)) : ?>
	<?php
	$sql = "SELECT * FROM Gift, Dib, DibStatus WHERE Dib.userId = {$me->get('userId')} AND Dib.dibStatusId = DibStatus.dibStatusId AND DibStatus.slug != 'delivered' AND Dib.giftId = Gift.giftId ORDER BY Dib.dateProjected LIMIT 3";
	$gift = new Gift($db);
	$gifts = $gift->query($sql);
	print_f($gifts);
	/*
	$dibStatus = new DibStatus($db);
	$dibStatus = $dibStatus->set(array(
		"slug" => ""
	))->find(1);
	$dib = new Dib($db);
	$dibs = $dib->set(array(
		"userId" => $me->get("userId")
	))->find(3, array("dibId","dibStatusId"), null, " ORDER BY dateProjected DESC");
	print_f($dibs);
	*/
	?>
	<?php if ($gifts) : ?>
		<div class="panel panel-summary">
			<div class="panel-heading">
				<h4 class="panel-title">5 dibs</h4>
				<a class="link-more" href="<?php echo $app->config("page", "dibs"); ?>">See all</a>
			</div>
			<div class="panel-body">
				<ol class="media-list media-list-sm">
					<li class="media">
						<a class="thumbnail" href="#"></a>
						<div class="media-body">
							<div class="media-controls">
								<button class="btn btn-warning" type="button">$45</button>
							</div>
							<h4 class="media-heading">
								<a href="#">Polka dotted sun dress</a>
							</h4>
							<a class="text-muted" href="#">Jaci Brush</a>
						</div>
					</li>
				</ol>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>



<?php
/* WISHLISTS */
$wishList = new WishList($db);
$wishLists = $wishList->set(array(
	"userId" => $they->get("userId"),
	"follower" => $me
))->find(null, array("wishListId", "name", "timestamp"), null, " ORDER BY timestamp DESC");
$numWishLists = count($wishLists);
?>
<div class="panel panel-summary">
	<div class="panel-heading">
		<h4 class="panel-title"><?php echo ($numWishLists === 1) ? "1 wish list" : $numWishLists . " wish lists"; ?></h4>
		<?php if ($wishLists) : ?>
			<a class="link-more" href="<?php echo $app->config("page", "wish-lists", array("userId" => $they->get("userId"))); ?>">See all</a>
		<?php endif; ?>
	</div>
	<div class="panel-body">
		<?php if ($wishLists) : ?>
			<?php 
			$max = 2;
			$counter = 0;
			?>
			<?php foreach ($wishLists as $wishList) : ?>
				<?php
				$gift = new Gift($db);
				$package = $gift->set(array(
					"wishListId" => $wishList->get("wishListId"),
					"follower" => $me,
					"isReceived" => "0"
				))->findPackage(6, array("giftId", "imageId", "name"), null, " ORDER BY timestamp DESC");
				?>
				<?php if ($package) : ?>
					<h5><a href="<?php echo $app->config("page", "wish-list", array("wishListId" => $wishList->get("wishListId"))); ?>"><?php echo $wishList->get("name"); ?></a></h5>
					<ol class="thumbnail-list thumbnail-list-sm">
						<?php foreach ($package["gifts"] as $gift) : ?>
							<li>
								<a class="thumbnail" href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>" title="<?php echo $gift->get("name"); ?>">
									<img src="<?php echo $gift->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $gift->get("name"); ?>">
								</a>
							</li>
						<?php endforeach; ?>
					</ol>
					<?php
					if ($counter++ >= $max) {
						break;
					}
					?>
				<?php else : ?>
					<?php $counter++; ?>
					<?php if ($counter >= $max || $counter === $numWishLists) : ?>
						<p class="alert alert-info">No gifts found in wish lists.</p>
						<?php break; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else: ?>
			No wish lists found. 
			<?php if ($me->isAlso($they)) : ?>
				<a href="<?php echo $app->config("page", "new-wish-list"); ?>">Create one now&nbsp;&rarr;</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>

<?php
$followers = $they->getLeaders();
?>
<div class="panel panel-summary">
	<div class="panel-heading">
		<h4 class="panel-title"><?php echo $they->numFollowing(); ?> following</h4>
		<a class="link-more" href="#">See all</a>
	</div>
	<div class="panel-body">
		<?php if ($followers || ($me->isAlso($they))) : ?>
		<ol class="media-list media-list-sm user-list">
			<?php foreach ($followers as $follower) : ?>
				<li class="media">
					<a class="thumbnail" href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>">
						<img class="img-circle" src="<?php echo $follower->getThumbnail()->size("sm")->get("src"); ?>" alt="">
					</a>
					<div class="media-body">
						<h4 class="media-heading">
							<a href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>"><?php echo $follower->fullName(); ?></a>
						</h4>
						<a class="text-muted" href="<?php echo $app->config("page", "gifts", array("userId" => $follower->get("userId"))); ?>"><?php echo $follower->numGifts(); ?> gifts</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php else: ?>
			<div class="alert alert-info">
				<?php echo $they->get("firstName"); ?> isn't following anyone.
			</div>
		<?php endif; ?>
	</div>
</div>


<div class="panel panel-summary">
	<div class="panel-heading">
		<h4 class="panel-title">Information</h4>
	</div>
	<div class="panel-body">
		<dl class="definition-list">
			<?php if ($me->isFollowing($they)) : ?>
				<?php if (!$they->birthdayIsPrivate() || $me->isAlso($they)) : ?>
					<dt>Birthday:</dt>
					<dd><?php echo $they->formattedBirthday(); ?></dd>
				<?php endif; ?>
				<?php if ($interests = $they->get("interests")) : ?>
					<dt><?php echo $they->getField("interests")["label"]; ?></dt>
					<dd><?php echo $interests; ?></dd>
				<?php endif; ?>
				<?php if ($favoriteStores = $they->get("favoriteStores")) : ?>
					<dt><?php echo $they->getField("favoriteStores")["label"]; ?></dt>
					<dd><?php echo $favoriteStores; ?></dd>
				<?php endif; ?>
				<?php if ($shirtSize = $they->get("shirtSize")) : ?>
					<dt><?php echo $they->getField("shirtSize")["label"]; ?></dt>
					<dd><?php echo $shirtSize; ?></dd>
				<?php endif; ?>
				<?php if ($shoeSize = $they->get("shoeSize")) : ?>
					<dt><?php echo $they->getField("shoeSize")["label"]; ?></dt>
					<dd><?php echo $shoeSize; ?></dd>
				<?php endif; ?>
				<?php if ($pantSize = $they->get("pantSize")) : ?>
					<dt><?php echo $they->getField("pantSize")["label"]; ?></dt>
					<dd><?php echo $pantSize; ?></dd>
				<?php endif; ?>
				<?php if ($hatSize = $they->get("hatSize")) : ?>
					<dt><?php echo $they->getField("hatSize")["label"]; ?></dt>
					<dd><?php echo $hatSize; ?></dd>
				<?php endif; ?>
				<?php if ($ringSize = $they->get("ringSize")) : ?>
					<dt><?php echo $they->getField("ringSize")["label"]; ?></dt>
					<dd><?php echo $ringSize; ?></dd>
				<?php endif; ?>
			<?php endif; ?>
			<dt>Last logged in:</dt>
			<dd><?php echo $app->formatDate($they->get("dateLastLoggedIn")); ?></dd>
			<dt>Date joined:</dt>
			<dd><?php echo $app->formatDate($they->get("dateCreated")); ?></dd>
		</dl>
	</div>
</div>