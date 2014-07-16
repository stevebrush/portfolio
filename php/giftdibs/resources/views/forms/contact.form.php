<?php
$form = new Form(array(
	"slug" => "feedback",
	"cssClass" => "gd-form-contact",
	"heading" => "Contact {$app->config('app','name')}",
	"action" => "{$app->config('ajax','contact')}"
));

$feedback = new Feedback($db);
$feedback->getInputs();

$reason 	= new FormField($form, $feedback->getField("feedbackReasonId"));
$followUp 	= new FormField($form, $feedback->getField("requestFollowUp"));
$email 		= new FormField($form, $feedback->getField("emailAddress"));
$message 	= new FormField($form, $feedback->getField("message"));
$referrer 	= new FormField($form, $feedback->getField("referrer"));
$referrer->setValue($session->getLastUrl());

$nickname = new FormField($form, array(
	"type" => "text",
	"name" => "nickname",
	"label" => "Nickname",
	"maxLength" => "5"
));

$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Send Feedback",
	"fieldClass" => "btn-primary"
));

$form->start();
	$form->heading();
	?>
	<div class="form-body">
		<?php
		$form->alert();
		$referrer->render();
		$reason->render();
		?>
		<div class="field-nickname">
			<?php $nickname->render(); ?>
		</div>
		<?php $message->render(); ?>
		<?php $followUp->render(); ?>
		<div class="gd-request-follow-up collapse">
			<?php $email->render(); ?>
		</div>
	</div>
	<div class="form-footer">
		<?php $submit->render("field"); ?>
	</div>
	<?php
$form->stop();