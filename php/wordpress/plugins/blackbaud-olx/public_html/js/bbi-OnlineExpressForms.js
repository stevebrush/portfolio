window
.bbiGetInstance()
.register({
	alias: "OnlineExpressForms",
	author: "Blackbaud Interactive Services"
})
.action("__", function (app, bbi, $) {
	$(function () {

	});
})
.action("ConfirmationSocialSharing", function (app, bbi, $) {

	var defaults = {};
	var settings = {};

	var $modal;

	var methods = {
		activateModal: function () {

			// Activate the modal with options.
			if (settings.activateOnLoad == "true") {
				$modal.modal();
			}

		},
		activateShareThis: function () {

			window.stLight.options({
				publisher: settings.shareThisPublisherId,
				doNotHash: false,
				doNotCopy: false,
				hashAddressBar: false
			});

			// Facebook
			window.stWidget.addEntry({
				 "service": "facebook",
				 "element": document.getElementById('olx-forms-modal-share-facebook'),
				 "url": settings.shareUrl,
				 "title": settings.shareTitle,
				 "type": "custom",
				 "text": "Facebook",
				 "image": settings.shareImage,
				 "summary": settings.shareSummary
			});

			// Twitter
			window.stWidget.addEntry({
				 "service": "twitter",
				 "element": document.getElementById('olx-forms-modal-share-twitter'),
				 "url": settings.shareUrl,
				 "title": settings.shareTitle,
				 "type": "custom",
				 "text": "Twitter",
				 "image": settings.shareImage,
				 "summary": settings.shareSummary
			});

			// Google Plus
			window.stWidget.addEntry({
				 "service": "googleplus",
				 "element": document.getElementById('olx-forms-modal-share-google-plus'),
				 "url": settings.shareUrl,
				 "title": settings.shareTitle,
				 "type": "custom",
				 "text": "Google Plus",
				 "image": settings.shareImage,
				 "summary": settings.shareSummary
			});

			// Email
			window.stWidget.addEntry({
				 "service": "email",
				 "element": document.getElementById('olx-forms-modal-share-email'),
				 "url": settings.shareUrl,
				 "title": settings.shareTitle,
				 "type": "custom",
				 "text": "Email",
				 "image": settings.shareImage,
				 "summary": settings.shareSummary
			});

		},
		buildModal: function () {

			var title;
			var description;
			var html;

			// Introduction Title
			if (settings.introductionTitle.length) {
				title = '<h4>' + settings.introductionTitle + '</h4>';
			} else {
				title = '';
			}

			// Introduction Description
			if (settings.introductionBody.length) {
				description = '<p>' + settings.introductionBody + '</p>';
			} else {
				description = '';
			}

			html = [
				'<div class="modal fade" id="olx-forms-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">',
					'<div class="modal-dialog modal-sm">',
						'<div class="modal-content">',
							'<div class="modal-body">',
								title,
								description,
								'<ul class="nav nav-stacked nav-pills">',
									'<li><a href="#" id="olx-forms-modal-share-facebook" class="btn btn-social btn-facebook"><i class="fa fa-facebook-official"></i>Facebook</a></li>',
									'<li><a href="#" id="olx-forms-modal-share-twitter" class="btn btn-social btn-twitter"><i class="fa fa-twitter"></i>Twitter</a></li>',
									'<li><a href="#" id="olx-forms-modal-share-google-plus" class="btn btn-social btn-google-plus"><i class="fa fa-google-plus"></i>Google</a></li>',
									'<li><a href="#" id="olx-forms-modal-share-email" class="btn btn-social btn-email"><i class="fa fa-envelope"></i>Email</a></li>',
								'</ul>',
							'</div>',
							'<div class="modal-footer">',
								'<button type="button" class="btn btn-default btn-block" data-dismiss="modal">Close</button>',
							'</div>',
						'</div>',
					'</div>',
				'</div>'
			];

			// Add the modal to the page.
			$('body').prepend(html.join(""));
			$modal = $('#olx-forms-modal');

		},
		loadShareThis: function (callback) {

			// Load the ShareThis library, if it doesn't exist.
			if (typeof stlib === "undefined") {
				bbi.helper.loadScript("//ws.sharethis.com/button/buttons.js", function () {
					callback();
				});
			} else {
				callback();
			}

		},
		onConfirmation: function () {
			// Launch modal with button.
			$('#mongo-form').append('<p><a id="olx-forms-launch-modal" class="btn btn-primary" href="#"><i class="fa fa-' + settings.buttonIcon + '"></i>' + settings.buttonLabel + '</a></p>');
			$('#olx-forms-launch-modal').on("click", function (e) {
				e.preventDefault();
				$modal.modal('show');
			});

		}
	};

	return {
		init: function (options, element) {

			settings = $.extend(true, {}, defaults, options);

			if (settings.active == "true") {

				methods.buildModal();

				methods.loadShareThis(function () {
					methods.activateShareThis();
				});

				bbi.olx.on("success", function (form) {
					methods.activateModal();
					methods.onConfirmation();
				});

			}
		}
	};

})
.action("SelectTextOnClick", function (app, bbi, $) {

	var methods = {
		selectText: function (element) {

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
	};

	return {
		init: function (options, element) {
			$('.olx-forms-selectable').on("click", function () {
				methods.selectText(this);
			});
		}
	};
})
.build();
