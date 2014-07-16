<?php if (!isset($loApp)) die(); ?>
<div class="luminateFormWrapper wrap">
	<h2>Form Permalinks</h2>
	<p>This section allows you to define which form is on a particular page.</p>
	<form name="loApiSettings" method="post" action="">
		<table class="loSettingsTable">
			<tr>
				<td>
					<label>Login Page:</label>
				</td>
				<td>
					<?php wp_dropdown_pages(array("selected"=>$loginPageId_val,"name"=>$loginPageId_name)); ?>
				</td>
			</tr>
			<tr>
				<td>
					<label>Logout Confirmation Page:</label>
				</td>
				<td>
					<?php wp_dropdown_pages(array("selected"=>$logoutPageId_val,"name"=>$logoutPageId_name)); ?>
				</td>
			</tr>
			<tr>
				<td>
					<label>Return User Page:</label>
				</td>
				<td>
					<?php wp_dropdown_pages(array("selected"=>$returnUserPageId_val,"name"=>$returnUserPageId_name)); ?>
				</td>
			</tr>
			<tr>
				<td>
					<label>New User Registration Page:</label>
				</td>
				<td>
					<?php wp_dropdown_pages(array("selected"=>$registerPageId_val,"name"=>$registerPageId_name)); ?>
				</td>
			</tr>
			<tr>
				<td>
					<label>Reset Password Page:</label>
				</td>
				<td>
					<?php wp_dropdown_pages(array("selected"=>$resetPasswordPageId_val,"name"=>$resetPasswordPageId_name)); ?>
				</td>
			</tr>
			<tr>
				<td>
					<label>User Profile Page:</label>
				</td>
				<td>
					<?php wp_dropdown_pages(array("selected"=>$profilePageId_val,"name"=>$profilePageId_name)); ?>
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e("Save Changes") ?>" />
		</p>
	</form>
</div>