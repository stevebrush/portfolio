<?php
$form = new Form(array(
	"slug" => "reminders",
	"cssClass" => "reminders-form",
	"heading" => "Reminders",
	"orientation" => "horizontal",
	"action" => $app->config("ajax", "new-reminder")
));
$notificationCustom = new NotificationTypeCustom($db);
$notificationCustom->getInputs();
$label = new FormField($form, $notificationCustom->getField("label"));
$month = new FormField($form, $notificationCustom->getField("month"));
$day = new FormField($form, $notificationCustom->getField("day"));
$submitButton = new FormField($form, array(
	"type" => "submit",
	"label" => "Create New",
	"fieldClass" => "btn-primary"
));
$form->start();
	$form->heading();
	?>
	<div class="form-body">
		<p>Create a custom notification to alert you two weeks in advance of upcoming gift-giving events, such as your wedding anniversary, or the graduation ceremony of your grandchild.</p>
		<?php if ($reminders = $notificationCustom->set("userId", $session->getUserId())->find()) : ?>
			<ul class="list-group">
			<?php
			$year = date("Y");
			?>
			<?php foreach ($reminders as $reminder) : ?>
				<?php $date = date("F j", strtotime($reminder->get("month") . "/" . $reminder->get("day") . "/" . $year)); ?>
				<li class="list-group-item">
					<?php echo $reminder->get("label"); ?> 
					<small class="text-muted"> - <?php echo $date; ?></small> 
					<a href="<?php echo $app->config("ajax", "delete-reminder"); ?>" class="btn btn-xs btn-default btn-data pull-right" data-loading-text="Loading..." data-signature="<?php echo $me->createSignature($reminder->get("notificationTypeCustomId")); ?>" data-notification-type-custom-id="<?php echo $reminder->get("notificationTypeCustomId"); ?>">Delete</a>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<input type="hidden" name="signature" value="<?php echo $me->createSignature("new-reminder"); ?>">
		<input type="hidden" name="redirect" value="<?php echo $app->currentUrl(); ?>">
		<?php
		$form->alert();
		$label->render();
		$month->render();
		$day->render();
		?>
	</div>
	<div class="form-footer">
		<?php $submitButton->render("field"); ?>
	</div>
	<?php
$form->stop();