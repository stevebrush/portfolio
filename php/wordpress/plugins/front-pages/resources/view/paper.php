<?php $paper = $data["paper"]; ?>
<div class="tfp-pane tfp-pane-detail">
	<?php if (isset($data["date"])) : ?>
		<h4><?php echo $data["date"]; ?></h4>
	<?php endif; ?>
	<div class="tfp-utility tfp-paper-detail-nav-container">
		<div class="tfp-navigation">
			<ul class="nav nav-pills nav-paper-detail pull-left">
				<li><a href="<?php echo $paper["links"]["back"]; ?>"><span class="fa fa-arrow-left"></span>Back</a></li>
				<li><a href="/todaysfrontpages/"><span class="fa fa-th-large"></span>Today's Pages</a></li>
				<!--<?php/*?>
				<?php if ($data ["options"] ["display"] === "detail-topten") : ?>
					<li><a href="<?php echo add_query_arg(array("tfp_display" => "topten"), get_permalink()); ?>"><span class="fa fa-star"></span>Top Ten</a></li>
				<?php elseif ($data ["options"] ["display"] === "detail-archive-date") : ?>
					<li><a href="<?php echo add_query_arg(array("tfp_display" => "archive-summary"), get_permalink()); ?>"><span class="fa fa-archive"></span>Archives</a></li>
				<?php else: ?>
					<li><a href="<?php echo $paper["links"]["back"]; ?>"><span class="fa fa-th-large"></span>Today's Pages</a></li>
				<?php endif; ?>
				<?php*/?>
				-->
			</ul>
			<ul class="nav nav-pills">
				<li<?php echo ($data["options"]["display"] === "archive-date" || $data["options"]["display"] === "archive-summary") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "archive-summary"), get_permalink()); ?>"><span class="fa fa-archive"></span>Archives</a></li>
				<?php if (isset($data["showTopTen"]) && $data["showTopTen"] == true) : ?>
					<li<?php echo ($data["options"]["display"] === "topten") ? ' class="active"': ""; ?>><a href="<?php echo add_query_arg(array("tfp_display" => "topten"), get_permalink()); ?>"><span class="fa fa-star"></span>Top Ten</a></li>
				<?php else : ?>
					<li><a href="#" disabled class="tfp-disabled"><span class="fa fa-star"></span>Top Ten</a></li>
				<?php endif; ?>
			</ul>
			<!--
			<ul class="nav nav-pills nav-paper-detail pull-right">
				<li><a href="#" class="tfp-print"><span class="fa fa-print"></span>Print</a></li>
				<li><a href="<?php echo $paper["links"]["pdf"]; ?>" target="_blank"><span class="fa fa-file-pdf-o"></span>PDF</a></li>
				<li><a href="<?php echo $paper["website"]; ?>" target="_blank"><span class="fa fa-external-link"></span>Web Site</a></li>
			</ul>
			-->
		</div>
	</div>
	<h2><?php echo $paper["title"]; ?></h2>
	<p class="tfp-meta">
		<span class="tfp-meta-info">Published in <?php echo $paper["location"]; ?></span>
		<span class="tfp-meta-controls">
			<a href="#" class="tfp-print"><span class="fa fa-print"></span>Print</a>
			<a href="<?php echo $paper["links"]["pdf"]; ?>" target="_blank"><span class="fa fa-file-pdf-o"></span>PDF</a>
			<a href="<?php echo $paper["website"]; ?>" target="_blank"><span class="fa fa-external-link"></span>Web Site</a>
		</span>
		<span class="tfp-meta-nav">
			<?php if (isset ($paper ["links"]) && isset ($paper ["links"] ["prev"])) : ?>
				<a href="<?php echo $paper ["links"] ["prev"]; ?>"><span class="fa fa-chevron-left"></span>Previous</a>
			<?php else : ?>
				<a href="#" disabled class="tfp-disabled"><span class="fa fa-chevron-left"></span>Previous</a>
			<?php endif; ?>
			<?php if (isset ($paper ["links"]) && isset ($paper ["links"] ["next"])) : ?>
				<a href="<?php echo $paper ["links"] ["next"]; ?>">Next<span class="fa fa-chevron-right"></span></a>
			<?php else : ?>
				<a href="#" disabled class="tfp-disabled">Next<span class="fa fa-chevron-right"></span></a>
			<?php endif; ?>
		</span>
	</p>

	<p class="tfp-thumbnail">
		<a href="<?php echo $paper["images"]["lg"]; ?>">
			<img src="<?php echo $paper["images"]["lg"]; ?>">
		</a>
	</p>
</div>
