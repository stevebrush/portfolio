<?php
$followers = $me->getFollowers();
$leaders = $me->getLeaders();
?>
<div id="supplement">
	<?php if ($session->isLoggedIn()) : ?>
		<div class="panel panel-primary">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#supplement-following" data-toggle="tab">Following</a></li>
				<li><a href="#supplement-followers" data-toggle="tab">Followers</a></li>
			</ul>
			<div class="panel-body tab-content">
				<div class="tab-pane active" id="supplement-following">
					<?php if ($leaders) : ?>
						<ul class="media-list">
							<?php foreach ($leaders as $leader) : ?>
								<li class="media">
									<a class="thumbnail pull-left" href="<?php echo $app->config("page", "profile", array("userId" => $leader->get("userId"))); ?>">
										<img class="media-object" src="<?php echo $leader->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $leader->firstNamePossessive(); ?> thumbnail">
									</a>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo $app->config("page", "profile", array("userId" => $leader->get("userId"))); ?>"><?php echo $leader->fullName(); ?></a>
										</h4>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="supplement-followers">
					<?php if ($followers) : ?>
						<ul class="media-list">
							<?php foreach ($followers as $follower) : ?>
								<li class="media">
									<a class="thumbnail pull-left" href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>">
										<img class="media-object" src="<?php echo $follower->getThumbnail()->size("sm")->get("src"); ?>" alt="<?php echo $follower->firstNamePossessive(); ?> thumbnail">
									</a>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo $app->config("page", "profile", array("userId" => $follower->get("userId"))); ?>"><?php echo $follower->fullName(); ?></a>
										</h4>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<p>
					<a class="btn btn-default btn-sm btn-block" href="<?php echo $app->config("page", "invite-friends"); ?>">Invite Friends</a>
				</p>
			</div>
		</div>
	<?php endif; ?>
</div>