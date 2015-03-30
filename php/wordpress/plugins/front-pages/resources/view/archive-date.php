<?php
$counter = 0;
$colCounter = 1;
$paginator = $data["paginator"];
?>
<div class="tfp-pane">
	<?php include TFP_VIEW_PATH . "utility-with-back.php"; ?>
	<div class="tfp-content">
		<div class="tfp-content-header">
			<?php include TFP_VIEW_PATH . "paginator.php"; ?>
		</div>
		<div class="tfp-content-body">
			<?php if (isset($data) && isset($data["papers"])) : ?>
				<div class="thumbnail-group">
					<?php foreach ($data["papers"] as $paper) : ?>
						<?php

						$counter++;

						// Offset
						if ($counter < $paginator["startItem"]) {
							continue;
						}

						// Limit
						if ($counter >= ($paginator["startItem"] + $data["options"]["show"])) {
							break;
						}

						// Create new row
						if ($counter === ($paginator["startItem"] + ($colCounter * $data["options"]["itemsPerRow"]) - $data["options"]["itemsPerRow"])) {
							echo '<div class="row">';
						}

						?>
						<div class="col-sm-<?php echo $data["options"]["colWidth"]; ?>">
							<div class="thumbnail-group-item">
								<p class="thumbnail">
									<a href="<?php echo add_query_arg(array("tfp_id" => $paper["paperId"])); ?>">
										<img src="<?php echo $paper["images"]["md"]; ?>">
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
						<?php

						// End row
						if ($counter === (($paginator["startItem"]) + (($colCounter * $data["options"]["itemsPerRow"]) - 1))) {
							echo '</div>';
							$colCounter++;
						}

						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="tfp-content-footer">
			<?php include TFP_VIEW_PATH . "paginator.php"; ?>
		</div>
	</div>
</div>
