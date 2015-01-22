<div class="tfp-pane">
	<?php include TFP_VIEW_PATH . "utility.php"; ?>
	<div class="tfp-content">
		<div class="tfp-content-body">
			<?php if (isset($data) && isset($data["papers"])) : ?>
				<?php
				$storedStates = array();
				$storedCountries = array();
				$paperIndex = 0;
				$showLocation = ((isset($wp_query->query_vars['tfp_sort_by']) === false) || $wp_query->query_vars['tfp_sort_by'] !== "title");
				?>
				<?php foreach ($data["papers"] as $paper) : ?>
					<?php if ($showLocation === true) : ?>
						<?php if ($paper["state"]) : ?>
							<?php if (in_array($paper["state"], $storedStates) === false) : ?>
								<?php $storedStates[] = $paper["state"]; ?>
								<p><strong><?php echo $paper["state"]; ?></strong></p>
							<?php endif; ?>
						<?php elseif ($paper["country"]) : ?>
							<?php if (in_array($paper["country"], $storedCountries) === false) : ?>
								<?php $storedCountries[] = $paper["country"]; ?>
								<p><strong><?php echo $paper["country"]; ?></strong></p>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
					<div class="tfp-list-item">
						<?php
						$title = (isset ($wp_query->query_vars ['tfp_sort_by']) && $wp_query->query_vars ['tfp_sort_by'] === "title") ? $paper["sortTitle"] : $paper["title"];
						?>
						<a class="tfp-list-link" href="<?php echo add_query_arg(array("tfp_id" => $paper["paperId"])); ?>" name="<?php echo $paper["paperId"]; ?>">
							<em><?php echo $title; ?></em>
							<div class="tfp-list-popup">
								<h4><?php echo $title; ?></h4>
								<h6><?php echo $paper ["location"]; ?></h6>
								<div class="thumbnail">
									<img src="<?php echo $paper ["images"]["md"]; ?>" alt="">
								</div>
							</div>
						</a> <small><?php echo $paper ["location"]; ?></small>
					</div>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
