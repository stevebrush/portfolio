<h1>Notifications</h1>
<?php 
$notification = new Notification($db);
$notifications = $notification->query("SELECT * FROM Notification WHERE userId = {$session->getUserId()} ORDER BY dateCreated DESC");
?>
<?php if ($notifications) : ?>
	<?php foreach ($notifications as $notification) : ?>
		<?php
		$notificationTypeId = $notification->get("notificationTypeId");
		$nt = new NotificationType($db);
		$notificationType = $nt->set("notificationTypeId", $notificationTypeId)->find(1);
		$slug = $notificationType->get("slug");
		?>
		<div class="panel panel-default panel-body panel-notifications">
			<?php switch ($slug) :
			
				case "new-follower" : 
				$follower = new User($db);
				$follower = $follower->set("userId", $notification->get("followerId"))->find(1);
				if ($follower) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","profile", array("userId"=>$follower->get("userId"))); ?>"><img src="<?php echo $follower->getThumbnail()->size("sm")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<h5 class="media-heading">
								<a href="<?php echo $app->config("page","profile", array("userId"=>$follower->get("userId"))); ?>"><?php echo $follower->fullName(); ?></a> is now following you.
							</h5>
							<p><small class="text-muted"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small></p>
							<div class="controls">
								<a href="<?php echo $app->config("ajax","delete-notification"); ?>" class="btn btn-default btn-sm btn-data" data-loading-text="Wait..." data-notification-id="<?php echo $notification->get("notificationId"); ?>" data-signature="<?php echo $me->createSignature($notification->get("notificationId")); ?>" data-redirect="<?php echo $app->config("page", "notifications"); ?>"><small class="glyphicon glyphicon-minus"></small>&nbsp;&nbsp;Got it</a>
							</div>
						</div>
					</div>
					<?php 
				else: 
					$notification->delete();
				endif;
				break;
				?>
				
				<?php
				case "follow-request" :
				$leader = new User($db);
				$leader = $leader->set("userId", $notification->get("followerId"))->find(1);
				if ($leader) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","profile", array("userId"=>$leader->get("userId"))); ?>"><img src="<?php echo $leader->getThumbnail()->size("sm")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<h5 class="media-heading">
								<a href="<?php echo $app->config("page","profile",array("userId"=>$leader->get("userId"))); ?>"><?php echo $leader->fullName(); ?></a> has requested that you follow <?php echo $leader->pronoun("him"); ?>.
							</h5>
							<p><small class="text-muted"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small></p>
							<div class="controls">
								<a href="<?php echo $app->config("ajax","delete-notification"); ?>" class="btn btn-default btn-sm btn-data" data-loading-text="Wait..." data-notification-id="<?php echo $notification->get("notificationId"); ?>" data-signature="<?php echo $me->createSignature($notification->get("notificationId")); ?>" data-redirect="<?php echo $app->config("page", "notifications"); ?>"><small class="glyphicon glyphicon-minus"></small>&nbsp;&nbsp;Got it</a>
								<?php if ($me->isFollowing($leader)) : ?>
									<a href="<?php echo $app->config('ajax','follow'); ?>" class="btn btn-default btn-unfollow btn-data btn-sm" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($leader->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $leader->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Unfollow</a>
								<?php else : ?>
									<a href="<?php echo $app->config('ajax','follow'); ?>" class="btn btn-default btn-follow btn-data btn-sm" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($leader->get("userId")); ?>" data-follower-id="<?php echo $me->get("userId"); ?>" data-leader-id="<?php echo $leader->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-user"></small>&nbsp;&nbsp;Follow</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php
				else:
					$notification->delete();
				endif;
				break;
				?>
				
				<?php 
				case "gift-comment":
				$commenter = new User($db);
				$commenter = $commenter->set("userId", $notification->get("followerId"))->find(1);
				$gift = new Gift($db);
				$gift = $gift->set("giftId", $notification->get("giftId"))->find(1);
				if ($commenter && $gift) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<div class="media">
								<div class="thumbnail pull-left">
									<a href="<?php echo $app->config("page", "profile", array("userId" => $commenter->get("userId"))); ?>"><img src="<?php echo $commenter->getThumbnail()->size("sm")->get("src"); ?>"></a>
								</div>
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo $app->config("page", "profile", array("userId" => $commenter->get("userId"))); ?>"><?php echo $commenter->fullName(); ?></a> commented on your gift, <a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))) . "#tab-comments"; ?>"><?php echo $gift->get("name"); ?></a>
									</h5>
									<small class="text-muted"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small>
								</div>
							</div>
							<div class="controls">
								<a href="<?php echo $app->config("ajax","delete-notification"); ?>" class="btn btn-default btn-sm btn-data" data-loading-text="Wait..." data-notification-id="<?php echo $notification->get("notificationId"); ?>" data-signature="<?php echo $me->createSignature($notification->get("notificationId")); ?>" data-redirect="<?php echo $app->config("page", "notifications"); ?>"><small class="glyphicon glyphicon-minus"></small>&nbsp;&nbsp;Got it</a>
							</div>
						</div>
					</div>
					<?php
				else: 
					$notification->delete();
				endif;
				break;
				?>
				
				<?php 
				case "gift-comment-also":
				$commenter = new User($db);
				$commenter = $commenter->set("userId", $notification->get("followerId"))->find(1);
				$gift = new Gift($db);
				$gift = $gift->set("giftId", $notification->get("giftId"))->find(1);
				$giftOwner = new User($db);
				$giftOwner = $giftOwner->set("userId", $gift->get("userId"))->find(1);
				if ($commenter && $gift && $giftOwner) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<div class="media">
								<div class="thumbnail pull-left">
									<a href="<?php echo $app->config("page", "profile", array("userId" => $commenter->get("userId"))); ?>"><img src="<?php echo $commenter->getThumbnail()->size("sm")->get("src"); ?>"></a>
								</div>
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo $app->config("page", "profile", array("userId" => $commenter->get("userId"))); ?>"><?php echo $commenter->fullName(); ?></a> also commented on <?php echo $giftOwner->firstNamePossessive(); ?> gift, <a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))) . "#tab-comments"; ?>"><?php echo $gift->get("name"); ?></a>
									</h5>
									<small class="text-muted"><span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small>
								</div>
							</div>
							<div class="controls">
								<a href="<?php echo $app->config("ajax","delete-notification"); ?>" class="btn btn-default btn-sm btn-data" data-loading-text="Wait..." data-notification-id="<?php echo $notification->get("notificationId"); ?>" data-signature="<?php echo $me->createSignature($notification->get("notificationId")); ?>" data-redirect="<?php echo $app->config("page", "notifications"); ?>"><small class="glyphicon glyphicon-minus"></small>&nbsp;&nbsp;Got it</a>
							</div>
						</div>
					</div>
					<?php
				else: 
					$notification->delete();
				endif;
				break;
				?>
				
				<?php 
				// Confirm I delivered gift
				case "gift-received":
				$giftOwner = new User($db);
				$giftOwner = $giftOwner->set("userId",$notification->get("followerId"))->find(1);
				$gift = new Gift($db);
				$gift = $gift->set("giftId",$notification->get("giftId"))->find(1);
				$dib = new Dib($db);
				$dib = $dib->set(array(
					"giftId" => $notification->get("giftId"), 
					"userId" => $me->get("userId")
				))->find(1);
				if ($giftOwner && $gift && $dib) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<div class="media">
								<div class="thumbnail pull-left">
									<a href="<?php echo $app->config("page", "profile", array("userId" => $giftOwner->get("userId"))); ?>"><img src="<?php echo $giftOwner->getThumbnail()->size("sm")->get("src"); ?>"></a>
								</div>
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo $app->config("page","profile",array("userId"=>$giftOwner->get("userId"))); ?>"><?php echo $giftOwner->fullName(); ?></a> 
										has marked <?php echo $giftOwner->pronoun("his"); ?> gift, 
										<a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><?php echo $gift->get("name"); ?></a>, as received.
									</h5>
									<small class="text-muted"><span class="glyphicon glyphicon-tag"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small>
								</div>
							</div>
							<div class="well">
								<h5 class="media-heading">Did you deliver this gift to <?php echo $giftOwner->get("firstName"); ?>?</h5>
								<div class="controls">
									<?php include FORM_PATH."confirm-gift-given.form.php"; ?>
								</div>
							</div>
						</div>
					</div>
					<?php
				else:
					$notification->delete();
				endif;
				break;
				?>
				
				<?php 
				// Confirm I received gift
				case "gift-dibbed":
				$dibber = new User($db);
				$dibber = $dibber->set("userId", $notification->get("followerId"))->find(1);
				$gift = new Gift($db);
				$gift = $gift->set("giftId", $notification->get("giftId"))->find(1);
				$dib = new Dib($db);
				$dib = $dib->set(array(
					"giftId" => $notification->get("giftId"), 
					"userId" => $dibber->get("userId")
				))->find(1);
				if ($dibber && $gift && $dib) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<div class="media">
								<div class="thumbnail pull-left">
									<a href="<?php echo $app->config("page", "profile", array("userId" => $dibber->get("userId"))); ?>"><img src="<?php echo $dibber->getThumbnail()->size("sm")->get("src"); ?>"></a>
								</div>
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo $app->config("page","profile",array("userId"=>$dibber->get("userId"))); ?>"><?php echo $dibber->fullName(); ?></a> has marked your gift, <a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><?php echo $gift->get("name"); ?></a>, as delivered.
									</h5>
									<small class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small>
								</div>
							</div>
							<div class="well">
								<strong>Did <?php echo $dibber->get("firstName"); ?> deliver this gift to you?</strong>
								<div class="controls">
									<?php include FORM_PATH."confirm-gift-received.form.php"; ?>
								</div>
							</div>
						</div>
					</div>
					<?php
				else: 
					$notification->delete();
				endif;
				break;
				?>
				
				<?php
				case "gift-received-confirmed" :
				$giftOwner = new User($db);
				$giftOwner = $giftOwner->set("userId",$notification->get("followerId"))->find(1);
				$gift = new Gift($db);
				$gift = $gift->set("giftId",$notification->get("giftId"))->find(1);
				$dib = new Dib($db);
				$dib = $dib->set(array(
					"giftId" => $notification->get("giftId"), 
					"userId" => $me->get("userId")
				))->find(1);
				if ($giftOwner && $gift && $dib) :
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<div class="media">
								<div class="thumbnail pull-left">
									<a href="<?php echo $app->config("page", "profile", array("userId" => $giftOwner->get("userId"))); ?>"><img src="<?php echo $giftOwner->getThumbnail()->size("sm")->get("src"); ?>"></a>
								</div>
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo $app->config("page","profile",array("userId"=>$giftOwner->get("userId"))); ?>"><?php echo $giftOwner->fullName(); ?></a> 
										has confirmed your delivery of <?php echo $giftOwner->pronoun("his"); ?> gift, <a href="<?php echo $app->config("page","gift", array("giftId"=>$gift->get("giftId"))); ?>"><?php echo $gift->get("name"); ?></a>.
									</h5>
									<small class="text-muted"><span class="glyphicon glyphicon-tag"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small>
								</div>
							</div>
							<a href="<?php echo $app->config("ajax", "delete-notification"); ?>" class="btn btn-default btn-sm btn-data" data-loading-text="Wait..." data-notification-id="<?php echo $notification->get("notificationId"); ?>" data-signature="<?php echo $me->createSignature($notification->get("notificationId")); ?>" data-redirect="<?php echo $app->config("page", "notifications"); ?>"><small class="glyphicon glyphicon-minus"></small>&nbsp;&nbsp;Got it</a>
						</div>
					</div>
					<?php
				else:
					$notification->delete();
				endif;
				break;
				?>
				
				<?php
				case "gift-dibbed-confirmed" :
				$dibber = new User($db);
				$dibber = $dibber->set("userId", $notification->get("followerId"))->find(1);
				$gift = new Gift($db);
				$gift = $gift->set("giftId", $notification->get("giftId"))->find(1);
				$dib = new Dib($db);
				$dib = $dib->set(array(
					"giftId" => $notification->get("giftId"), 
					"userId" => $dibber->get("userId")
				))->find(1);
				if ($dibber && $gift && $dib) : 
					$quantity = $dib->get("quantity");
					?>
					<div class="media">
						<div class="thumbnail pull-left">
							<a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>"><img src="<?php echo $gift->getThumbnail()->size("md")->get("src"); ?>"></a>
						</div>
						<div class="media-body">
							<div class="media">
								<div class="thumbnail pull-left">
									<a href="<?php echo $app->config("page", "profile", array("userId" => $dibber->get("userId"))); ?>"><img src="<?php echo $dibber->getThumbnail()->size("sm")->get("src"); ?>"></a>
								</div>
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo $app->config("page", "profile", array("userId" => $dibber->get("userId"))); ?>"><?php echo $dibber->fullName(); ?></a>
										dibbed <?php echo ($quantity > 1) ? "<span class=\"badge\">{$quantity}</span> of " : ""; ?>your gift, 
										<a href="<?php echo $app->config("page", "gift", array("giftId" => $gift->get("giftId"))); ?>"><?php echo $gift->get("name"); ?></a>.
									</h5>
									<small class="text-muted"><span class="glyphicon glyphicon-tag"></span>&nbsp;&nbsp;<?php echo $app->friendlyDate($notification->get("dateCreated")); ?></small>
								</div>
							</div>
							<div class="controls">
								<a href="<?php echo $app->config("ajax", "delete-notification"); ?>" class="btn btn-default btn-sm btn-data" data-loading-text="Wait..." data-notification-id="<?php echo $notification->get("notificationId"); ?>" data-signature="<?php echo $me->createSignature($notification->get("notificationId")); ?>" data-redirect="<?php echo $app->config("page", "notifications"); ?>"><small class="glyphicon glyphicon-minus"></small>&nbsp;&nbsp;Got it</a>
								<a href="<?php echo $app->config("page", "new-message", array("userId" => $dibber->get("userId"), "redirect" => $app->currentUrl(), "content" => "{$dibber->get('firstName')},%0AThanks for getting me exactly what I wanted: {$gift->get('name')}.")); ?>" class="btn btn-primary btn-sm" target="_blank"><small class="glyphicon glyphicon-heart"></small>&nbsp;&nbsp;Send thank you note</a>
							</div>
						</div>
					</div>
					<?php
				else: 
					$notification->delete();
				endif;
				break;
				?>
			<?php endswitch; ?>
		</div>
	<?php endforeach; ?>
<?php else : ?>
	<div class="alert alert-info">You do not have any notifications at this time.</div>
<?php endif; ?>