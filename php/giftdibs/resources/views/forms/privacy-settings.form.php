<div class="form-container">
	<div class="form-heading">
		<h1 class="form-title"><?php echo $page->getTitle(); ?></h1>
	</div>
	<div class="form-body">
		<h3>Blocked Users</h3>
		<p>Blocked users cannot see your wish lists, your profile, or find you in search results.</p>
		<?php
		$u_b = new User_Blocked($db);
		$ubs = $u_b->set("userId", $session->getUserId())->find();
		?>
		<?php if ($ubs) : ?>
			<?php $user = new User($db); ?>
			<?php foreach ($ubs as $ub) : ?>
				<?php $u = $user->set("userId", $ub->get("blockedId"))->find(1); ?>
				<?php echo $u->fullName(); ?>&nbsp;-&nbsp;
				<button class="btn btn-default" type="button" data-action="<?php echo $app->config('ajax','block-user'); ?>" data-loading-text="Wait..." data-signature="<?php echo $me->createSignature($u->get("userId")); ?>" data-user-id="<?php echo $me->get("userId"); ?>" data-blocked-id="<?php echo $u->get("userId"); ?>" data-redirect="<?php echo $app->currentUrl(); ?>">
					Unblock
				</button>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="alert alert-info">You haven't blocked any users.</div>
		<?php endif; ?>
	</div>
</div>

<h4></h4>


