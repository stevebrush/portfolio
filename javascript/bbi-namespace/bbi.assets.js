/*! BBI Assets Handler (c) Blackbaud, Inc. */
(function (_win, bbi) {
	"use strict";



	var alias = "assets-handler";



	bbi.on("init", function () {



		bbi.extension({
			alias: alias,
			defaults: {
				scriptLoaderUrl: '//api.blackbaud.com/services/asset-loader/index.php'
			},
			directive: function (ext, bbi, $) {



				var settings = ext.settings();
				var bbiSettings = bbi.settings();

				var _dependencies = {
					"accordion-content": ["handlebars", "handlebars-helpers", "accordion-content"],
					"bb-twitter-feed": ["handlebars", "moment", "bb-twitter-feed"],
					"bbnc-carousel": ["handlebars", "handlebars-helpers", "simple-carousel", "bbnc-carousel"],
					"bbnc-donation": ["handlebars", "handlebars-helpers", "bbnc-donation"],
					"bbnc-localize-parts": ["sessvars", "cookie", "bbnc-localize-parts"],
					"bbnc-virtual-tour": ["jquery-tools", "jquery-easing", "png-fix", "hover-intent", "slideset-1.0.0", "bbnc-virtual-tour"],
					"flickr-gallery": ["swipebox", "handlebars", "flickr-gallery"],
					"font-resizer": ["cookie", "font-resizer"],
					"google-maps": ["handlebars", "google-maps"],
					"handlebars-helpers": ["handlebars", "handlebars-helpers"],
					"mega-menu": ["handlebars", "handlebars-helpers", "mega-menu"],
					"parse-rss": ["handlebars", "sessvars", "moment", "xdomainrequest", "parse-rss"],
					"simple-carousel": ["handlebars", "handlebars-helpers", "simple-carousel"],
					"slideset": ["jquery-tools", "slideset"],
					"slideset-1.0.0": ["jquery-tools", "slideset-1.0.0"],
					"youtube-gallery": ["handlebars", "handlebars-helpers", "youtube-gallery"]
				};

				var _assets = [];
				var _events = bbi("events").getInstance();
				var _isDebugMode = bbi.isDebugMode();



				function CheckDependencies (query) {

					var queryLength = query.length;
					var label;
					var temp = [];

					if (queryLength > 0) {
						for (var i = 0; i < queryLength; i++) {

							label = query[i];

							if (_dependencies.hasOwnProperty(label)) {
								// A query was found in the dependencies array
								// Add the dependencies array for this particular query
								temp.push.apply(temp, _dependencies[label]);
							} else {
								temp.push(label);
							}
						}
					}

					return temp;

				}



				function CheckLoaded (callback) {

					var loaded = true;

					for (var k in _assets) {
						if (_assets[k].loaded === false) {
							if (_isDebugMode) {
								bbi.log("Asset not yet loaded: " + k, false);
							}
							loaded = false;
							break;
						}
					}

					if (loaded) {
						callback();
					} else {
						if (typeof myTimeout101 === "undefined") {
							(function () {
								var counter = 0;
								var max = 200; // 10 seconds
								var myTimeout101 = _win.setTimeout(function () {
									counter++;
									CheckLoaded(callback);
									checkTimer();
								}, 50);
								var checkTimer = function () {
									if (counter > max) {
										_win.clearTimeout(myTimeout101);
										if (_isDebugMode) {
											bbi.log("Asset waiting period reached its max. Quitting.", false);
										}
									}
								};
							})();
						}
					}

				}



				function IsUnique (label) {

					var unique = (_assets.hasOwnProperty(label) === false);

					if (_isDebugMode === true) {
						console.log("Assets currently loaded: ", _assets);
					}

					if (! unique) {
						if (_isDebugMode === true) {
							bbi.log("Asset already loaded, and will be ignored: " + label, false);
						}
					}

					return unique;

				}



				function LoadAssets (queue, loadCSS, callback) {

					var queueLength = queue.unique.length;

					if (queueLength > 0) {

						if (_isDebugMode) {
							bbi.log("Attempt to load assets (with dependencies): " + queue.unique.join(","), false);
						}

						var url = settings.scriptLoaderUrl + "?query=" + queue.unique.join(",") + "&include_css=" + loadCSS.toString() + "&namespace_alias=" + bbiSettings.alias;

						if (_isDebugMode) {
							bbi.log("Requesting assets via: " + url, false);
						}

						bbi.helper.loadScript(url, function () {
							if (_isDebugMode) {
								bbi.log("Required assets loaded: " + queue.unique.join(", "), false);
							}
							for (var i = 0, len = queue.unique.length; i < len; i++) {
								_assets[queue.unique[i]].loaded = true;
							}
							CheckLoaded(callback);
						});

					} else {
						CheckLoaded(callback);
					}

				}



				function RegisterAsset (label) {

					if (_isDebugMode === true) {
						bbi.log("Registering unique asset: " + label, false);
					}

					_assets[label] = {
						loaded: false
					};

				}



				function RegisterUnique (query) {

					var label;
					var temp = {
						unique: [],
						requested: query
					};

					for (var i = 0, len = query.length; i < len; i++) {

						label = query[i];

						if (_isDebugMode === true) {
							bbi.log("Is the asset '" + label + "' unique?", false);
						}

						// This is the first time we've checked,
						// so just add the entire query at once,
						// Only add the Asset if it's unique
						if (IsUnique(label)) {

							RegisterAsset(label);

							temp.unique.push(label);

						}
					}

					return temp;

				}



				function Require (query, callback, loadCSS) {

					var queue = [];

					if (_isDebugMode) {
						bbi.log("Requesting assets: " + query.join(", "), false);
					}

					// Save callback inside an event
					// This will be triggered when all assets have been loaded
					var _onComplete = function () {

						if (_isDebugMode) {
							bbi.log("Required assets loaded, firing callback.", false);
						}

						// Send the window's version of jQuery, in case we're using noConflict.
						var _win$ = bbi.jQuery("window");
						if (typeof _win$ === "function") {
							callback(_win$);
							return;
						}

						// Alright, we must be using Luminate Online, which loads jQuery asynchronously via Yahoo.
						bbi("luminate").getInstance(0).fetchYahoo(function (a) {
							callback(a);
						});

					};

					// Make sure assets are passed as an array
					if (Object.prototype.toString.call(query) !== "[object Array]") {
						throw new Error("Invalid types passed to BBI.require(). This method accepts two arguments: an Array and a Function.");
					}

					// Should we include the assets' stylesheets?
					if (typeof loadCSS !== "boolean") {
						loadCSS = true;
					}

					var query = CheckDependencies(query);
					var queue = RegisterUnique(query);

					LoadAssets(queue, loadCSS, _onComplete);

				}



				return {
					require: Require
				};

			}
		});



		bbi.map("require", bbi.instantiate(alias).require);



	});



}.call({}, window, bbiGetInstance()));
