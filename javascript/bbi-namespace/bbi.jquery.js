/*! BBI jQuery (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "jQuery";



	bbi.on("preload", function() {



		bbi.extension({
			alias: alias,
			defaults: {
				jQuery: null
			},
			directive: function(ext, bbi) {

				var settings = ext.settings();

				var _$;
				var _window$;

				var _locations = {
					namespace: null,
					window: null
				};

				var methods = {
					check: function() {

						var settingsOkay = methods.check.settings();
						var windowOkay = methods.check.window();

						// The user didn't use noConflict, which is fine.
						// Just use the window's version of jQuery.
						if (!settingsOkay && windowOkay) {
							_$ = _window$;
							_locations.namespace = _$;
							_locations.window = _window$;
						}

						// jQuery not defined!
						if (typeof _$ !== "function") {
							throw new Error("[BBI.jQuery.Check] The BBI namespace requires jQuery 1.7.2 (or greater) to operate.");
						}

						delete this["check"];

					},
					instance: function (label) {
						var j;
						if (typeof _locations[label] === "function") {
							return _locations[label];
						} else if (typeof _$ === "function") {
							return _$;
						} else {
							return _window$;
						}
					},
					set: function (label, fn) {
						_locations[label] = fn;
					}
				};

				methods.check.settings = function () {
					if (typeof settings.jQuery === "function") {
						_locations.namespace = _$ = settings.jQuery;
						if (typeof _win.console === "object") {
							console.log("[BBI.jQuery.Check] BBI using jQuery.noConflict() v." + _$.fn.jquery);
						}
						return true;
					}
					return false;
				};

				methods.check.window = function () {
					if (typeof _win.jQuery === "function") {
						_locations.window = _window$ = _win.jQuery;
						if (typeof _win.console === "object") {
							console.log("[BBI.jQuery.Check] Window using jQuery v.", _window$.fn.jquery);
						}
						return true;
					}
					return false;
				};

				return {
					setLocation: methods.set,
					check: methods.check,
					jQuery: methods.instance
				};
			}
		});



		var instance = bbi.instantiate(alias, bbi.options());



		instance.check();



		bbi.map("jQuery", instance.jQuery);



	});



}.call({}, window, bbiGetInstance()));
