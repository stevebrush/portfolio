<?php 
if (!isset($me)) return;
$oFrm = new Form(array(
	"slug" => "delete-account",
	"cssClass" => "delete-account-form",
	"heading" => "Delete your account",
	"action" => "{$app->config('ajax','delete-account')}"
));
?>
<div class="modal-dialog">
	<div class="modal-content">
		<?php $oFrm->start(); ?>
			<input type="hidden" name="signature" value="<?php echo $me->createSignature($session->getUserId()); ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $oFrm->heading(); ?></h4>
			</div>
			<div class="modal-body">
				<?php $oFrm->alert(); ?>
				<p>Are you sure you want to <strong>permanently delete</strong> your account?</p>
				<div class="alert alert-danger">This action cannot be undone!</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary btn-submit" data-loading-text="Processing...">Yes, delete my account forever</button>
			</div>
		<?php $oFrm->stop(); ?>
	</div>
</div>