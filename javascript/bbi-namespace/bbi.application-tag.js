/*! BBI Applications Tag Handler (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	/**
	 * Tags function as each App's action initializers.
	 * A tag is simply any HTML element with the "data-bbi-app" and "data-bbi-action" attributes appended to it.
	 * When BBI locates these attributes, it will attempt to fire the "init" function returned by each action.
	 * HTML5 data attributes will be sent as options to each specified action.
	 **/



	var alias = "applications-tag-handler";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {
				dataAttr_action: 'data-bbi-action',
				dataAttr_app: 'data-bbi-app'
			},
			directive: function(ext, bbi, $) {


				var _settings = ext.settings();
				var _appsHandler = bbi("applications-handler").getInstance(0);
				var _tags = [];


				var methods = {
					execute: function() {

						var apps = bbi.apps();
						var tag;
						var context;
						var i;
						var length = _tags.length;

						// Give IE8 a chance to breathe
						_win.setTimeout(function() {

							// Execute init function, passing in arguments
							for (i = 0; i < length; i++) {
								tag = _tags[i];
								if (typeof tag.action === "string") {
									if (typeof apps[tag.app] !== "object") {
										bbi.log("The app with the alias \"" + tag.app + "\" does not exist, or the alias on the tag does not match the alias used to register the application: <div data-bbi-action=\"" + tag.app + "\" data-bbi-action=\"" + tag.action + "\"></div>\nIn some instances this error occurs when the namespace is being overwritten by another reference. Double-check that the namespace is only being initialized once on the page.");
									}
									context = apps[tag.app].actions[tag.action];
									if (typeof context === "object" && typeof context.init === "function") {
										bbi.helper.executeFunctionByName("init", [tag.data, tag.element], context);
									} else if (tag.data.length) {
										throw new Error("The action, " + tag.action + ", in the app, " + tag.app + ", is expecting to receive options but does not have an initializing function. Add an 'init' function to your action to receive options.", false);
									} else {
										throw new Error("The action, " + tag.action + ", in the app, " + tag.app + ", does not exist. Double-check the data-bbi-action and data-bbi-app attributes on your tag.");
									}
								}
							}
						}, 0);
					},
					find: function() {
						var tags = $('[' + _settings.dataAttr_app + '], [bbi-app]');
						var length = tags.length;
						var i;
						if (length > 0) {
							for (i = 0; i < length; i++) {
								methods.process(tags[i]);
							}
							methods.execute();
						} else {
							if (bbi.isDebugMode() === true) {
								bbi.log("No tags found.", false);
							}
						}
					},
					process: function(elem) {
						var app = elem.getAttribute('bbi-app') || elem.getAttribute(_settings.dataAttr_app);
						var action = elem.getAttribute('bbi-action') || elem.getAttribute(_settings.dataAttr_action);
						_tags.push({
							app: app,
							action: action,
							data: bbi.helper.data(elem),
							element: elem
						});
					}
				};


				var __construct = (function() {
					$(_win.document).on('bbi-apps-loaded', function() {
						$(methods.find);
					});
				}());


				return {};


			}
		});



		var instance = bbi.instantiate(alias);



	});



}.call({}, window, bbiGetInstance()));
