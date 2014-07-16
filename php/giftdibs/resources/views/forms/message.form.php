<?php
$form = new Form(array(
	"slug" => "new-message",
	"heading" => "New message",
	"cssClass" => "message-form",
	"action" => $app->config("ajax","new-message")
));
$recipientId = (isset($_GET["userId"])) ? $_GET["userId"] : 0;
$contentText = (isset($_GET["content"])) ? $_GET["content"] : "";
$redirect = (isset($_GET["redirect"])) ? $_GET["redirect"] : null;
$message = new Message($db);
$message->getInputs();
$content = new FormField($form, $message->getField("content"));
$content->setLabel("");
$content->setValue($contentText);
$friendsChoices = array();
$leaders = $me->getLeaders();
if ($leaders) {
	foreach ($leaders as $leader) {
		$leaderId = $leader->get("userId");
		$selected = ($recipientId === $leaderId) ? "true" : "false";
		$friendsChoices[] = array(
			"label" => $leader->fullName(),
			"value" => $leaderId,
			"selected" => $selected
		);
	}
}
$participants = new FormField($form, array(
	"type" => "checkboxGroup",
	"name" => "userIds",
	"label" => "Send to:",
	"required" => "true",
	"choices" => $friendsChoices
));
$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Send",
	"fieldClass" => "btn-primary"
));
$form->start();
	?>
	<div class="panel panel-default">
		<div class="modal-header">
			<h4 class="modal-title"><?php echo $form->getHeading(); ?></h4>
		</div>
		<div class="modal-body">
			<input type="hidden" name="signature" value="<?php echo $me->createSignature("new-message"); ?>">
			<?php if (isset($redirect)) : ?>
				<input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
			<?php endif; ?>
			<?php 
			$form->alert();
			$participants->render();
			$content->render();
			?>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" type="button">Cancel</button>
			<?php $submit->render("field"); ?>
		</div>
	</div>
	<?php
$form->stop();