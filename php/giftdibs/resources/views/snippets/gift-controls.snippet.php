<?php 
/* CONTROLS */
if (!isset($counter)) {
	$counter = 1;
}
$myDib = $gift->setDibInfo($gift)->getDibsBy($me);
if ($myDib) {
	$dibStatus = new DibStatus($db);
	$dibStatus = $dibStatus->set("dibStatusId", $myDib->get("dibStatusId"))->find(1);
	$dibStatusSlug = $dibStatus->get("slug");
}
$showFollowMessage = false;
$showLoginMessage = false;
?>
<?php if ($gift->isReceived()) : /* Gift is received? */ ?>
	<div class="btn-group">
		<button class="btn btn-primary active" type="button">
			<small class="glyphicon glyphicon-ok-sign"></small>
			Received!
		</button>
		<button type="button" data-target="#view-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-primary">
			&bull;&bull;&bull;
			<span class="sr-only">View dibbers</span>
		</button>
		<div id="view-dib-modal-<?php echo $counter; ?>" class="modal">
			<?php include MODAL_PATH."dibs-confirmed.modal.php"; ?>
		</div>
	</div>
<?php else : /* Gift is not received */ ?>
	<?php if ($gift->userCanView($me)) : ?>
		<?php if ($me->isAlso($they)) : /* ME owns the gift */ ?>
    		<button type="button" data-target="#mark-received-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-primary">
    			Mark Received
    		</button>
			<div id="mark-received-modal-<?php echo $counter; ?>" class="modal">
				<?php include MODAL_PATH."mark-received.modal.php"; ?>
			</div>
			<!--
        	<button type="button" class="btn btn-default" data-toggle="dropdown">
        		<small class="glyphicon glyphicon-cog"></small>
        		<span class="sr-only">Toggle dropdown</span>
        	</button>
        	<ul class="dropdown-menu">
            	<li>
            		<a href="<?php echo $app->config("page", "edit-gift", array("giftId"=>$gift->get("giftId"))); ?>">
            			Edit
            		</a>
            	</li>
				<li>
					<button type="button" data-target="#delete-gift-modal-<?php echo $counter; ?>" data-toggle="modal">
						Delete
					</button>
				</li>
        	</ul>
        	<div id="delete-gift-modal-<?php echo $counter; ?>" class="modal">
				<?php include MODAL_PATH."delete-gift.modal.php"; ?>
			</div>
			-->
		<?php else : /* ME is not the owner */ ?>
			<?php if ($gift->getDibs()->hasMultiple()) : /* Multiple dibs possible */ ?>
				<?php if ($gift->getDibs()->numAvailable() == 0) : /* No dibs available */ ?>
					<div class="btn-group">
						<?php if ($myDib) : ?>
							<button type="button" data-target="#edit-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-danger">
								<small class="glyphicon glyphicon-tags"></small>
								<?php echo $myDib->get("quantity"); ?> dibbed by you
							</button>
							<div id="edit-dib-modal-<?php echo $counter; ?>" class="modal">
								<?php include MODAL_PATH."dib.modal.php"; ?>
							</div>
						<?php else : ?>
							<a href="#" class="btn btn-danger disabled">
								<small class="glyphicon glyphicon-tags"></small>
								Dibbed
							</a>
						<?php endif; ?>
						<button type="button" data-target="#view-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-danger">
							&bull;&bull;&bull;
						</button>
						<div id="view-dib-modal-<?php echo $counter; ?>" class="modal">
							<?php include MODAL_PATH."dibs-committed.modal.php"; ?>
						</div>
					</div>
				<?php else : /* Some dibs left */ ?>
					<?php if ($gift->getDibs()->numCommitted() > 0) : /* Dibs exist */ ?>
						<?php if ($myDib) : ?>
							<?php
							switch ($dibStatusSlug) {
								case "pending":
									?>
									<button type="button" class="btn btn-default active" disabled>
										<small class="glyphicon glyphicon-tags"></small>
										<?php echo $myDib->get("quantity"); ?> Dibbed by you (pending)
									</button>
									<?php
								break;
								case "delivered":
									?>
									<button type="button" class="btn btn-default active" disabled>
										<small class="glyphicon glyphicon-tag"></small>
										<?php echo $myDib->get("quantity"); ?> Delivered by you
									</button>
									<?php
								break;
								default:
									?>
									<div class="btn-group">
										<button type="button" data-target="#edit-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-danger">
											<small class="glyphicon glyphicon-tags"></small>
											<?php echo $myDib->get("quantity"); ?> dibbed by you (<?php echo $gift->getDibs()->numAvailable(); ?> left)
										</button>
										<div id="edit-dib-modal-<?php echo $counter; ?>" class="modal">
											<?php include MODAL_PATH."dib.modal.php"; ?>
										</div>
										<button type="button" data-target="#view-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-danger">
											&bull;&bull;&bull;
										</button>
										<div id="view-dib-modal-<?php echo $counter; ?>" class="modal">
											<?php include MODAL_PATH."dibs-committed.modal.php"; ?>
										</div>
									</div>
									<?php
								break;
							}
							?>
						<?php else : ?>
							<div class="btn-group">
								<button type="button" data-target="#new-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-success">
									<small class="glyphicon glyphicon-tags"></small>
									Dib this (<?php echo $gift->getDibs()->numAvailable(); ?> left)
								</button>
								<button type="button" data-target="#view-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-success">
									&bull;&bull;&bull;
								</button>
								<div id="view-dib-modal-<?php echo $counter; ?>" class="modal">
									<?php include MODAL_PATH."dibs-committed.modal.php"; ?>
								</div>
							</div>
							<div id="new-dib-modal-<?php echo $counter; ?>" class="modal">
								<?php include MODAL_PATH."dib.modal.php"; ?>
							</div>
						<?php endif; ?>
					<?php else : /* No dibs yet */ ?>
						<?php if ($me->isFollowing($they)) : ?>
							<button type="button" data-target="#new-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-success">
								<small class="glyphicon glyphicon-tags"></small>
								Dib this (<?php echo $gift->getDibs()->numAvailable(); ?> left)
							</button>
							<div id="new-dib-modal-<?php echo $counter; ?>" class="modal">
								<?php include MODAL_PATH."dib.modal.php"; ?>
							</div>
						<?php elseif ($session->isLoggedIn()) : $showFollowMessage = true; ?>
						<?php else : $showLoginMessage = true; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php else : /* Only one dib possible */ ?>
				<?php if ($gift->getDibs()->numAvailable() == 0) : /* Dibbed */ ?>
					<div class="btn-group">
						<?php if ($myDib) : ?>
							<?php
							switch ($dibStatusSlug) {
								case "pending":
									?>
									<button type="button" class="btn btn-default active" disabled>
										<small class="glyphicon glyphicon-tag"></small>
										Dibbed by you (pending)
									</button>
									<?php
								break;
								case "delivered":
									?>
									<button type="button" class="btn btn-default active" disabled>
										<small class="glyphicon glyphicon-tag"></small>
										Delivered by you
									</button>
									<?php
								break;
								default:
									?>
									<button type="button" data-target="#edit-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-danger">
										<small class="glyphicon glyphicon-tag"></small>
										Dibbed by you
									</button>
									<div id="edit-dib-modal-<?php echo $counter; ?>" class="modal">
										<?php include MODAL_PATH."dib.modal.php"; ?>
									</div>
									<?php
								break;
							}
							?>
						<?php else : ?>
							<button class="btn btn-danger" type="button">
								<small class="glyphicon glyphicon-tag"></small>
								Dibbed
							</button>
							<button type="button" data-target="#view-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-danger">
								&bull;&bull;&bull;
							</button>
							<div id="view-dib-modal-<?php echo $counter; ?>" class="modal">
								<?php include MODAL_PATH."dibs-committed.modal.php"; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php else : /* Not dibbed */ ?>
					<?php if ($me->isFollowing($they)) : ?>
						<button type="button" data-target="#new-dib-modal-<?php echo $counter; ?>" data-toggle="modal" class="btn btn-success">
							<small class="glyphicon glyphicon-tag"></small>
							Dib This
						</button>
						<div id="new-dib-modal-<?php echo $counter; ?>" class="modal">
							<?php include MODAL_PATH."dib.modal.php"; ?>
						</div>
					<?php elseif ($session->isLoggedIn()) : $showFollowMessage = true; ?>
					<?php else : $showLoginMessage = true; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php elseif (!$session->isLoggedIn()) : /* User not logged in */ ?>
		<?php $showLoginMessage = true; ?>
	<?php else : $showFollowMessage = true; ?>
	<?php endif; ?>
<?php endif; ?>

<?php if ($showLoginMessage) : ?>
	<div class="alert alert-danger">
		<p>You must be logged in to dib <?php echo $they->firstNamePossessive(); ?> gifts.</p>
		<a href="<?php echo $app->config("page","login",array("redirect"=>urlencode($app->currentUrl()))); ?>" class="btn btn-success">
			<small class="glyphicon glyphicon-log-in"></small>
			Log in to dib this
		</a>
	</div>
<?php elseif ($showFollowMessage) : ?>
	<div class="alert alert-info">
		<p>Only <?php echo $they->firstNamePossessive(); ?> followers can dib <?php echo $they->pronoun("his"); ?> gifts.</p>
		<button type="button" class="btn btn-default btn-follow" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($they->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $they->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>" data-action="<?php echo $app->config('ajax','follow'); ?>">
			<small class="glyphicon glyphicon-user"></small>
			Follow <?php echo $they->get("firstName"); ?> to dib <?php echo $they->pronoun("his"); ?> gifts
		</button>
	</div>
<?php endif; ?>