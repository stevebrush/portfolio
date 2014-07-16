<div id="section-product-detail" class="row">
	<div class="col-sm-5 thumbnail">
		<?php $thumbnail = $product->getThumbnail()->size("lg"); ?>
		<img src="<?php echo $thumbnail->get("src"); ?>" alt="<?php echo $product->get("name"); ?>" class="<?php echo $thumbnail->get("class"); ?>">
	</div>
	<div class="col-sm-7">
		<h1><?php echo $product->get("name"); ?></h1>
		<p><?php echo $product->priceHtml(); ?></p>
		<p>
			<?php if ($product->get("url")) : ?>
				<a href="<?php echo $product->get("url"); ?>" class="btn btn-warning" target="_blank"><small class="glyphicon glyphicon-shopping-cart"></small>&nbsp;&nbsp;Buy now</a>
			<?php endif; ?>
			<?php if ($session->isLoggedIn()) : ?>
				<a href="#add-to-wish-list-modal" data-toggle="modal" class="btn btn-default"><small class="glyphicon glyphicon-list-alt"></small>&nbsp;&nbsp;Add to</a>
				<div id="add-to-wish-list-modal" class="modal">
					<?php include MODAL_PATH."add-to-wish-list.modal.php"; ?>
				</div>
			<?php endif; ?>
		</p>
		<?php if ($description = $product->get("description")) : ?>
			<p><strong>Product description</strong><br><?php echo $description; ?></p>
		<?php endif; ?>
		<p><?php echo $product->urlHtml(); ?></p>
	</div>
</div>