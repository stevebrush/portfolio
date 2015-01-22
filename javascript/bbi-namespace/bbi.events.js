/*! BBI Events (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "events";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {},
			directive: function(ext, bbi, $) {

				var settings = ext.settings();
				var _j;
				var $win;
				var usingNoConflict = false;

				var methods = {
					trigger: function(key, args) {
						if (usingNoConflict) {
							_j("namespace")(_win.document).trigger(key).unbind(key);
						}
						_j("window")(_win.document).trigger(key).unbind(key);
					}
				};

				var __constructor = (function () {
					_j = bbi("jQuery").getInstance(0).jQuery;
					usingNoConflict = (_j("namespace").fn.jquery !== _j("window").fn.jquery);
				}());

				return {
					trigger: methods.trigger
				};
			}
		});



		var instance = bbi.instantiate(alias);



	});



}.call({}, window, bbiGetInstance()));
