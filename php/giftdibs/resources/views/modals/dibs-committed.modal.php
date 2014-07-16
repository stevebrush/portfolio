<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Dibs for <strong><?php echo $gift->get("name"); ?></strong> <small>(<?php echo $gift->getDibs()->numAvailable(); ?>&nbsp;of&nbsp;<?php echo $gift->getDibs()->numPossible(); ?>)&nbsp;available</small></h4>
		</div>
		<div class="modal-body">
			<?php if ($gift->getDibs() && $gift->getDibs()->dibs) : ?>
				<div class="list-group">
					<?php foreach ($gift->getDibs()->dibs as $dib) : ?>
						<div class="list-group-item">
							<?php
							$user = new User($db);
							$dibUser = $user->set("userId", $dib->get("userId"))->find(1, array("firstName, lastName, userId"));
							if ($me->isAlso($dibUser)) {
								echo "<strong>You</strong> dibbed ".$dib->get("quantity")." <small class=\"text-muted pull-right\">" . $app->friendlyDate($dib->get("timestamp")) . "</small>";
							} 
							else if ($dib->get("isPrivate") && $me->get("userId") !== $gift->get("userId")) {
								echo "<strong>Anonymous</strong> dibbed ".$dib->get("quantity") . "<small class=\"text-muted pull-right\">" . $app->friendlyDate($dib->get("timestamp")) . "</small>";
							} 
							else {
								echo "<strong><a href=\"".$app->config("page", "profile", array( "userId" => $dibUser->get("userId") ))."\">".$dibUser->fullName()."</a></strong> dibbed ".$dib->get("quantity")." <small class=\"text-muted pull-right\">" . $app->friendlyDate($dib->get("timestamp")) . "</small>";
							}
							?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="alert alert-info">No dibs have been committed yet.</div>
			<?php endif; ?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>