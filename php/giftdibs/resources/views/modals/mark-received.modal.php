<?php 
$form = new Form(array(
	"slug" => "mark-received",
	"cssClass" => "mark-received-form",
	"heading" => "Mark received",
	"action" => $app->config("ajax","mark-received")
));
?>
<div class="modal-dialog">
	<div class="modal-content">
		<?php $form->start(); ?>
			<input type="hidden" name="giftId" value="<?php echo $gift->get("giftId"); ?>">
			<input type="hidden" name="signature" value="<?php echo $me->createSignature($gift->get("giftId")); ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $form->getHeading(); ?></h4>
			</div>
			<div class="modal-body">
				<?php $form->alert(); ?>
				Are you sure you want to mark the gift <strong><?php echo $gift->get("name"); ?></strong> as received?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary btn-submit" data-loading-text="Processing...">Yes, I received this gift</button>
			</div>
		<?php $form->stop(); ?>
	</div>
</div>