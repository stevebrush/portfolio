<?php
$form = new Form(array(
	"slug" => "confirm-gift-given",
	"heading" => "",
	"cssClass" => "confirm-gift-given-form",
	"action" => $app->config("ajax","confirm-gift-given")
));
$form->start();
	?>
	<input type="hidden" name="notificationId" value="<?php echo $notification->get("notificationId"); ?>">
	<input type="hidden" name="followerId" value="<?php echo $notification->get("followerId"); ?>">
	<input type="hidden" name="giftId" value="<?php echo $notification->get("giftId"); ?>">
	<input type="hidden" name="dibId" value="<?php echo $dib->get("dibId"); ?>">
	<input type="hidden" name="signature" value="<?php echo $me->createSignature($dib->get("dibId")); ?>">
	<input type="hidden" name="redirect" value="<?php echo $app->config("page", "notifications"); ?>">
	<input type="checkbox" name="userDidDeliver" class="confirm-yes-no-checkbox">
	<button type="button" class="btn btn-success btn-sm btn-confirm-yes" data-loading-text="Wait..."><small class="glyphicon glyphicon-ok"></small>&nbsp;&nbsp;Yes</button>
	<button type="button" class="btn btn-danger btn-sm btn-confirm-no" data-loading-text="Wait..."><small class="glyphicon glyphicon-remove"></small>&nbsp;&nbsp;No</button>
	<?php
$form->stop();