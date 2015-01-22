/*! BBI Application Handler (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "applications-handler";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {},
			directive: function(ext, bbi, $) {


				var settings = ext.settings();
				var _apps = {};
				var _appsLoaded = false;
				var _appsReady = false;
				var _events = bbi("events").getInstance();


				var methods = {
					activate: function () {

						var a;
						var app;

						for (a in _apps) {

							app = _apps[a];
							app.compile();

						}

						if (bbi.isDebugMode() === true) {
							bbi.log("Apps loaded. [Event: bbi-apps-loaded]", false);
						}
						_events.trigger('bbi-apps-loaded');

					},
					check: function () {

						if (bbi.isDebugMode() === true) {
							bbi.log("Checking applications' status...", false);
						}

						if (_appsLoaded === true && _appsReady === true) {
							return;
						}

						var allLoaded = true;
						var allReady = true;

						var numApps = 0;
						var numScripts = (bbi("applications-script-handler")) ? bbi("applications-script-handler").getInstance(0).scripts().length : 0;

						for (var x in _apps) {
							numApps++;
							if (_apps[x].status.ready === false) {
								allReady = false;
								break;
							}
							if (_apps[x].status.loaded === false) {
								allLoaded = false;
								break;
							}
						}

						// The number of unique scripts must be the same number of apps.
						// (Each app has its own file, but can be initialized more than once.)
						if (numApps === numScripts || numScripts === 0) {

							// App has been built.
							if (_appsReady === false && allReady === true) {
								_appsReady = true;
								if (bbi.isDebugMode() === true) {
									bbi.log("Apps ready. [Event: bbi-apps-ready]", false);
								}
								_events.trigger('bbi-apps-ready');
							}

							// Required assets have been loaded.
							if (_appsLoaded === false && allLoaded === true) {
								_appsLoaded = true;
								methods.activate();
							}

						}

					},
					getApps: function () {
						var app;
						var temp = {};
						for (var a in _apps) {
							app = _apps[a];
							temp[a] = {
								actions: app.actions,
								alias: app.alias,
								scope: app.scope,
								settings: app.settings
							};
						}
						return temp;
					},
					register: function (options) {
						return bbi.instantiate("app", options);
					},
					save: function (alias, instance) {
						_apps[alias] = instance;
					}
				};


				return {
					activate: methods.activate,
					check: methods.check,
					getApps: methods.getApps,
					register: methods.register,
					save: methods.save
				};


			}
		});



		var instance = bbi.instantiate(alias);



		bbi.map("register", instance.register);
		bbi.map("apps", instance.getApps);



	});



}.call({}, window, bbiGetInstance()));
