<div class="media user-card clearfix">
	<div class="thumbnail pull-left">
		<?php $thumbnail = $they->getThumbnail()->size("sm"); ?>
		<img src="<?php echo $thumbnail->get("src"); ?>">
	</div>
	<div class="media-body">
		<h4 class="media-heading">
			<a href="<?php echo $app->config("page", "profile", array("userId" => $they->get("userId"))); ?>"><?php echo $they->fullName(); ?></a>
		</h4>
		<small class="text-muted">&nbsp;<?php echo $app->friendlyDate($gift->get("timestamp")); ?></small>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-body">
		<div id="section-gift-detail">
			<div class="row">
				<div class="col-sm-5">
					<div class="thumbnail">
						<?php $thumbnail = $gift->getThumbnail()->size("lg"); ?>
						<img src="<?php echo $thumbnail->get("src"); ?>" alt="<?php echo $gift->get("name"); ?>">
					</div>
				</div>
				<div class="col-sm-7">
					<h1><?php echo $gift->get("name"); ?></h1>
					<p><strong class="price"><?php echo $app->formatPrice($gift->get("price")); ?></strong> <span class="priority"><?php echo $gift->priorityHtml(); ?></span></p>
					<div class="controls">
						<?php include SNIPPET_PATH."gift-controls.snippet.php"; ?>
					</div>
					<?php if ($wishList->getType() === "registry") : ?>
						<div class="alert alert-warning"><small class="glyphicon glyphicon-tag"></small>&nbsp;&nbsp;<?php echo ($gift->dibs()->numCommitted() == 1) ? "<strong>".$gift->dibs()->numCommitted() . "</strong> has" : "<strong>".$gift->dibs()->numCommitted() . "</strong> have"; ?> been dibbed</div>
					<?php endif; ?>
				
					<?php if ($wishList->getType() !== "registry" && (!$session->isLoggedIn() || !$me->isFollowing($they))) : ?>
						<div class="modal modal-auto-open">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title"><strong class="text-danger">Watch it!</strong> This gift could be reserved by a buyer.</h4>
								</div>
								<div class="modal-body">
									<?php if ($session->isLoggedIn()) : ?>
										<p class="alert alert-info">To see if this gift is reserved or not, you need to <strong>follow</strong> <?php echo $they->get("firstName"); ?>.</p>
										<button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Got it.</button>
									<?php else : ?>
										<div class="alert alert-info">
											<p>You must be logged in to GiftDibs to view the status of this gift.</p>
										</div>
										<a href="<?php echo $app->config("page","login",array("redirect"=>urlencode($app->currentUrl()))); ?>" class="btn btn-primary btn-lg btn-block">Log in now</a>
										<a href="#" class="btn btn-lg btn-facebook btn-block btn-facebook-signup" data-loading-text="Processing...">Create a free account using Facebook</a>
										<a href="<?php echo $app->config("page","signup",array("redirect"=>urlencode($app->currentUrl()))); ?>" class="btn btn-link btn-block">Sign up using my email address&nbsp;&rarr;</a>
										<button type="button" class="btn btn-default btn-block" data-dismiss="modal">Not right now</button>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
					<?php 
					$product = new Product($app);
					$product = $product->set(array(
						"productId" => $gift->get("productId"),
						"productIdType" => $gift->get("productIdType")
					))->find(1);
					?>
					<?php if ($product) : ?>
						<div class="media product-card">
							<div class="thumbnail pull-left">
								<a href="<?php echo $product->get("url"); ?>" target="_blank"><img src="<?php echo $product->getThumbnail()->size("sm")->get("src"); ?>"></a>
							</div>
							<div class="media-body">
								<a href="<?php echo $product->get("url"); ?>" target="_blank">
									<strong><?php echo $product->get("name"); ?></strong>
								</a>
								<p class="price"><?php echo $product->priceHtml(); ?></p>
								<a href="<?php echo $product->get("url"); ?>" target="_blank" class="btn btn-warning"><small class="glyphicon glyphicon-shopping-cart"></small>&nbsp;&nbsp;<?php echo ($product->isOnSale()) ? "Get deal" : "Buy now"; ?></a>
								<?php if ($session->isLoggedIn()) : ?>
									<a href="#add-to-wish-list-modal" data-toggle="modal" class="btn btn-default"><small class="glyphicon glyphicon-list-alt"></small>&nbsp;&nbsp;Add to</a>
									<div id="add-to-wish-list-modal" class="modal">
										<?php include MODAL_PATH."add-to-wish-list.modal.php"; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			
			<div class="panel-nav">
				<div class="container">
					<!-- Tab navigation -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab-details" data-toggle="tab">Details</a></li>
						<li><a href="#tab-comments" data-toggle="tab">Comments</a></li>
						<li><a href="#tab-related-products" data-toggle="tab">Related Products</a></li>
					</ul>
				</div>
			</div>
			
			<!-- Tab panes -->
			<div class="tab-content">
				<div class="tab-pane active" id="tab-details">
					<div class="table-responsive">
						<table class="table table-striped">
							<?php if ($notes = $gift->get("notes")) : ?>
								<tr>
									<td>Notes:</td>
									<td><?php echo $notes; ?></td>
								</tr>
							<?php endif; ?>
							<?php if ($url = $gift->urlHtml()) : ?>
								<tr>
									<td>External link:</td>
									<td><?php echo $url; ?></td>
								</tr>
							<?php endif; ?>
							<?php if (!$gift->isReceived()) : ?>
								<tr>
									<td>Dib information</td>
									<td>
										<?php if ($session->isLoggedIn()) : ?>
											<?php if ($me->isAlso($they)) : ?>
												<?php if ($wishList->getType() == "registry") : ?>
													<?php echo "(".$gift->dibs()->numAvailable()." of ".$gift->dibs()->numPossible().") available"; ?>
												<?php else : ?>
													<?php echo $gift->dibs()->numPossible() . " available"; ?>
												<?php endif; ?>
											<?php elseif ($me->isFollowing($they)) : ?>
												<?php echo "(".$gift->dibs()->numAvailable()." of ".$gift->dibs()->numPossible().") available"; ?>
											<?php else : ?>
												You must be a follower of <?php echo $they->firstNamePossessive(); ?> to view this gift's dib information.
											<?php endif; ?>
										<?php else : ?>
											Please <a href="<?php echo $app->config("page", "login", array("redirect"=>$app->currentUrl())); ?>">log in</a> to see how many dibs are available.
										<?php endif; ?>
									</td>
								</tr>
							<?php endif; ?>
							<tr>
								<td>Quantity requested</td>
								<td><?php echo $gift->get("quantity"); ?></td>
							</tr>
							<tr>
								<td>Preferred condition</td>
								<td><?php echo $gift->gradeLabel("gradeId"); ?></td>
							</tr>
							<tr>
								<td>Wish list</td>
								<td><small class="glyphicon glyphicon-list-alt"></small>&nbsp;&nbsp;<a href="<?php echo $app->config("page","wish-list",array("wishListId"=>$wishList->get("wishListId"))); ?>"><?php echo $wishList->get("name"); ?></a></td>
							</tr>
							<tr>
								<td>Privacy</td>
								<td>
									<?php $privacyId = $wishList->get("privacyId"); ?>
									<?php if ($privacyId == 1) : ?>
										<small class="glyphicon glyphicon-globe"></small>&nbsp;&nbsp;This gift is <strong>public</strong>.
									<?php elseif ($privacyId == 2) : ?>
										<small class="glyphicon glyphicon-eye-close"></small>&nbsp;&nbsp;Only <strong>you</strong> can view this gift.
									<?php elseif ($privacyId == 3) : ?>
										<small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Only <strong><?php echo ($me->isAlso($they)) ? "your" : $they->firstNamePossessive(); ?> followers</strong> can view this gift.
									<?php elseif ($privacyId == 4) : ?>
										<?php if ($privateUsers) : ?>
											<small class="glyphicon glyphicon-eye-close"></small>&nbsp;&nbsp;Only 
											<?php $counter = 0; $length = count($privateUsers); ?>
											<?php foreach ($privateUsers as $user) : ?>
												<a href="<?php echo $app->config('page','profile',array('userId'=>$user->get("userId"))); ?>"><?php echo $user->fullName(); ?></a>
												<?php echo ($counter++ > $length) ? ", " : ""; ?>
											<?php endforeach; ?>
											<?php if ($me->isAlso($they)) : ?> and you<?php endif; ?>
											 may view this gift.
										<?php endif; ?>
									<?php endif; ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="tab-comments">
					<?php if ($me->isAlso($they) || $me->isFollowing($they)) : ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h5 class="panel-title">Comments</h5>
							</div>
							<div class="panel-body">
								<?php 
								$comment = new Comment($db);
								$comments = $comment->set("giftId", $giftId)->find();
								?>
								<?php if ($comments) : ?>
									<?php 
									$u = new User($db); 
									?>
									<?php foreach ($comments as $comment) : ?>
										<?php
										$user = $u->set("userId", $comment->get("userId"))->find(1); 
										?>
										<div class="media comment">
											<div class="pull-left thumbnail"><img src="<?php echo $user->getThumbnail()->size("sm")->get("src"); ?>"></div>
											<div class="media-body">
												<?php echo $comment->get("content"); ?><br>
												<small>
													<a href="<?php echo $app->config("page", "profile", array("userId" => $user->get("userId"))); ?>"><?php echo $user->fullName(); ?></a><span class="text-muted"> &middot; <?php echo $app->friendlyDate($comment->get("timestamp")); ?></span>
												</small>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
								<div class="media comment">
									<div class="thumbnail pull-left">
										<a href="<?php echo $app->config("page", "profile"); ?>"><img src="<?php echo $me->getThumbnail()->size("sm")->get("src"); ?>"></a>
									</div>
									<div class="media-body">
										<?php include FORM_PATH . "gift-comment.form.php"; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="tab-related-products">...</div>
			</div>
		</div>
	</div>
</div>