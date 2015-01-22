/*! BBI Storage (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "storage";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {},
			directive: function(ext, bbi, $) {
				var settings = ext.settings();
				var x = {};
				var methods = {
					clear: function(key) {
						if (typeof key === "string") {
							delete x[key];
						} else {
							x = {};
						}
					},
					expose: function() {
						return x;
					},
					get: function(key) {
						if (x && typeof x[key] !== "undefined") {
							return x[key];
						}
						return null;
					},
					set: function(key, value) {
						if (typeof _win.sessvars === "object") {
							throw new Error("You are attempting to use " + bbiSettings.alias + ".storage when Sessvars currently exists on the page. Sessvars overwrites the storage object, so only one method can be used at a given time.");
						}
						x[key] = value;
					}
				};
				var __construct = (function() {
					var temp;
					var n = _win.name;
					try {
						if (n && n.length > 0 && typeof n !== "undefined" && n !== "undefined") {
							if (typeof $.parseJSON === "function") {
								if (bbi.isDebugMode() === true) {
									bbi.log("[BBI.storage] Parsing Window.name via $.parseJSON.", false);
								}
								temp = $.parseJSON(n);
							} else {
								if (bbi.isDebugMode() === true) {
									bbi.log("[BBI.storage] Parsing Window.name via eval().", false);
								}
								temp = eval('(' + n + ')');
							}
						} else {
							if (bbi.isDebugMode() === true) {
								bbi.log("[BBI.storage] Window.name is empty; creating empty storage object.", false);
							}
							temp = {};
						}
					} catch (e) {
						bbi.log("[BBI.storage.build]: " + e.message, false);
						if (bbi.isDebugMode() === true) {
							console.log("Error details: ", e);
						}
					} finally {
						if (typeof temp === "object") {
							x = temp;
						}
					}
					// When the page refreshes or is closed,
					// save the temp object into the Window
					_win.onunload = function() {
						if (typeof _win.sessvars !== "object") {
							window.name = JSON.stringify(x);
						}
					};
				}());
				return methods;
			}
		});



		var instance = bbi.instantiate(alias);



		bbi.map(alias, instance);



	});



}.call({}, window, bbiGetInstance()));
