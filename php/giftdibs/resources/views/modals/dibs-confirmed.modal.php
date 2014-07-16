<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">Confirmed Dibs for <strong><?php echo $gift->get("name"); ?></strong></h4>
		</div>
		<div class="modal-body">
			<?php $numUnconfirmed = 0; ?>
			<?php if ($gift->dibs() && $gift->dibs()->dibs) : ?>
				<div class="list-group">
					<?php foreach ($gift->dibs()->dibs as $dib) : ?>
						<div class="list-group-item">
							<?php
							$user = new User($db);
							$dibUser = $user->set("userId", $dib->get("userId"))->find(1, array("firstName, lastName, userId"));
							
							if ($dib->get("dibStatusId") === "4") {
								if ($me->isAlso($dibUser)) {
									echo "<strong>You</strong> dibbed ".$dib->get("quantity")." <small class=\"text-muted pull-right\">" . $app->friendlyDate($dib->get("timestamp")) . "</small>";
								} 
								else if ($dib->get("isPrivate") && $me->get("userId") !== $gift->get("userId")) {
									echo "<strong>Anonymous</strong> dibbed ".$dib->get("quantity") . "<small class=\"text-muted pull-right\">" . $app->friendlyDate($dib->get("timestamp")) . "</small>";
								} 
								else {
									echo "<strong><a href=\"".$app->config("page", "profile", array( "userId" => $dibUser->get("userId") ))."\">".$dibUser->fullName()."</a></strong> dibbed ".$dib->get("quantity")." <small class=\"text-muted pull-right\">" . $app->friendlyDate($dib->get("timestamp")) . "</small>";
								}
							} else {
								$numUnconfirmed += $dib->get("quantity");
							}
							
							if ($numUnconfirmed) {
								echo "<strong>{$numUnconfirmed}</strong> unconfirmed dibs";
							}
							?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="alert alert-info">No dibs have been confirmed yet.</div>
			<?php endif; ?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>