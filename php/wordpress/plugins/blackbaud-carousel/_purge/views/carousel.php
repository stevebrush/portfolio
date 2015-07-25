<div id="blackbaudbc_<?php echo $id; ?>" class="carousel slide">
	<ol class="carousel-indicators">
		<?php foreach ($images as $key => $image) : ?>
			<li data-target="#blackbaudbc_<?php echo $id; ?>" data-slide-to="<?php echo $key; ?>" data-interval="<?php echo $atts['interval']; ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>></li>
		<?php endforeach; ?>
	</ol>
	<div class="carousel-inner">
		<?php foreach ($images as $key => $image) : ?>
			<div class="item <?php echo ($key == 0) ? 'active' : ''; ?>" id="<?php echo $image['post_id']; ?>">
				<?php echo $image ['image']; ?>
				<?php if ($atts ['showcaption'] == 'true') : ?>
					<div class="carousel-caption">
						<?php if (isset($image['title'])) : ?>
							<h1><?php echo $image['title']; ?></h1>
						<?php endif; ?>
						<?php if (isset($image['excerpt'])) : ?>
							<h3><?php echo $image['excerpt']; ?></h3>
						<?php endif; ?>
						<?php if (isset($image['content'])) : ?>
							<p><?php echo $image['content']; ?></p>
						<?php endif; ?>
						<?php if (isset($image['btn_link'])) : ?>
							<a class="btn btn-primary" href="<?php echo $image['btn_link']; ?>"><?php echo $image['btn_label']; ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ($atts['showcontrols'] === 'true' && $atts['twbs'] == '3') : ?>
		<a class="left carousel-control" href="#blackbaudbc_<?php echo $id; ?>" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
		<a class="right carousel-control" href="#blackbaudbc_<?php echo $id; ?>" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
	<?php elseif ($atts['showcontrols'] === 'true') : ?>
		<a class="left carousel-control" href="#blackbaudbc_<?php echo $id; ?>" data-slide="prev">‹</a>
		<a class="right carousel-control" href="#blackbaudbc_<?php echo $id; ?>" data-slide="next">›</a>
	<?php endif; ?>
</div>
<script>
jQuery(document).ready(function () {
	jQuery('#blackbaudbc_<?php echo $id; ?>').carousel({
		interval: <?php echo $atts['interval']; ?>
	});
});
</script>
