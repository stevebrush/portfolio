<?php
$form = new Form(array(
	"slug" => "email-preferences",
	"cssClass" => "email-preferences-form",
	"heading" => "Email preferences",
	"orientation" => "horizontal",
	"action" => $app->config('ajax','email-preferences')
));
$submitButton = new FormField($form, array(
	"type" => "submit",
	"label" => "Save Changes",
	"fieldClass" => "btn-primary"
));
$notificationType = new NotificationType($db);
$notificationTypes = $notificationType->find();
$choices = array();
if ($notificationTypes) {
	foreach ($notificationTypes as $nt) {
		$choices[] = array(
			"label" => $nt->get("label"),
			"value" => $nt->get("notificationTypeId"),
			"selected" => $me->acceptsEmailFor($nt->get("slug"))
		);
	}
}
$checkboxes = new FormField($form, array(
	"type" => "checkboxGroup",
	"name" => "emailAlerts",
	"label" => "You will receive an email when",
	"choices" => $choices
));
$form->start(); 
	$form->heading();
	?>
	<div class="form-body">
		<?php
		$form->alert();
		?>
		<input type="hidden" name="userId" value="<?php echo $me->get("userId"); ?>">
		<input type="hidden" name="signature" value="<?php echo $me->createSignature("email-preferences"); ?>">
		<div class="form-group">
			<label class="col-sm-3 control-label">Emails will be sent to:</label>
			<div class="col-sm-9">
				<p class="form-control-static"><?php echo $me->get("emailAddress"); ?> - <a href="<?php echo $app->config('page','account-details'); ?>">change</a></p>
			</div>
		</div>
		<div class="gd-email-preferences">
			<?php $checkboxes->render(); ?>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<p class="checkbox form-control-static">
					<label><input type="checkbox" data-gd-check-all data-target=".gd-email-preferences"> Check all</label></p>
			</div>
		</div>
	</div>
	<div class="form-footer">
		<?php $submitButton->render("field"); ?>
	</div>
	<?php
$form->stop();