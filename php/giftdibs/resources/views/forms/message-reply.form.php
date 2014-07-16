<?php
$form = new Form(array(
	"slug" => "new-message-reply",
	"heading" => "Reply",
	"cssClass" => "message-reply-form",
	"action" => $app->config("ajax", "new-message-reply")
));
$reply = new MessageReply($db);
$reply->getInputs();
$content = new FormField($form, $reply->getField("content"));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Send",
	"fieldClass" => "btn-primary"
));
$form->start();
	?>
	<input type="hidden" name="messageId" value="<?php echo $thisMessage->get("messageId"); ?>">
	<input type="hidden" name="signature" value="<?php echo $me->createSignature($thisMessage->get("messageId")); ?>">
	<input type="hidden" name="redirect" value="<?php echo $app->currentUrl(); ?>">
	<?php
	$form->alert();
	$content->render();
	//$submit->render();
$form->stop();