<?php 
$statusTab = (isset($_GET["status"])) ? $_GET["status"] : "active";
switch ($statusTab) {
	case "active":
	default:
		$sql = "SELECT User.firstName, User.lastName, User.userId, User.imageId FROM User, Gift, Dib WHERE Dib.userId = {$session->getUserId()} AND Dib.giftId = Gift.giftId AND Gift.userId = User.userId AND Dib.dibStatusId != '4' GROUP BY User.userId";
	break;
	case "complete":
		$sql = "SELECT User.firstName, User.lastName, User.userId, User.imageId FROM User, Gift, Dib WHERE Dib.userId = {$session->getUserId()} AND Dib.giftId = Gift.giftId AND Gift.userId = User.userId AND Dib.dibStatusId = '4' GROUP BY User.userId";
	break;
}
$user = new User($db);
$users = $user->query($sql);
?>
<div class="page-heading">
	<h1>My Dibs</h1>
</div>
<div class="container-fluid">
	<ul class="nav nav-tabs">
		<li<?php echo ($statusTab === "active") ? " class=\"active\"": ""; ?>><a href="<?php echo $app->config("page", "dibs"); ?>">Active</a></li>
		<li<?php echo ($statusTab === "complete") ? " class=\"active\"": ""; ?>><a href="<?php echo $app->config("page", "dibs-complete"); ?>">Complete</a></li>
	</ul>
	<?php
	/**
	
	1. Get all users that I've dibbed from
	2. For each user, get the wish lists
	3. For each wish list, get the gifts I've dibbed
	4. For each gift, display it's dib status
	
	*/
	?>
	<?php if ($users) : ?>
		<?php foreach ($users as $u) : ?>
			<div class="media user-card-dibs">
				<div class="thumbnail pull-left">
					<img src="<?php echo $u->getThumbnail()->size("sm")->get("src"); ?>">
				</div>
				<div class="media-body">
					<h3 class="media-heading">For <a href="<?php echo $app->config("page", "profile", array("userId" => $u->get("userId"))); ?>"><?php echo $u->fullName(); ?></a></h3>
				</div>
			</div>
			<div class="panel panel-default">
				<table class="table table-dibs">
					<thead class="hidden">
						<tr>
							<th class="table-dibs-name">Gift name</th>
							<th class="table-dibs-status">Status</th>
							<th class="table-dibs-due">Due</th>
							<th class="table-dibs-price">Price</th>
							<th class="table-dibs-controls"></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$wishList = new WishList($db);
						if ($statusTab === "active") {
							$sql = "SELECT WishList.wishListId, WishList.name FROM Dib, WishList, Gift WHERE Dib.dibStatusId != '4' AND Dib.userId = {$session->getUserId()} AND Dib.giftId = Gift.giftId AND Gift.userId = {$u->get('userId')} AND Gift.wishListId = WishList.wishListId GROUP BY WishList.wishListId";
						} else {
							$sql = "SELECT WishList.wishListId, WishList.name FROM Dib, WishList, Gift WHERE Dib.dibStatusId = '4' AND Dib.userId = {$session->getUserId()} AND Dib.giftId = Gift.giftId AND Gift.userId = {$u->get('userId')} AND Gift.wishListId = WishList.wishListId GROUP BY WishList.wishListId";
						}
						$wishLists = $wishList->query($sql);
						$totalPrice = 0;
						?>
						<?php if ($wishLists) : ?>
							<?php foreach ($wishLists as $wishList) : ?>	
								<tr>
									<td class="table-dibs-wish-list" colspan="5"><h5><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;<a href="<?php echo $app->config("page", "wish-list", array("wishListId"=>$wishList->get("wishListId"))); ?>"><?php echo $wishList->get("name"); ?></a></h5></td>
								</tr>
								<?php 
								$gift = new Gift($db);
								if ($statusTab === "active") {
									$sql = "SELECT Gift.giftId FROM Dib, WishList, Gift WHERE Dib.dibStatusId != '4' AND Dib.userId = {$session->getUserId()} && Dib.giftId = Gift.giftId && Gift.userId = {$u->get('userId')} && Gift.wishListId = {$wishList->get('wishListId')} GROUP BY Gift.giftId";
									
								} else {
									$sql = "SELECT Gift.giftId FROM Dib, WishList, Gift WHERE Dib.dibStatusId = '4' AND Dib.userId = {$session->getUserId()} && Dib.giftId = Gift.giftId && Gift.userId = {$u->get('userId')} && Gift.wishListId = {$wishList->get('wishListId')} GROUP BY Gift.giftId";
								}
								$gifts = $gift->query($sql);
								?>
								<?php if ($gifts) : ?>
									<?php foreach ($gifts as $g) : ?>
										<?php
										$gift = new Gift($db);
										$gift = $gift->set("giftId", $g->get("giftId"))->find(1);
										$dib = new Dib($db);
										if ($statusTab === "active") {
											$dib = $dib->query("SELECT * FROM Dib WHERE giftId = {$gift->get('giftId')} AND userId = {$session->getUserId()} AND dibStatusId != '4' LIMIT 1");
										} else {
											$dib = $dib->query("SELECT * FROM Dib WHERE giftId = {$gift->get('giftId')} AND userId = {$session->getUserId()} AND dibStatusId = '4' LIMIT 1");
										}
										?>
										<?php if ($dib && $gift) : ?>
											<?php
											$dib = array_shift($dib);
											$totalPrice += $gift->get("price");
											$dibStatusId = $dib->get("dibStatusId");
											$dibStatus = new DibStatus($db);
											$dibStatus = $dibStatus->set("dibStatusId", $dibStatusId)->find(1);
											$dibStatusSlug = $dibStatus->get("slug");
											?>
											<tr>
												<td class="table-dibs-name">
													<div class="media product-card">
														<div class="thumbnail pull-left">
															<img src="<?php echo $gift->getThumbnail()->size("sm")->get("src"); ?>">
														</div>
														<div class="media-body">
															<p>
																<strong><a href="<?php echo $app->config("page","gift",array("giftId"=>$gift->get("giftId"))); ?>">
																	<?php echo ($dibStatusSlug === "purchased") ? "<span class=\"text-strikeout\">" : ""; ?>
																		<?php echo $gift->get("name"); ?>
																	<?php echo ($dibStatusSlug === "purchased") ? "</span>" : ""; ?>
																</a></strong><br>
																External link: <a href="<?php echo $gift->get("url"); ?>" target="_blank"><?php echo $app->friendlyUrl($gift->get("url")); ?>&nbsp;&rarr;</a><br>
																<span class="text-muted">You dibbed <?php echo $dib->get("quantity"); ?></span>
															</p>
															<?php if ($statusTab === "active") : ?>
																<?php if ($dibStatusSlug !== "pending") : ?>
																	<p><a href="#edit-dib-modal-<?php echo $gift->get("giftId"); ?>" data-toggle="modal" class="btn btn-default btn-sm"><small class="glyphicon glyphicon-pencil"></small>&nbsp;&nbsp;Edit</a></p>
																	<div id="edit-dib-modal-<?php echo $gift->get("giftId"); ?>" class="modal">
																		<?php include MODAL_PATH."dib.modal.php"; ?>
																	</div>
																<?php endif; ?>
															<?php endif; ?>
														</div>
													</div>
												</td>
												<td class="table-dibs-status">
													<?php if ($statusTab === "active") : ?>
														<?php include FORM_PATH."dib-status.form.php"; ?>
													<?php else : ?>
														Delivered<br>
														<small class="text-muted"><?php echo $app->formatDate($dib->get("dateDelivered"), "d M Y"); ?></small>
													<?php endif; ?>
												</td>
												<?php if ($statusTab === "active") : ?>
													<td class="table-dibs-due">
														<strong>Due:</strong><br><?php echo $app->formatDate($dib->get("dateProjected"), "M d"); ?>
													</td>
												<?php endif; ?>
												<td class="table-dibs-price bg-info">
													<span class="price"><?php echo $app->formatPrice($gift->get("price")); ?></span>
												</td>
												<td class="table-dibs-controls">
													<?php 
														$product = new Product($app);
														$product = $product->set(array(
															"productId" => $gift->get("productId"),
															"productIdType" => $gift->get("productIdType")
														))->find(1);
													?>
													<?php if ($product) : ?>
														<div class="media product-card">
															<div class="thumbnail pull-right">
																<a href="<?php echo $product->get("url"); ?>" target="_blank"><img src="<?php echo $product->getThumbnail()->size("sm")->get("src"); ?>"></a>
															</div>
															<p>
																<span class="price"><?php echo $product->priceHtml(); ?></span>
																<a href="<?php echo $product->get("url"); ?>" target="_blank"><strong><?php echo $product->get("name"); ?></strong></a>
															</p>
														</div>
														<a href="<?php echo $product->get("url"); ?>" target="_blank" class="btn btn-warning btn-sm"><?php echo ($product->isOnSale()) ? "Get deal" : "Buy online"; ?>&nbsp;&rarr;</a>
													<?php endif; ?>
												</td>
											</tr>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<tr>
							<td class="bg-warning" colspan="<?php echo ($statusTab === "active") ? "3" : "2"; ?>">
								<strong>Total for <?php echo $u->get("firstName"); ?>:</strong>
							</td>
							<td class="bg-info">
								<strong class="price"><?php echo $app->formatPrice($totalPrice); ?></strong>
							</td>
							<td class="bg-warning"></td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="alert alert-info">
			You have no active dibs at this time.
		</div>
	<?php endif; ?>
</div>