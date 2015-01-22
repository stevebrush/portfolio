<div class="olx-form-wrapper">
	<?php if (isset($data) && count($data) > 0) : ?>
		<?php foreach ($data["fields"] as $field) : ?>
			<div class="form-group">
				<?php echo $field["label"]; ?>
				<?php echo $field["input"]; ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
