/*! BBI Online Express (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "online-express";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {},
			directive: function (ext, bbi, $) {

				var settings = ext.settings();

				var bbiReady = false;
				var olxReady = false;

				var bb$;

				var $doc = $(_win.document);
				var $btn;
				var $root;

				var _onError;
				var _onValidation;
				var _onFail;
				var _onSuccess;
				var _onBeforeUpdate;
				var _onAfterUpdate;

				var _updateForm;

				var methods = {
					block: function () {
						$root.block({
	                        message: "Processing",
	                        css: {
	                            padding: "10px",
	                            border: "none",
	                            fontSize: "16px",
	                            backgroundColor: "#000",
	                            borderRadius: "10px",
	                            "-webkit-border-radius": "10px",
	                            "-moz-border-radius": "10px",
	                            opacity: 0.5,
	                            color: "#fff"
	                        },
	                        overlayCSS: {
	                            backgroundColor: "#fff",
	                            opacity: 0.5
	                        }
	                    });
					},
					check: function() {
						if (bbiReady && olxReady) {
							$doc.trigger("olx-ready");
						}
					},
					on: function(when, fn) {
						switch (when) {



						case "error":
							/**
							 * This function fires when errors are found on the page (pre-processing).
							 **/
							_onError = fn;
							break;



						case "validate":
							/**
							 * This function allows the developer to add their own validations.
							 * Returning "true" will allow the form to continue processing.
							 * Returning "false" will prevent the form from processing.
							 **/
							_onValidation = fn;
							break;



						case "success":
							/**
							 * The onSuccess function doesn't stop the form from submitting,
							 * it simply halts the form's update method, to let you do things
							 * before the user sees the confirmation screen.
							 **/
							_onSuccess = fn;
							break;



						case "fail":
							/**
							 * This function fires when submission errors are found on the page (post-processing).
							 **/
							_onFail = fn;
							break;



						case "beforeUpdate":
							/**
							 * This function fires before each form update.
							 **/
							_onBeforeUpdate = fn;
							break;



						case "afterUpdate":
							/**
							 * This function fires after each form update.
							 **/
							_onAfterUpdate = fn;
							break;



						}
					},
					overrides: function () {

						// Add our own validations object.
						_win.BBOXSectionScripts.BBI_NAMESPACE = {
							presubmit: function () {

								var form = {
									block: methods.block,
									unblock: methods.unblock
								};
								var status;

								if (bbi.isDebugMode()) {
									bbi.log("Online Express Form has been submitted. Validations in progress...", false);
								}

								// Scroll to top of page.
								//bbi("helper").getInstance(0).scrollTo($root);

								status = (typeof _onValidation === "function") ? _onValidation.call(form, form) : true;
								if (bbi.isDebugMode()) {
									bbi.log("Online Express validated? " + status, false);
								}
								return status;

							}
						};

						// Hijack the HTML as it is returned from post-processing.
						_win.bboxOverrides = {
							handleSubmitCallbackOverride: function (html) {

								var numErrors = $(html).find('.BBFormErrorItem').length;
								var form = {
									block: methods.block,
									update: methods.trigger
								};

								// Register the onUpdate functions.
								_updateForm = function () {
									if (typeof _onBeforeUpdate === "function") {
										_onBeforeUpdate.call(form, form, html);
									}
									methods.update(html);
									if (typeof _onAfterUpdate === "function") {
										_onAfterUpdate.call(form, form, html);
									}
								};

								// There are errors on the page.
								if (numErrors > 0) {
									if (typeof _onFail === "function") {
										_onFail.call(form, form, html);
									} else {
										methods.trigger();
									}
								}

								// No errors.
								else {
									if (typeof _onSuccess === "function") {
										_onSuccess.call(form, form, html);
									} else {
										methods.trigger();
									}
								}

								// Update the form's HTML.
								if (typeof _win.bbox.squirtMarkup === "function") {
									_win.bbox.squirtMarkup('<div class="bbi-olx-form-wrapper"><div class="alert alert-info bbi-olx-message">Processing, please wait...</div><div class="bbi-olx-form bbi-off">'+html+'</div></div>', true);
								}
							}
						};

						// Hijack the submit button
						$btn.on("click", function () {
							var form = {};
							if ($('#divClientError').is(":visible")) {
								if (typeof _onError === "function") {
									_onError.call(form, form);
								}
							}
						});

					},
					trigger: function () {
						$doc.trigger("olx-form-submitted");
					},
					unblock: function () {
						$root.unblock();
					},
					update: function (html) {

						// Give OLX time to breathe.
						_win.setTimeout(function () {

							var $root = bb$("#bbox-root");

							$root.find('.bbi-olx-message').addClass('bbi-off');
							$root.find('.bbi-olx-form').removeClass('bbi-off');

							if (typeof bb$.fn.unblock === "function") {
								$root.unblock();
							}

						}, 50);

					}
				};



				var __construct = (function () {

					$doc.off("olx-form-submitted").on("olx-form-submitted", function () {
						if (typeof _updateForm === "function") {
							_updateForm();
						}
					});

					// OLX loaded.
					_win.bboxShowFormComplete = function () {

						// Get OLX's jQuery.
						var j = bbi("jQuery").getInstance(0);
						j.setLocation("olx", _win["bb$"]);
						bb$ = j.jQuery("olx");

						// Set the root variable.
						$root = bb$('#bbox-root');

						// Hijack the submit button.
						$btn = $root.find('.BBFormSubmitbutton');

						olxReady = true;
						methods.check();
					};

					// BBI ready.
					$doc.on("bbi-ready", function () {
						bbiReady = true;
						methods.check();
					});

					// OLX ready.
					$doc.on("olx-ready", function () {
						methods.overrides();
					});

				}());



				return {
					block: methods.block,
					on: function (when, fn) {
						if (olxReady) {
							methods.on(when, fn);
						} else {
							$doc.on("olx-ready", function () {
								methods.on(when, fn);
							});
						}
					},
					unblock: methods.unblock,
					update: methods.trigger
				};



			}
		});



		var instance = bbi.instantiate(alias);



		bbi.map("olx", instance);



	});



}.call({}, window, bbiGetInstance()));
