<?php if (isset($data['meta']) && ! empty($data['meta']['launcher_label'])) : ?>
    <button type="button" class="btn btn-primary btn-lg btn-blackbaud-flyer<?php echo (! empty($data['meta']['css_class'])) ? ' btn-' . $data['meta']['css_class'] : ''; ?>" data-toggle="modal" data-target="#blackbaud-modal-<?php echo $data['ID']; ?>">
        <?php echo $data['meta']['launcher_label']; ?>
    </button>
<?php endif; ?>
<div class="modal modal-blackbaud-flyer fade<?php echo (! empty($data['meta']['css_class'])) ? ' ' . $data['meta']['css_class'] : ''; ?>" id="blackbaud-modal-<?php echo $data['ID']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"><?php echo $data ['post_title']; ?></h3>
            </div>
            <div class="modal-body">
                <?php if (! empty($data['meta']['html_before'])) : ?>
                    <div class="modal-html-before">
                        <?php echo $data['meta']['html_before']; ?>
                    </div>
                <?php endif; ?>
                <?php if (! empty($data['meta']['thumbnail'])) : ?>
                    <div class="thumbnail">
                        <img src="<?php echo $data['meta']['thumbnail']; ?>">
                    </div>
                <?php endif; ?>
                <?php if (! empty($data['post_excerpt'])) : ?>
                    <h4 class="modal-subtitle"><?php echo $data['post_excerpt']; ?></h4>
                <?php endif; ?>
                <?php echo do_shortcode($data ['post_content']); ?>
                <?php if (! empty($data['meta']['button_label'])) : ?>
                    <div class="modal-call-to-action">
                        <a class="btn btn-primary" target="_blank" href="<?php echo $data['meta']['button_url']; ?>"><?php echo $data['meta']['button_label']; ?></a>
                    </div>
                <?php endif; ?>
                <?php if (! empty($data['meta']['html_after'])) : ?>
                    <div class="modal-html-after">
                        <?php echo $data['meta']['html_after']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($data['meta']['auto_launch']) && $data['meta']['auto_launch'] == 'true') : ?>
<script>
(function($){
    $(function () {
        $('#blackbaud-modal-<?php echo $data['ID']; ?>').modal();
    });
}(jQuery));
</script>
<?php endif; ?>
