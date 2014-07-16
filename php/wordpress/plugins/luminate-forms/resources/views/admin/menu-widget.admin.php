<div id="posttype-lo-profile-menu" class="posttypediv">
	<div id="tabs-panel-lo-profile-menu" class="tabs-panel tabs-panel-active">
		<ul id ="lo-profile-menu-checklist" class="categorychecklist form-no-clear">
			<li>
				<label class="menu-item-title">
					<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php _e("Login", "luminate-forms"); ?>
				</label>
				<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
				<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php _e("Login", "luminate-forms"); ?>">
				<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php echo $loApp->config("url","login"); ?>">
				<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="lo-logged-out">
			</li>
			<li>
				<label class="menu-item-title">
					<input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="-2"> <?php _e("Logout", "luminate-forms"); ?>
				</label>
				<input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="custom">
				<input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="<?php _e("Logout", "luminate-forms"); ?>">
				<input type="hidden" class="menu-item-url" name="menu-item[-2][menu-item-url]" value="http://link-is-generated-automagically.html">
				<input type="hidden" class="menu-item-classes" name="menu-item[-2][menu-item-classes]" value="lo-logged-in lo-log-out-link">
			</li>
			<li>
				<label class="menu-item-title">
					<input type="checkbox" class="menu-item-checkbox" name="menu-item[-3][menu-item-object-id]" value="-3"> <?php _e("My Profile", "luminate-forms"); ?>
				</label>
				<input type="hidden" class="menu-item-type" name="menu-item[-3][menu-item-type]" value="custom">
				<input type="hidden" class="menu-item-title" name="menu-item[-3][menu-item-title]" value="<?php _e("My Profile", "luminate-forms"); ?>">
				<input type="hidden" class="menu-item-url" name="menu-item[-3][menu-item-url]" value="<?php echo $loApp->config("url","profile"); ?>">
				<input type="hidden" class="menu-item-classes" name="menu-item[-3][menu-item-classes]" value="lo-logged-in">
			</li>
			<li>
				<label class="menu-item-title">
					<input type="checkbox" class="menu-item-checkbox" name="menu-item[-4][menu-item-object-id]" value="-4"> <?php _e("Register", "luminate-forms"); ?>
				</label>
				<input type="hidden" class="menu-item-type" name="menu-item[-4][menu-item-type]" value="custom">
				<input type="hidden" class="menu-item-title" name="menu-item[-4][menu-item-title]" value="<?php _e("Register", "luminate-forms"); ?>">
				<input type="hidden" class="menu-item-url" name="menu-item[-4][menu-item-url]" value="<?php echo $loApp->config("url","register"); ?>">
				<input type="hidden" class="menu-item-classes" name="menu-item[-4][menu-item-classes]" value="lo-logged-out">
			</li>
		</ul>
	</div>
	<p class="button-controls">
		<span class="add-to-menu">
			<input type="submit" <?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-lo-profile-menu">
			<span class="spinner"></span>
		</span>
	</p>
</div>