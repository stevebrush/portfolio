<?php
$form = new Form(array(
	"slug" => "shipping-address",
	"cssClass" => "shipping-address-form",
	"heading" => "Shipping address",
	"orientation" => "horizontal",
	"action" => "{$app->config('ajax','shipping-address')}"
));
$me->getInputs();
$address1 = new FormField($form, $me->getField("address1"));
$address2 = new FormField($form, $me->getField("address2"));
$city = new FormField($form, $me->getField("city"));
$state = new FormField($form, $me->getField("state"));
$zip = new FormField($form, $me->getField("zip"));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Save changes",
	"fieldClass" => "btn-primary"
));
$form->start();
	echo "<h2>{$form->getHeading()}</h2>";
	$form->alert();
	$address1->render();
	$address2->render();
	$city->render();
	$state->render();
	$zip->render(); ?>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<div class="alert alert-info">
				<span class="label label-info">Note:</span> Your shipping address is only displayed to your followers on wish lists that you specify.
			</div>
		</div>
	</div>
	<?php
	$submit->render();
$form->stop();