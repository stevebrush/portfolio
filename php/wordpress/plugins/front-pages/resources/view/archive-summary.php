<div class="tfp-pane">
	<?php include TFP_VIEW_PATH . "utility-basic.php"; ?>
	<div class="tfp-content">
		<div class="tfp-content-body">
			<?php if (isset($data) && isset($data["papers"])) : ?>
				<?php
				$usedTitles = array();
				$clones = $data["papers"];
				?>
				<?php foreach ($data["papers"] as $paper) : ?>
					<?php
					$title = $paper["seriestitle"];
					$showTitle = true;
					?>
					<?php foreach ($clones as $k => $v) : ?>
						<?php if ($v["seriestitle"] == $title) : ?>
							<?php if ($showTitle) : ?>
								<p><strong><?php echo $title; ?></strong></p>
								<?php $showTitle = false; ?>
							<?php endif; ?>
							<p><a href="<?php echo $v["links"]["detail"]; ?>" name="<?php echo $paper["archiveid"]; ?>"><?php echo $v["title"]; ?></a></p>
							<?php unset($clones[$k]); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
