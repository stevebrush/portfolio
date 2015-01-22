/*! BBI BBNC (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "bbnc";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {
				partTitleKeyword: 'Customization'
			},
			directive: function(ext, bbi, $) {
				var settings = ext.settings();
				var _prm;
				var _usesMicrosoftAjax = false;
				var methods = {
					attach: function(fn, args, context) {
						$(function() {
							$.proxy(fn, context)(args);
						});
						if (_usesMicrosoftAjax === true) {
							_prm.add_endRequest(function() {
								$.proxy(fn, context)(args);
							});
						}
					},
					getPageRequestManager: function() {
						var prm = {};
						if (typeof _prm === "object") {
							return _prm;
						}
						try {
							prm = Sys.WebForms.PageRequestManager.getInstance();
							_usesMicrosoftAjax = true;
						} catch (e) {
							if (bbi.isDebugMode() === true) {
								bbi.log(e.message, false);
							}
						}
						return prm;
					},
					showPartTitle: function() {
						methods.attach(function() {
							var body = $('#BodyId');
							if (body.find('.js-part-label').length === 0) {
								body.find('[id*="_tdPartName"]:contains("' + settings.partTitleKeyword + '")').each(function() {
									var popup = $(this);
									$('#' + popup.attr('id').replace('tdPartName', 'pnlPart')).prepend('<div class="js-part-label">' + popup.text() + ' <em>(click to modify)</em></div>');
								});
							}
						});
					}
				};
				var __construct = (function() {
					_prm = methods.getPageRequestManager();
					if (bbi("debug").getInstance(0).isPageEditor()) {
						methods.showPartTitle();
					}
				}());
				return {
					attach: methods.attach
				};
			}
		});



		var instance = bbi.instantiate(alias);



		bbi.map("attach", instance.attach);



	});



}.call({}, window, bbiGetInstance()));
