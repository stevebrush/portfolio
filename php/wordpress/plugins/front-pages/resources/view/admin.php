<style>
	.tfp-form-caption {
		width: 150px;
	}
	.tfp-settings-table,
	.textbox {
		width: 100%;
	}
</style>
<div class="wrap">
	<h2>Today's Front Pages: Settings</h2>
	<form name="tfpApiSettings" method="post" action="">
		<h4 class="tfp-section-heading">
			JSON Feed Locations (full URLs)
		</h4>
		<table class="tfp-settings-table">
			<?php if (isset($data["feed"])) : ?>
				<?php foreach ($data["feed"] as $slug => $arr) : ?>
					<tr>
						<td class="tfp-form-caption">
							<label><?php echo $arr["label"]; ?></label>
						</td>
						<td class="tfp-form-control">
							<input class="textbox" type="text" name="<?php echo $slug; ?>" value="<?php echo $arr["value"]; ?>" maxlength="250">
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
		<h4 class="tfp-section-heading">
			Microsoft Bing Maps App Key
		</h4>
		<table class="tfp-settings-table">
			<?php if (isset($data["map"])) : ?>
				<?php foreach ($data["map"] as $slug => $arr) : ?>
					<tr>
						<td class="tfp-form-caption">
							<label><?php echo $arr["label"]; ?></label>
						</td>
						<td class="tfp-form-control">
							<input class="textbox" type="text" name="<?php echo $slug; ?>" value="<?php echo $arr["value"]; ?>" maxlength="250">
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e("Save Changes") ?>" />
		</p>
	</form>
</div>