<?php 
$oWL = new WishList($db);
$oWL->getInputs();
$oWLs = $oWL->set(array(
	"userId" => $session->getUserId()
))->find();

$form = new Form(array(
	"slug" => "add-to-wish-list",
	"cssClass" => "add-to-wish-list-form",
	"heading" => "Add to wish list",
	"action" => $app->config("ajax","new-gift")
));

$wishListName = new FormField($form, $oWL->getField("name"));
$wishListName->set(array(
	"name" => "newWishListName",
	"label" => "New wish list name",
	"fieldClass" => "input-new-wish-list-name"
));

//if (isset($product)) $gift = $product;

$productId 		= $product->get("productId");
$productIdType	= $product->get("productIdType");
$name 			= substr($product->get("name"), 0, 255);
$url 			= $product->get("url");
$price 			= $app->formatPrice($product->bestPrice());
$thumbnail 		= $product->getThumbnail();
$thumbnailUrl 	= $thumbnail->size("original")->get("src");
?>
<div class="modal-dialog">
	<div class="modal-content">
		<?php $form->start(); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $form->getHeading(); ?></h4>
			</div>
			<div class="modal-body">
				<?php $form->alert(); ?>
				<input type="hidden" name="signature" value="<?php echo $me->createSignature("new-gift"); ?>">
				<input type="hidden" name="productId" value="<?php echo $productId; ?>">
				<input type="hidden" name="productIdType" value="<?php echo $product->get("productIdType"); ?>">
				<input type="hidden" name="name" value="<?php echo $name; ?>">
				<input type="hidden" name="url" value="<?php echo $url; ?>">
				<input type="hidden" name="price" value="<?php echo $price; ?>">
				<input type="hidden" name="gradeId" value="1">
				<input type="hidden" name="quantity" value="1">
				<input type="hidden" name="thumbnailUrl" value="<?php echo $thumbnailUrl; ?>">
				<input type="hidden" name="redirect" value="<?php echo $app->currentUrl(); ?>">
				<div class="well media gift-preview">
					<div class="thumbnail pull-left">
						<img src="<?php echo $thumbnail->size("sm")->get("src"); ?>" class="<?php echo $thumbnail->size("sm")->get("class"); ?>">
					</div>
					<div class="media-body">
						<h4 class="media-heading"><?php echo $name; ?></h4>
						<span class="price"><?php echo $price; ?></span>
					</div>
				</div>
				
				<?php if ($oWLs) : ?>
					<div class="form-group">
						<div class="radio">
							<label><input type="radio" name="wishListSwitch" checked="checked" value="current">My wish lists</label>
						</div>
						<div class="current-wish-list-name-container">
							<select name="wishListId" class="form-control">
								<?php foreach ($oWLs as $list) : ?>
									<option value="<?php echo $list->get("wishListId"); ?>"><?php echo $list->get("name"); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>
				<div class="form-group">
					<div class="radio">
						<label><input type="radio" name="wishListSwitch" value="new">New wish list</label>
					</div>
					<div class="new-wish-list-name-container">
						<?php $wishListName->render("field"); ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary btn-submit" data-loading-text="Processing...">Add</button>
			</div>
		<?php $form->stop(); ?>
	</div>
</div>