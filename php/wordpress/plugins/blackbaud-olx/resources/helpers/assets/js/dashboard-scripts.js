(function ($) {
	$(function () {

		$('.blackbaud-metabox-gallery-picker').each(function () {

			console.log("Image picker found!");

			var $cont = $(this);
			var custom_uploader;

			$cont.find('.blackbaud-metabox-gallery-picker-button').on("click", function (e) {

		        e.preventDefault();

		        // If the uploader object has already been created, reopen the dialog.
		        if (custom_uploader) {
		            custom_uploader.open();
		            return;
		        }

		        // Extend the wp.media object.
		        custom_uploader = wp.media.frames.file_frame = wp.media({
		            title: "Choose Image",
		            button: {
		                text: "Choose Image"
		            },
		            multiple: false
		        });

		        // When a file is selected, grab the URL and set it as the text field's value.
		        custom_uploader.on("select", function () {
		            attachment = custom_uploader.state().get("selection").first().toJSON();
		            $cont.find('input').val(attachment.url);
		        });

		        // Open the uploader dialog
		        custom_uploader.open();

		    });

		});

		$('[data-checkbox-group-selector]').each(function () {

			var checkbox = $(this).find(':checkbox');
			var selector = $(this).attr("data-checkbox-group-selector");

			var update = function () {
				if (checkbox.is(":checked")) {
					$(selector).show();
				} else {
					$(selector).hide();
				}
			};

			checkbox.on("click", function () {
				update();
			});

			update();

		});

	});
}(jQuery));
