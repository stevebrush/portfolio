<style>
	.wrap-olx-forms {
		padding: 10px 20px 0 2px;
		margin: 0;
	}
		.wrap-olx-forms h2 {
			margin: 9px 15px 4px 0;
			padding: 0;
		}
		.wrap-olx-forms .dashicons {
			font-size: 33px;
			width: 33px;
			height: 33px;
		}
</style>
<div class="wrap wrap-olx-forms">
	<h2><span class="dashicons dashicons-admin-settings"></span> <?php _e ("Online Express Forms: Settings"); ?></h2>
	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php settings_fields ($data["pageId"]); ?>
		<?php do_settings_sections ($data["pageId"]); ?>
		<?php submit_button (); ?>
	</form>
</div>
