<?php
$userId = $session->getUserId();
$holiday = new Holiday($db);
$holidays = $holiday->find(null, array("holidayId", "slug", "label", "month", "day", "notificationSent"), null, " ORDER BY month DESC");
$form = new Form(array(
	"slug" => "holidays",
	"cssClass" => "holidays-form",
	"heading" => "Holidays",
	"orientation" => "horizontal",
	"action" => $app->config("ajax", "edit-holidays")
));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Save Changes",
	"fieldClass" => "btn-primary"
));
$form->start();
	$form->heading();
	?>
	<div class="form-body">
		<p>For each holiday below, GiftDibs will send you an email two weeks in advance so that you can be prepared to get a gift for someone you care about.</p>
		<div class="panel panel-default">
			<?php $form->alert(); ?>
			<input type="hidden" name="signature" value="<?php echo $me->createSignature("edit-holidays"); ?>">
			<?php if ($holidays) : ?>
				<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Holiday</th>
							<th>Date</th>
							<th><input class="pull-right" type="checkbox" data-gd-check-all data-target=".gd-holiday-preferences"></th>
						</tr>
					</thead>
					<tbody class="gd-holiday-preferences">
						<?php foreach ($holidays as $holiday) : ?>
							<?php
							$holidayId = $holiday->get("holidayId");
							$holiday_user = new Holiday_User($db);
							$checked = $holiday_user->set(array(
								"userId" => $userId,
								"holidayId" => $holidayId
							))->find(1);
							?>
							<tr>
								<td><label class="form-label" for="holiday-<?php echo $holiday->get("slug"); ?>"><?php echo $holiday->get("label"); ?></label></td>
								<td><?php echo date("F j", strtotime($holiday->get("month") . "/" . $holiday->get("day") . "/" . date("Y"))); ?></td>
								<td><input class="pull-right" type="checkbox" id="holiday-<?php echo $holiday->get("slug"); ?>" value="<?php echo $holidayId; ?>" name="holidays[]"<?php echo ($checked) ? " checked": ""; ?>></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="form-footer">
		<?php $submit->render("field"); ?>
	</div>
<?php $form->stop(); ?>