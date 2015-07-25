/*! (c) Blackbaud, Inc. */
bbiGetInstance().register({
    alias: "BlackbaudWP",
    author: "Blackbaud Interactive"
})
.scope({
    selectText: function(element) {
        var range;
        if (document.selection) {
            range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
        } else if (window.getSelection()) {
            range = document.createRange();
            range.selectNode(element);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
        }
    }
})
.action("dashboard", function(app, bbi, $) {
    return {
        init: function() {
            $(function() {
                // Trigger the WordPress gallery picker popup.
                $('.blackbaud-metabox-gallery-picker').each(function() {
                    var $cont = $(this);
                    var customUploader;
                    $cont.find('.blackbaud-metabox-gallery-picker-button').on("click", function(e) {
                        e.preventDefault();
                        // If the uploader object has already been created, reopen the dialog.
                        if (customUploader) {
                            customUploader.open();
                            return;
                        }
                        // Extend the wp.media object.
                        customUploader = wp.media.frames.file_frame = wp.media({
                            title: "Choose Image",
                            button: {
                                text: "Choose Image"
                            },
                            multiple: false
                        });
                        // When a file is selected, grab the URL and set it as the text field's value.
                        customUploader.on("select", function() {
                            attachment = customUploader.state().get("selection").first().toJSON();
                            $cont.find('input').val(attachment.url);
                        });
                        // Open the uploader dialog
                        customUploader.open();
                    });
                });
                // Show or hide a selection of fields based on a checkbox's state.
                $('[data-checkbox-group-selector]').each(function() {
                    var $checkbox = $(this).find(':checkbox');
                    var selector = $(this).attr("data-checkbox-group-selector");
                    var update = function() {
                            if ($checkbox.is(":checked")) {
                                $(selector).show();
                            } else {
                                $(selector).hide();
                            }
                        };
                    $checkbox.on("click", update);
                    update();
                });
                // Select text when clicking on the element.
                $('.blackbaud-selectable').on("click", function() {
                    app.scope.selectText(this);
                });
            });
        }
    }
})
.build();
