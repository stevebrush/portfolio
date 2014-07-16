<?php if (!isset($loApp)) die(); ?>
<style>
	.loSettingsTable .textbox {
		width:150px;
	}
	.loSettingsTable .textboxWide {
		width:300px;
	}
</style>
<div class="luminateFormWrapper wrap">
	<h2>API Settings</h2>
	<form name="loApiSettings" method="post" action="">
		<table class="loSettingsTable">
			<tr>
				<td><label>API Key</label></td>
				<td>
					<input class="textbox" type="text" name="<?php echo $apiKey_name; ?>" value="<?php echo $apiKey_val; ?>" maxlength="100">
				</td>
			</tr>
			<tr>
				<td><label>API Secret Key</label></td>
				<td>
					<input class="textbox" type="text" name="<?php echo $apiSecret_name; ?>" value="<?php echo $apiSecret_val; ?>" maxlength="100">
				</td>
			</tr>
			<tr>
				<td><label>API Username</label></td>
				<td>
					<input class="textbox" type="text" name="<?php echo $apiUser_name; ?>" value="<?php echo $apiUser_val; ?>" maxlength="100">
				</td>
			</tr>
			<tr>
				<td><label>API Password</label></td>
				<td>
					<input class="textbox" type="text" name="<?php echo $apiPassword_name; ?>" value="<?php echo $apiPassword_val; ?>" maxlength="100">
				</td>
			</tr>
			<tr>
				<td><label>API Version</label></td>
				<td>
					<input class="textbox" type="text" name="<?php echo $apiVersion_name; ?>" value="<?php echo $apiVersion_val; ?>" maxlength="100">
				</td>
			</tr>
			<tr>
				<td><label>API URL</label></td>
				<td>
					<input class="textbox textboxWide" type="text" name="<?php echo $apiUrl_name; ?>" value="<?php echo $apiUrl_val; ?>" maxlength="100">
				</td>
			</tr>
			<tr>
				<td><label>API URL (secure)</label></td>
				<td>
					<input class="textbox textboxWide" type="text" name="<?php echo $apiUrlSecure_name; ?>" value="<?php echo $apiUrlSecure_val; ?>" maxlength="100">
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e("Save Changes") ?>" />
		</p>
	</form>
</div>