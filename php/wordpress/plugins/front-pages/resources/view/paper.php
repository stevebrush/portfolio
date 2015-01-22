<?php $paper = $data["paper"];?>
<div class="tfp-pane tfp-pane-detail">
	<h2><?php echo $paper["title"]; ?></h2>
	<p>
		<?php if (isset($data["date"])) : ?>
			<?php echo $data["date"]; ?>&nbsp;|&nbsp;
		<?php endif; ?>
		Published in <?php echo $paper["location"]; ?>
	</p>
	<div class="tfp-utility tfp-paper-detail-nav-container">
		<div class="tfp-navigation">
			<ul class="nav nav-pills nav-paper-detail pull-left">
				<li><a href="#" class="tfp-back-button"><span class="fa fa-arrow-left"></span>Back</a></li>
				<?php if ($data ["options"] ["display"] === "detail-topten") : ?>
					<li><a href="<?php echo add_query_arg(array("tfp_display" => "topten"), get_permalink()); ?>"><span class="fa fa-star"></span>Top Ten</a></li>
				<?php else : ?>
					<li><a href="<?php echo $paper["links"]["back"]; ?>"><span class="fa fa-th-large"></span>Gallery</a></li>
				<?php endif; ?>
			</ul>
			<ul class="nav nav-pills nav-paper-detail pull-right">
				<li><a href="#" class="tfp-print"><span class="fa fa-print"></span>Print</a></li>
				<li><a href="<?php echo $paper["links"]["pdf"]; ?>" target="_blank"><span class="fa fa-file-pdf-o"></span>PDF</a></li>
				<li><a href="<?php echo $paper["website"]; ?>" target="_blank"><span class="fa fa-external-link"></span>Web Site</a></li>
			</ul>
		</div>
		<div class="tfp-filters">
			<ul class="nav nav-pills">
				<li class="tfp-nav-prev">
					<?php if (isset ($paper ["links"]) && isset ($paper ["links"] ["prev"])) : ?>
						<a href="<?php echo $paper ["links"] ["prev"]; ?>"><span class="fa fa-chevron-left"></span>Previous</a>
					<?php else : ?>
						<a href="#" disabled class="tfp-disabled"><span class="fa fa-chevron-left"></span>Previous</a>
					<?php endif; ?>
				</li>
				<li class="tfp-nav-next">
					<?php if (isset ($paper ["links"]) && isset ($paper ["links"] ["next"])) : ?>
						<a href="<?php echo $paper ["links"] ["next"]; ?>">Next<span class="fa fa-chevron-right"></span></a>
					<?php else : ?>
						<a href="#" disabled class="tfp-disabled">Next<span class="fa fa-chevron-right"></span></a>
					<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>
	<p class="tfp-thumbnail">
		<a href="<?php echo $paper["images"]["lg"]; ?>">
			<img src="<?php echo $paper["images"]["lg"]; ?>">
		</a>
	</p>
</div>
