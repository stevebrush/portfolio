<div class="olx-form-wrapper">
	<?php if (isset($data) && count($data) > 0) : ?>
		<?php foreach ($data ["fields"] as $field) : ?>
			<?php echo $field ["html"]; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
