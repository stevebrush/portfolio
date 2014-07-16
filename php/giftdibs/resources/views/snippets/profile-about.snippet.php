<?php 
// Get field labels for profile information
$they->getInputs();
?>
<?php if ($me->isFollowing($they) || $me->isAlso($they)) : ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">Information</h2>
			<div class="control-panel">
				<a class="btn btn-default" href="#">Edit</a>
			</div>
		</div>
		<div class="panel-body">
			<table class="table table-clean table-condensed">
				<tbody>
					<?php if (!$they->birthdayIsPrivate() || $me->isAlso($they)) : ?>
						<tr>
							<th>Birthday</th>
							<td><?php echo $they->formattedBirthday(); ?><?php echo ($they->birthdayIsPrivate()) ? " <em class=\"text-muted\">(private)</em>" : ""; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($interests = $they->get("interests")) : ?>
						<tr>
							<th><?php echo $they->getField("interests")["label"]; ?></th>
							<td><?php echo $interests; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($favoriteStores = $they->get("favoriteStores")) : ?>
						<tr>
							<th><?php echo $they->getField("favoriteStores")["label"]; ?></th>
							<td><?php echo $favoriteStores; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($shirtSize = $they->get("shirtSize")) : ?>
						<tr>
							<th><?php echo $they->getField("shirtSize")["label"]; ?></th>
							<td><?php echo $shirtSize; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($shoeSize = $they->get("shoeSize")) : ?>
						<tr>
							<th><?php echo $they->getField("shoeSize")["label"]; ?></th>
							<td><?php echo $shoeSize; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($pantSize = $they->get("pantSize")) : ?>
						<tr>
							<th><?php echo $they->getField("pantSize")["label"]; ?></th>
							<td><?php echo $pantSize; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($hatSize = $they->get("hatSize")) : ?>
						<tr>
							<th><?php echo $they->getField("hatSize")["label"]; ?></th>
							<td><?php echo $hatSize; ?></td>
						</tr>
					<?php endif; ?>
					<?php if ($ringSize = $they->get("ringSize")) : ?>
						<tr>
							<th><?php echo $they->getField("ringSize")["label"]; ?></th>
							<td><?php echo $ringSize; ?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<th>Last logged in:</th>
						<td><?php echo $app->formatDate($they->get("dateLastLoggedIn")); ?></td>
					</tr>
					<tr>
						<th>Date joined:</th>
						<td><?php echo $app->formatDate($they->get("dateCreated")); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php endif; ?>
