<?php 
$form = new Form(array(
	"slug" => "delete-account",
	"cssClass" => "delete-account-form",
	"heading" => "Delete Your Account",
	"action" => "{$app->config('ajax','delete-account')}"
));
?>
<?php $form->start(); ?>
	<?php $form->heading(); ?>
	<div class="form-body">
		<?php $form->alert(); ?>
		<input type="hidden" name="signature" value="<?php echo $me->createSignature($session->getUserId()); ?>">
		<p>Are you sure you want to <strong>permanently delete</strong> your account?</p>
		<div class="alert alert-danger">This action cannot be undone!</div>
	</div>
	<div class="form-footer">
		<button type="button" class="btn btn-primary btn-danger" data-loading-text="Processing...">Yes, delete my account forever.</button>
	</div>
<?php $form->stop(); ?>