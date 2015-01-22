<div class="tfp-pane">
	<?php include TFP_VIEW_PATH . "utility-basic.php"; ?>
	<div class="tfp-content">
		<div class="tfp-content-body">
			<?php if (isset($data) && isset($data["papers"]) && isset($data["papers"])) : $showSummary = true; ?>
				<div class="thumbnail-group">
					<?php foreach ($data["papers"] as $paper) : ?>
						<?php if ($showSummary && isset($data["summary"])) : $showSummary = false; ?>
							<div class="col-md-6 col-sm-8">
								<div class="thumbnail-group-item tfp-top-ten">
									<h4 class="tfp-top-ten-title"><?php echo $data["summary"]["top10Title"]; ?></h4>
									<p class="tfp-top-ten-author">By <?php echo $data["summary"]["top10Author"]; ?></p>
									<p class="tfp-top-ten-body"><?php echo $data["summary"]["top10desc"]; ?></p>
								</div>
							</div>
						<?php endif; ?>
						<div class="col-md-3 col-sm-4">
							<div class="thumbnail-group-item">
								<p class="thumbnail">
									<a href="<?php echo add_query_arg(array("tfp_id" => $paper["paperId"])); ?>">
										<img src="<?php echo $paper["images"]["sm"]; ?>">
									</a>
								</p>
								<h4 class="thumbnail-group-title">
									<a href="<?php echo add_query_arg(array("tfp_id" => $paper["paperId"])); ?>" title="<?php echo $paper["title"]; ?>">
										<?php echo (isset($wp_query->query_vars['tfp_sort_by']) && $wp_query->query_vars['tfp_sort_by'] === "title") ? $paper["sortTitle"] : $paper["title"]; ?>
									</a>
								</h4>
								<div class="thumbnail-group-body">
									<p title="<?php echo $paper["location"]; ?>"><?php echo $paper["location"]; ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="tfp-rss-link">
		<h4>RSS:</h4>
		<a href="http://www.newseum.org/todaysfrontpages/?tfp_display=toptenrss" target="_blank"><span class="fa fa-rss"></span>http://www.newseum.org/todaysfrontpages/?tfp_display=toptenrss</a>
	</div>
</div>
