<?php
$form = new Form(array(
	"slug" => "edit-gift-guide",
	"cssClass" => "edit-gift-guide-form",
	"heading" => "Gift-giving guide",
	"orientation" => "horizontal",
	"action" => "{$app->config('ajax','edit-gift-guide')}"
));

$me->getInputs();
$interests = new FormField($form, $me->getField("interests"));
$favoriteStores = new FormField($form, $me->getField("favoriteStores"));
$shirtSize = new FormField($form, $me->getField("shirtSize"));
$shoeSize = new FormField($form, $me->getField("shoeSize"));
$pantSize = new FormField($form, $me->getField("pantSize"));
$hatSize = new FormField($form, $me->getField("hatSize"));
$ringSize = new FormField($form, $me->getField("ringSize"));
$signature = new FormField($form, array(
	"type" => "hidden",
	"name" => "signature",
	"value" => $me->createSignature("edit-gift-guide")
));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Save changes",
	"fieldClass" => "btn-primary"
));
$form->start();
	$form->heading();
	?>
	<div class="form-body">
		<?php
		$form->alert();
		$signature->render("field");
		$interests->render();
		$favoriteStores->render();
		$shirtSize->render();
		$shoeSize->render();
		$pantSize->render();
		$hatSize->render();
		$ringSize->render();
		?>
		<p class="alert alert-info"><span class="label label-info">Note:</span> Gift guide information is only displayed to your followers, and helps them get you something that may not be on your wish list!</p>
	</div>
	<div class="form-footer">
		<button class="btn btn-primary btn-submit" data-loading-text="Processing..." type="submit">Save Changes</button>
	</div>
	<?php
$form->stop();