<!--
<div class="tab-content">
	<div class="tab-pane active" id="tab-profile">
		<?php
		$wishList = new WishList($db);
		$wishLists = $wishList->set("userId", $they->get("userId"))->findForFollower($me, null, "wishListId,name,userId,privacyId", null, "");
		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Recent Wish Lists</h2>
				<div class="control-panel">
					<?php if ($me->isAlso($they)) : ?>
						<a class="btn btn-default" href="<?php echo $app->config("page", "new-wish-list"); ?>"><small class="glyphicon glyphicon-plus"></small>New</a>
					<?php endif; ?>
					<?php if (count($wishLists) > 2) : ?>
						<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#wish-lists-overflow" title="Show more wish lists">
							<small class="glyphicon glyphicon-chevron-down"></small>More
						</button>
					<?php endif; ?>
				</div>
			</div>
			<div class="panel-body">
				<?php $counter = 0; ?>
				<?php if ($wishLists) : ?>
					<ul class="nav nav-ribbon nav-ribbon-success">
						<?php foreach ($wishLists as $wishList) : ?>
							<?php if ($counter === 2) : ?>
								<li class="collapse" id="wish-lists-overflow">
									<ul class="nav nav-ribbon nav-ribbon-success">
							<?php endif; ?>
							<li><a href="<?php echo $app->config("page", "wish-list", array("wishListId" => $wishList->get("wishListId"))); ?>"><?php echo $wishList->get("name"); ?></a></li>
							<?php $counter++; ?>
						<?php endforeach; ?>
						<?php if ($counter > 2) : ?>
									</ul>
								</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		
		<?php
		// Get most wanted
		$gift = new Gift($db);
		$mostWanted = $gift->set(array(
			"userId" => $they->get("userId"),
			"isReceived" => "0",
			"follower" => $me
		))->findPackage(5);
		?>
		<?php if ($mostWanted["gifts"]) : ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title"><?php echo $they->firstNamePossessive(); ?> Most Wanted</h2>
				</div>
				<div class="panel-body">
					<ul class="media-list item-list">
						<?php foreach ($mostWanted["gifts"] as $gift) : ?>
							<li class="media">
								<a class="thumbnail pull-left" href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>">
									<img class="media-object" src="<?php echo $gift->getThumbnail()->size("sm")->get("src"); ?>" alt="Gift thumbnail">
								</a>
								<div class="media-body">
									<h3 class="media-heading">
										<a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>">
											<?php echo $gift->get("name"); ?>
										</a>
									</h3>
									<div class="product-info">
										<?php echo $gift->priceHtml(); ?>
									</div>
									<div class="user-info">
										<?php $priorityLabel = $gift->priorityLabel(); ?>
										<div class="priority" title="Priority: <?php echo $priorityLabel; ?>">
											<span class="sr-only">Priority: <?php echo $priorityLabel; ?></span>
											<?php echo $gift->priorityHtml(); ?>
										</div>
										<?php if ($me->isAlso($they)) : ?>
											&nbsp;&nbsp;&nbsp;<a href="<?php echo $app->config("page", "edit-gift", array("giftId" => $gift->get("giftId"))); ?>">Edit</a>&nbsp;&nbsp;&nbsp;<a href="#">Remove</a>
										<?php endif; ?>
									</div>
								</div>
								<?php if ($me->isAlso($they)) : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>Mark Received</button> 
									</div>
								<?php elseif ($me->isFollowing($they)) : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-tag"></span>Dib this</button> 
										<button class="btn btn-default" type="button">Add to...</button> 
									</div>
								<?php elseif ($session->isLoggedIn()) : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-user"></span>Follow to dib this</button> 
										<button class="btn btn-default" type="button">Add to...</button> 
									</div>
								<?php else : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-log-in"></span>Log in to dib this</button> 
									</div>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="tab-pane" id="tab-recent-gifts">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Recent Gifts</h2>
				<div class="control-panel">
					<a class="btn btn-default" href="#"><small class="glyphicon glyphicon-plus"></small>New gift</a>
					<span class="btn-group" data-filter-type="view" data-target="#profile-recent-gifts">
						<button class="btn btn-default" type="button">
							<span class="sr-only">View as list</span>
							<span class="glyphicon glyphicon-list"></span>
						</button>
						<button class="btn btn-default active" type="button">
							<span class="sr-only">View as thumbnails</span>
							<span class="glyphicon glyphicon-th-large"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="panel-body">
				<?php
				// All gifts
				$gift = new Gift($db);
				$allGifts = $gift->set(array(
					"userId" => $they->get("userId"),
					"isReceived" => "0",
					"follower" => $me
				))->findPackage(10);
				?>
				<?php if ($allGifts["gifts"]) : ?>
					<ul class="media-list media-boxes" id="profile-recent-gifts">
						<?php foreach ($allGifts["gifts"] as $gift) : ?>
							<li class="media">
								<a class="thumbnail pull-left" href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>">
									<img class="media-object" src="<?php echo $gift->getThumbnail()->size("lg")->get("src"); ?>" alt="Gift thumbnail">
								</a>
								<div class="media-body">
									<h3 class="media-heading">
										<a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>">
											<?php echo $gift->get("name"); ?>
										</a>
									</h3>
									<div class="product-info">
										<?php echo $gift->priceHtml(); ?>
									</div>
									<div class="user-info">
										<?php $priorityLabel = $gift->priorityLabel(); ?>
										<div class="priority" title="Priority: <?php echo $priorityLabel; ?>">
											<span class="sr-only">Priority: <?php echo $priorityLabel; ?></span>
											<?php echo $gift->priorityHtml(); ?>
										</div>
										<?php if ($me->isAlso($they)) : ?>
											&nbsp;&nbsp;&nbsp;<a href="<?php echo $app->config("page", "edit-gift", array("giftId" => $gift->get("giftId"))); ?>">Edit</a>&nbsp;&nbsp;&nbsp;<a href="#">Remove</a>
										<?php endif; ?>
									</div>
								</div>
								<?php if ($me->isAlso($they)) : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>Mark Received</button> 
									</div>
								<?php elseif ($me->isFollowing($they)) : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-tag"></span>Dib this</button> 
										<button class="btn btn-default" type="button">Add to...</button> 
									</div>
								<?php elseif ($session->isLoggedIn()) : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-user"></span>Follow to dib this</button> 
										<button class="btn btn-default" type="button">Add to...</button> 
									</div>
								<?php else : ?>
									<div class="control-panel">
										<button class="btn btn-primary" type="button"><span class="glyphicon glyphicon-log-in"></span>Log in to dib this</button> 
									</div>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="tab-following">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Following</h2>
				<div class="control-panel">
					<a class="btn btn-default" href="#"><span class="glyphicon glyphicon-plus"></span>Invite</a>
					<a class="btn btn-default" href="#"><span class="glyphicon glyphicon-search"></span>Find</a>
				</div>
			</div>
			<div class="panel-body">
				<?php if ($leaders = $they->getLeaders()) : ?>
					<ul class="media-list user-list">
						<?php foreach ($leaders as $leader) : ?>
							<li class="media user-card">
								<a class="thumbnail pull-left" href="<?php echo $app->config("page", "profile", array("userId" => $leader->get("userId"))); ?>">
									<img class="media-object" src="<?php echo $leader->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $leader->firstNamePossessive(); ?> thumbnail">
								</a>
								<div class="media-body">
									<h3 class="media-heading">
										<a href="<?php echo $app->config("page", "profile", array("userId" => $leader->get("userId"))); ?>"><?php echo $leader->fullName(); ?></a>
									</h3>
									<small class="text-muted">15 gifts</small>
								</div>
								<div class="control-panel">
									<button class="btn btn-primary" title="Click to stop following Michael"><span class="glyphicon glyphicon-user"></span>Following</button>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="tab-followers">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Followers</h2>
				<div class="control-panel">
					<a class="btn btn-default" href="#"><span class="glyphicon glyphicon-plus"></span>Invite</a>
					<a class="btn btn-default" href="#"><span class="glyphicon glyphicon-search"></span>Find</a>
				</div>
			</div>
			<div class="panel-body">
				<?php if ($followers = $they->getFollowers()) : ?>
					<ul class="media-list user-list">
						<?php foreach ($followers as $follower) : ?>
							<li class="media user-card">
								<a class="thumbnail pull-left" href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>">
									<img class="media-object" src="<?php echo $follower->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $follower->firstNamePossessive(); ?> thumbnail">
								</a>
								<div class="media-body">
									<h3 class="media-heading">
										<a href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>"><?php echo $follower->fullName(); ?></a>
									</h3>
									<small class="text-muted">15 gifts</small>
								</div>
								<div class="control-panel">
									<button class="btn btn-primary" title="Click to stop following Michael"><span class="glyphicon glyphicon-user"></span>Following</button>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
-->