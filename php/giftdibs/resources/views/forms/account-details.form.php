<?php
$form = new Form(array(
	"slug" => "account-details",
	"cssClass" => "account-details-form",
	"heading" => "Account details",
	"orientation" => "horizontal",
	"action" => $app->config('ajax','account-details')
));

$me->getInputs();

/* Database Fields */
$email = new FormField($form, $me->getField("emailAddress"));
$currency = new FormField($form, $me->getField("currencyId"));
$signature = new FormField($form, array(
	"type" => "hidden",
	"name" => "signature",
	"value" => $me->createSignature("account-details")
));
$changePassword = new FormField($form, array(
	"type" => "static",
	"label" => "Password",
	"value" => "<a href=\"{$app->config('page','change-password')}\">Change password...</a>"
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
		<?php
		$form->alert();
		$signature->render("field");
		$email->render(); 
		$changePassword->render();
		//$currency->render();
		?>
	</div>
	<div class="form-footer">
		<?php $submit->render("field"); ?>
	</div>
	<?php
$form->stop();