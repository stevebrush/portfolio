<?php
$form = new Form(array(
	"slug" => "new-comment",
	"heading" => "Comment",
	"cssClass" => "comment-form",
	"action" => $app->config("ajax", "new-comment")
));
$comment = new Comment($db);
$comment->getInputs();
$content = new FormField($form, $comment->getField("content"));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Comment",
	"fieldClass" => "btn-primary"
));
$form->start();
	?>
	<input type="hidden" name="giftId" value="<?php echo $gift->get("giftId"); ?>">
	<input type="hidden" name="signature" value="<?php echo $me->createSignature($gift->get("giftId")); ?>">
	<input type="hidden" name="redirect" value="<?php echo $app->currentUrl(); ?>">
	<?php
	$form->alert();
	$content->render();
	//$submit->render();
$form->stop();