<?php 
$oFrm = new Form(array(
	"slug" => "delete-wish-list",
	"cssClass" => "delete-wish-list-form",
	"heading" => "Delete wish list",
	"action" => $app->config('ajax','delete-wish-list')
));
$wishList = new WishList($db);
$wishList = $wishList->set("wishListId", $_GET["wishListId"])->find(1);
?>
<div class="modal-dialog">
	<div class="modal-content">
		<?php $oFrm->start(); ?>
			<input type="hidden" name="wishListId" value="<?php echo $wishList->get("wishListId"); ?>">
			<input type="hidden" name="signature" value="<?php echo $me->createSignature($wishList->get("wishListId")); ?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $oFrm->getHeading(); ?></h4>
			</div>
			<div class="modal-body">
				<?php $oFrm->alert(); ?>
				Are you sure you want to delete the wish list <strong><?php echo $wishList->get("name"); ?></strong>?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary btn-submit" data-loading-text="Processing...">Confirm delete</button>
			</div>
		<?php $oFrm->stop(); ?>
	</div>
</div>