<div class="wrap wrap-blackbaud">
    <h2>
        <span class="dashicons dashicons-admin-settings"></span> <?php echo $data["page_title"]; ?>
    </h2>
    <form method="post" action="options.php" enctype="multipart/form-data">
        <?php settings_fields ($data ["page_id"]); ?>
        <?php do_settings_sections ($data ["page_id"]); ?>
        <?php submit_button (); ?>
    </form>
</div>
