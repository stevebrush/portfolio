<?php

$thumbnail = $gift->getThumbnail();

$followers = $me->getFollowers();

$form = new Form(array(
	"slug" => "new-gift",
	"heading" => (empty($giftId)) ? "New Gift" : "Edit Gift",
	"cssClass" => "gift-form",
	"orientation" => "horizontal",
	"action" => $app->config("ajax","new-gift"),
	"allowUpload" => "true"
));

$gift->getInputs();

$name 			= new FormField( $form, $gift->getField("name"));
$url 			= new FormField( $form, $gift->getField("url"));
$price 			= new FormField( $form, $gift->getField("price"));
$notes 			= new FormField( $form, $gift->getField("notes"));
$quantity 		= new FormField( $form, $gift->getField("quantity"));
$wishListIds 	= new FormField( $form, $gift->getField("wishListId"));
$priority 		= new FormField( $form, $gift->getField("priorityId"));
$grade 			= new FormField( $form, $gift->getField("gradeId"));

$wishList = new WishList($db);
$wishList->getInputs();
$wishListName = new FormField($form, $wishList->getField("name"));
$wishListName->set(array(
	"name" => "newWishListName",
	"label" => "New wish list name",
	"fieldClass" => "input-new-wish-list-name"
));

$submit = new FormField($form, array(
	"type" => "submit",
	"label" => "Submit",
	"fieldClass" => "btn-primary"
));
?>
<div class="form-container">
	<?php 
	$form->start(); 
		$form->heading();
		$form->alert(); 
		?>
		<?php if (!empty($giftId)) : ?>
			<input type="hidden" name="signature" value="<?php echo $me->createSignature($gift->get("giftId")); ?>">
			<input type="hidden" name="giftId" value="<?php echo $gift->get("giftId"); ?>">
		<?php else : ?>
			<input type="hidden" name="signature" value="<?php echo $me->createSignature("new-gift"); ?>">
		<?php endif; ?>
		<input type="hidden" name="productId" value="<?php echo $gift->get("productId"); ?>">
		<input type="hidden" name="productIdType" value="<?php echo $gift->get("productIdType"); ?>">
		<div class="form-body">
			<?php // Wish List ?>
			<div class="form-group">
				<label class="col-sm-2 control-label">Wish List:</label>
				<div class="col-sm-10">
					<div class="radio-extend" data-gd-radio-tabs="">
						<div class="radio-extend-heading">
							<div class="radio active">
								<label>
									<input type="radio" name="wishListSwitch" data-target="#gd-wish-list-existing" checked="">
									Existing:
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="wishListSwitch" data-target="#gd-wish-list-new">
									New:
								</label>
							</div>
						</div>
						<div class="radio-extend-body">
							<div class="form-control-container">
								<div class="gd-tab-content gd-active" id="gd-wish-list-existing">
									<?php $wishListIds->render("field"); ?>
								</div>
								<div class="gd-tab-content" id="gd-wish-list-new">
									<?php $wishListName->render("field"); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php // Gift Name ?>
			<?php $name->render("label"); ?>
			<div id="gift-form-item-search" class="section-add-gifts row">
				<div class="form-group col-sm-11 col-xs-10">
					<?php $name->render("field"); ?>
				</div>
				<div class="form-group col-sm-1 col-xs-2">
					<button type="button" class="btn btn-primary btn-block btn-search" data-loading-text="Wait..."><span class="sr-only">Find</span><span class="glyphicon glyphicon-search"></span></button>
					<button type="button" class="btn btn-danger btn-block btn-cancel"><span class="glyphicon glyphicon-remove"></span></button>
				</div>
				<div class="list-group search-results"></div>
			</div>

			<?php $url->render(); ?>
			<?php $price->render("field"); ?>
			<?php $notes->render(); ?>
			
			<?php // Image Uploader ?>
			<div class="form-group image-uploader">
				<label class="control-label">Thumbnail</label>
				<div class="media">
					<div id="thumbnail-image-container" class="pull-left">
						<?php $thumbnail = $gift->getThumbnail(); ?>
						<?php if ($thumbnail && $thumbnail->get("imageId")) : ?>
							<div id="original-thumbnail" class="thumbnail"><img src="<?php echo $thumbnail->size("md")->get("src"); ?>"></div>
						<?php endif; ?>
					</div>
					<div id="thumbnail-controls" class="media-body">
					
						<p id="btn-get-from-external-link"><a class="btn btn-default btn-xs" href="#" data-loading-text="Loading..."><span class="glyphicon glyphicon-import"></span> Get from external link</a></p>
						<p id="btn-cancel-external-link"><a class="btn btn-default btn-xs" href="#"><span class="glyphicon glyphicon-remove"></span> Cancel</a></p>
						<div id="gift-thumbnail-url" class="form-group">
							<label class="control-label">Image URL</label>
							<input class="form-control" type="text" name="thumbnailUrl" value="">
						</div>
						
						<p id="btn-remove-thumbnail"><a class="btn btn-default btn-xs" href="#"><span class="glyphicon glyphicon-trash"></span> Remove thumbnail</a></p>
						<p id="btn-undo-remove-thumbnail"><a class="btn btn-danger btn-xs" href="#"><span class="glyphicon glyphicon-remove-circle"></span> Undo remove</a></p>
						<div id="remove-thumbnail-form-group" class="checkbox"><label><input type="checkbox" id="remove-thumbnail-checkbox" name="removeThumbnailCheckbox" value="yes"> Remove image?</label></div>
						
						<input id="thumbnail-input" type="file" name="thumbnail">
					</div>
				</div>
			</div>
			
			<?php $priority->render(); ?>
			<?php $grade->render(); ?>
			<?php $quantity->render(); ?>
			
			<div class="alert alert-info">
				<p><span class="label label-info">Note</span> Gifts inherit the privacy settings of their respective wish lists.</p>
			</div>
		</div>
		<div class="form-footer">
			<?php $submit->render("field"); ?>
		</div>
	<?php $form->stop(); ?>
</div>