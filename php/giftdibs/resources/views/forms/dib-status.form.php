<?php
$form = new Form(array(
	"slug" => "dib-status",
	"cssClass" => "dib-status-form",
	"heading" => "",
	"action" => "{$app->config('ajax','dib-status')}"
));

$dibStatusId = $dib->get("dibStatusId");
$dibStatus = new DibStatus($db);
$dibStatus = $dibStatus->set("dibStatusId", $dibStatusId)->find(1);

switch ($dibStatus->get("slug")) {
	
	case "pending":
	$status = new FormField($form, array(
		"type" => "select",
		"name" => "dibStatusId",
		"label" => "Status:",
		"choices" => array(
			array("label" => "Pending review", "value" => "3", "selected" => "true")
		),
		"helplet" => "Delivery confirmation sent to {$u->get('firstName')}",
		"disabled" => "true"
	));
	break;
	
	case "delivered":
	$status = new FormField($form, array(
		"type" => "static",
		"label" => "Status:",
		"value" => "Delivered"
	));
	break;
	
	default:
	$status = new FormField($form, array(
		"type" => "select",
		"name" => "dibStatusId",
		"label" => "Status:",
		"choices" => array(
			array("label" => "Reserved", "value" => "1", "selected" => ($dibStatusId == "1") ? "true" : "false"),
			array("label" => "Purchased", "value" => "2", "selected" => ($dibStatusId == "2") ? "true" : "false"),
			array("label" => "Delivered", "value" => "4", "selected" => ($dibStatusId == "4") ? "true" : "false")
		)
	));
	break;
	
}
$form->start();
	?>
	<input type="hidden" name="dibId" value="<?php echo $dib->get('dibId'); ?>">
	<input type="hidden" name="signature" value="<?php echo $me->createSignature($dib->get('dibId')); ?>">
	<?php
	$status->render();
$form->stop();