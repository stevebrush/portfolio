/*! BBI Helper (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "helper";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {},
			directive: function(ext, bbi, $) {
				var settings = ext.settings();
				var _doc = _win.document;
				var methods = {
					arrayFromString: function(str, delimiter) {
						if (typeof str !== "string") {
							str = "";
						}
						if (typeof delimiter !== "string") {
							delimiter = ",";
						}
						return $.map(str.split(delimiter), $.trim);
					},
					clone: function(obj) {
						var cloned;
						try {
							cloned = JSON.parse(JSON.stringify(obj));
						} catch (e) {
							cloned = obj;
							if (bbi.isDebugMode() === true) {
								bbi.log(e.message, false);
							}
						}
						return cloned;
					},
					data: function(elem) {
						var elem;
						var attrs;
						var i = 0;
						var temp = {};
						var name;
						// Was a jQuery element passed, instead of a standard element?
						elem = elem;
						if (typeof elem[0] === "object") {
							elem = elem[0];
						}
						// The element doesn't have attributes, which means it's not an HTML object
						attrs = elem.attributes;
						if (typeof attrs !== "object") {
							return false;
						}
						i = attrs.length;
						while (i--) {
							if (attrs[i]) {
								name = attrs[i].name;
								if (name.indexOf("data-") === 0) {
									name = $.camelCase(name.slice(5));
									temp[name] = attrs[i].value
								}
							}
						}
						return temp;
					},
					doOnFind: function(selector, callback, duration) { // in milliseconds
						var maxIterations = 100; // 10 seconds
						var waitTime = 100; // How long to wait between iterations; 10 times per second
						if (typeof duration != "undefined" && duration > 0 && duration < 30000) {
							maxIterations = duration / waitTime;
						}
						var counter = 0;
						var check = function () {
								_win.setTimeout(function() {
									if (counter++ > maxIterations) {
										return false;
									}
									if ($(selector).length > 0) {
										if (typeof callback === "function") {
											callback();
										}
									} else {
										check();
									}
								}, waitTime);
							};
						check();
					},
					executeFunctionByName: function(name, args, context) {
						var fn = methods.functionExists(name, context);
						if (fn) {
							fn.apply(this, args);
						}
					},
					functionExists: function(name, context) {
						var namespace;
						var func;
						if (typeof context !== "object") {
							context = _win;
						}
						if (typeof name !== "string") {
							return false;
						}
						namespace = name.split(".");
						func = namespace.pop();
						for (var i = 0, len = namespace.length; i < len; i++) {
							context = context[namespace[i]];
						}
						if (typeof context === "object" && typeof context[func] === "function") {
							return context[func];
						} else {
							throw new Error("[BBI.helper.functionExists] The function \"" + func + "\" was not found in the context specified.", false);
						}
					},
					getUrlVars: function(str) {
						var url;
						var vars = {};
						var parts;
						if (typeof str === "string") {
							url = str;
						} else {
							url = _win.location.href;
						}
						parts = _win.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
							vars[key] = value;
						});
						return vars;
					},
					isMobile: (function() {
						var a = navigator.userAgent;
						var is = {
							android: function() {
								return (a.indexOf("Android") > -1);
							},
							blackberry: function() {
								return (a.indexOf("BlackBerry") > -1);
							},
							ios: function() {
								return (a.indexOf("iPhone") > -1 || a.indexOf("iPad") > -1 || a.indexOf("iPod") > -1);
							},
							opera: function() {
								return (a.indexOf("Opera Mini") > -1);
							},
							windows: function() {
								return (a.indexOf("IEMobile") > -1);
							}
						};
						return {
							Android: is.android,
							BlackBerry: is.blackberry,
							iOS: is.ios,
							Opera: is.opera,
							Windows: is.windows,
							any: function() {
								return (is.android() || is.blackberry() || is.ios() || is.opera() || is.windows());
							}
						};
					})(),
					loadScript: function(src, callback) {
						if (typeof callback !== "function") {
							callback = function() {};
						}
						var s = _doc.createElement('script');
						s.type = 'text/' + (src.type || 'javascript');
						s.src = src.src || src;
						s.async = false;
						s.onreadystatechange = s.onload = function() {
							var state = s.readyState;
							if (!callback.done && (!state || /loaded|complete/.test(state))) {
								callback.done = true;
								callback();
							}
						};
						// use body if available. more safe in IE
						(_doc.body || head).appendChild(s);
					},
					objectLength: function(obj) {
						var counter = 0;
						for (var x in obj) {
							if (obj.hasOwnProperty(x)) {
								counter++;
							}
						}
						return counter;
					},
					scrollTo: function (target, speed, callback) {

						var element;

						if (typeof speed !== "number") {
							speed = 500;
						}

						if (typeof target === "string") {
							element = $(target);
						} else if (typeof target === "object") {
							element = target;
						} else {
							return false;
						}

					    $('html, body').animate({
					        scrollTop: element.offset().top
					    }, speed, function () {
						    if (typeof callback === "function") {
							    callback();
						    }
					    });

					},
					urlContains: function (keyword) {
						return _win.location.href.indexOf(keyword) > -1;
					}
				};
				return methods;
			}
		});



		var instance = bbi.instantiate(alias);



		bbi.map(alias, instance);



	});



}.call({}, window, bbiGetInstance()));
