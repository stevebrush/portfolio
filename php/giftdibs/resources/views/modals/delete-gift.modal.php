<?php 
if (!isset($gift)) return;
$oFrm = new Form(array(
	"slug" => "delete-gift",
	"cssClass" => "delete-gift-form",
	"heading" => "Delete gift",
	"action" => $app->config('ajax','delete-gift')
));
?>
<div class="modal-dialog">
	<div class="modal-content">
		<?php $oFrm->start(); ?>
			<input type="hidden" name="giftId" value="<?php echo $gift->get("giftId"); ?>">
			<input type="hidden" name="signature" value="<?php echo $me->createSignature($gift->get("giftId")); ?>">
			<input type="hidden" name="redirect" value="<?php echo $app->config("page","wish-list",array("wishListId"=>$gift->get("wishListId"))); ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $oFrm->getHeading(); ?></h4>
			</div>
			<div class="modal-body">
				<?php echo $oFrm->alert(); ?>
				Are you sure you want to delete the gift <strong><?php echo $gift->get("name"); ?></strong>?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary btn-submit" data-loading-text="Processing...">Confirm delete</button>
			</div>
		<?php $oFrm->stop(); ?>
	</div>
</div>