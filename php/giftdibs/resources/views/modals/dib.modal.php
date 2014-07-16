<?php
// get dib variable from surroundings...
if (!isset($myDib)) {
	$myDib = $dib;
}
$dibQuantity = 0;
if ($myDib) {
	$dibQuantity = $myDib->get("quantity");
	$dibId = $myDib->get("dibId");
}
$form = new Form(array(
	"slug" => "dib-gift",
	"cssClass" => "dib-gift-form",
	"heading" => ($myDib) ? "Edit dib" : "Dib this gift",
	"orientation" => "horizontal",
	"action" => $app->config("ajax","dib-gift")
));
$numAvailable = ($myDib) ? ($gift->dibs()->numAvailable() + $dibQuantity) : $gift->dibs()->numAvailable();
$choices = array();
for ($i=0; $i<$numAvailable; $i++) {
	$choices[] = array("label" => ($i+1), "value" => ($i+1), "selected" => ($dibQuantity == $i+1) ? "true" : "false");
}
$quantity = new FormField($form, array(
	"type" => "select",
	"name" => "quantity",
	"label" => "How many will you deliver?",
	"choices" => $choices,
	"required" => "true"
	//"helplet" => "{$numAvailable} of {$gift->dibs()->numPossible()} remaining"
));
$dateProjected = new FormField($form, array(
	"type" => "text",
	"name" => "dateProjected",
	"label" => "When will you deliver this gift?",
	"value" => ($myDib) ? $app->formatDate($myDib->get("dateProjected")) : "12/25/".date("Y"),
	"helplet" => "mm/dd/yyyy"
));
$isPrivate = new FormField($form, array(
	"type" => "checkbox",
	"name" => "isPrivate",
	"label" => "Make this dib anonymous",
	"helplet" => "People will see that this gift is dibbed, but they won't know who dibbed it.",
	"checked" => ($myDib && $myDib->get("isPrivate")) ? "true" : "false"
));
?>
<div class="modal-dialog">
	<div class="modal-content">
		<?php $form->start(); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $form->getHeading(); ?></h4>
			</div>
			<div class="modal-body">
				<?php if ($myDib) : ?>
					<input type="hidden" name="dibId" value="<?php echo $dibId; ?>">
					<input type="hidden" name="signature" value="<?php echo $me->createSignature($dibId); ?>">
				<?php endif; ?>
				<input type="hidden" name="giftId" value="<?php echo $gift->get("giftId"); ?>">
				<input type="hidden" name="redirect" value="<?php echo $app->currentUrl(); ?>">
				<?php $form->alert(); ?>
				<div class="media product-card">
					<div class="thumbnail pull-left">
						<img src="<?php $thumbnail = $gift->getThumbnail(); echo $thumbnail->size("sm")->get("src"); ?>">
					</div>
					<div class="media-body">
						<h4 class="media-heading"><?php echo $gift->get("name"); ?></h4>
						<p><span class="price"><?php echo $app->formatPrice($gift->get("price")); ?></span></p>
					</div>
				</div>
				<?php 
				$quantity->render();
				$dateProjected->render();
				?>
				<?php if ($wishList->getType() != "Registry") : ?>
					<?php $isPrivate->render(); ?>
				<?php else : ?>
					<p class="alert alert-info"><span class="label label-info">Note:</span> Since this is a registry, your dibs will be publicly viewable by <?php echo $they->get("firstName"); ?> and <?php echo $they->pronoun("his"); ?> followers.</p>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<?php if ($myDib) : ?>
					<a href="<?php echo $app->config("ajax", "delete-dib"); ?>" data-loading-text="Processing..." class="btn btn-danger btn-data pull-left" data-signature="<?php echo $me->createSignature($dibId); ?>" data-dib-id="<?php echo $dibId; ?>" data-redirect="<?php echo $app->currentUrl(); ?>"><small class="glyphicon glyphicon-trash"></small>&nbsp;&nbsp;Remove Dib</a>
					<button type="button" class="btn btn-primary btn-submit" data-loading-text="Processing...">Update</button>
				<?php else : ?>
					<button type="button" class="btn btn-primary btn-submit" data-loading-text="Processing...">Dib this</button>
				<?php endif; ?>
			</div>
		<?php $form->stop(); ?>
	</div>
</div>