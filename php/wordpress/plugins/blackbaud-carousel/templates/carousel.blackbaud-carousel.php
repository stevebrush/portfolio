<?php if (count($data['slides']) > 0) : ?>
    <?php $is_image_backgrounds = (isset($data['image_backgrounds']) && $data['image_backgrounds'] === "on" && isset($post->thumbnail)); ?>
    <?php if ($data['transition_type'] === "fade") : ?>
    	<style>
    		.carousel-fade > .carousel-inner > .item { opacity: 0; -webkit-transition: {{settings.transitionSpeed}}ms linear; -moz-transition: {{settings.transitionSpeed}}ms linear; -webkit-transition: {{settings.transitionSpeed}}ms linear; -o-transition: {{settings.transitionSpeed}}ms linear; transition: {{settings.transitionSpeed}}ms linear; -webkit-transition-property: opacity; -moz-transition-property: opacity; -webkit-transition-property:opacity; -o-transition-property:opacity; transition-property: opacity; }
    		.carousel-fade > .carousel-inner > .active { opacity: 1; }
    		.carousel-fade > .carousel-inner > .active.left,
    		.carousel-fade > .carousel-inner > .active.right { left: 0; opacity: 0; z-index: 1; }
    		.carousel-fade > .carousel-inner > .next.left,
    		.carousel-fade > .carousel-inner > .prev.right { opacity: 1; }
    		.carousel-fade > .carousel-control { z-index: 2; }
    		.carousel div[data-href] { cursor: pointer; }
    	</style>
    <?php endif; ?>

    <div id="simple-carousel-<?php echo $data['id']; ?>" class="carousel slide<?php echo ($data['transition_type'] === "fade") ? ' carousel-fade' : ''; ?><?php echo ($is_image_backgrounds) ? ' carousel-image-backgrounds' : ''; ?>">
    	<ol class="carousel-indicators">
    		<?php foreach ($data['slides'] as $i => $slide) : ?>
    			<li data-target="#simple-carousel-<?php echo $data['id']; ?>" data-slide-to="<?php echo $i; ?>"<?php echo ($i == $data['starting_index']) ? ' class="active"' : ''; ?>></li>
    		<?php endforeach; ?>
    	</ol>
    	<div class="carousel-inner">
    		<?php foreach ($data['slides'] as $post) : ?>
    			<div
        			class="<?php echo $post->fields['css_class'][0]; ?> item<?php echo ($i == $data['starting_index']) ? ' active' : ''; ?><?php echo ($is_image_backgrounds) ? ' item-background-image' : ''; ?>"
                    <?php if ($is_image_backgrounds) : ?>
                        style="background-image:url(<?php echo $post->thumbnail; ?>);background-repeat:no-repeat;"
                    <?php endif; ?>>
    				<?php if (!$is_image_backgrounds) : ?>
					    <img src="<?php echo $post->thumbnail; ?>" alt="">
    				<?php endif; ?>
    				<div class="carousel-caption">
    					<?php if (isset($post->post_title)) : ?>
    						<h1><?php echo $post->post_title; ?></h1>
    					<?php endif; ?>
    					<?php if (!empty($post->fields['subtitle'][0])) : ?>
    						<h2><?php echo $post->fields['subtitle'][0]; ?></h2>
    					<?php endif; ?>
    					<div class="carousel-caption-body">
                            <?php if (!empty($post->fields['blurb'][0])) : ?>
        						<div class="carousel-caption-blurb">
            						<?php echo $post->fields['blurb'][0]; ?>
            				    </div>
        					<?php endif; ?>
        					<?php if (!empty($post->fields['primary_button_label'][0])) : ?>
        						<div class="carousel-call-to-action">
    								<a class="btn btn-lg btn-primary" href="<?php echo $post->fields['primary_button_link'][0]; ?>">
        								<?php echo $post->fields['primary_button_label'][0]; ?>
        				            </a>
    							</div>
        					<?php endif; ?>
    						<?php if (!empty($post->fields['secondary_button_label'][0])) : ?>
        						<div class="carousel-call-to-action">
    								<a class="btn btn-lg btn-default" href="<?php echo $post->fields['secondary_button_link'][0]; ?>">
        								<?php echo $post->fields['secondary_button_label'][0]; ?>
        				            </a>
    							</div>
        					<?php endif; ?>
    					</div>
    				</div>
    			</div>
    		<?php endforeach; ?>
    	</div>
    	<a class="left carousel-control" href="#simple-carousel-<?php echo $data['id']; ?>" data-slide="prev">
    		<?php echo stripslashes($data['navigation_previous']); ?>
    	</a>
    	<a class="right carousel-control" href="#simple-carousel-<?php echo $data['id']; ?>" data-slide="next">
    		<?php echo stripslashes($data['navigation_next']); ?>
    	</a>
    </div>
<?php endif; ?>
