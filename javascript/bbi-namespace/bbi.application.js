/*! BBI Application (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	/**
	 * The App object represents each of the customizations on the page.
	 * Applications live in separate files, and are activated when BBI has loaded.
	 * Ideally, the applications will be loaded from "psuedo" script tags,
	 * using the "data-bbi-src" attribute (see _Script object below).
	 **/



	var alias = "app";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {
				alias: "MyApp",
				assignment_numbers: "",
				author: "First Last",
				client: "",
				created: "mm/dd/yyyy",
				requires: {
					assets: [],
					loadCSS: true
				},
				changelog: []
			},
			directive: function(ext, bbi, $) {



				var that = this;
				var _appsHandler = bbi("applications-handler").getInstance();
				var _events = bbi("events").getInstance();



				that.settings = ext.settings();
				that.actions = {};
				that.alias = "";
				that.scope = {};
				that.status = {
					loaded: false,
					ready: false
				};



				var methods = {
					action: function (name, func) {

						if (that.actions.hasOwnProperty(name)) {
							throw new Error("The name you provided for the action \"" + name + "\" already exists in the app \"" + that.settings.alias + "\". Action names must be unique.");
						}

						if ("function" === typeof name) {
							that.actions.push(name);
						} else if ("string" === typeof name && "function" === typeof func) {
							that.actions[name] = func;
						} else {
							throw new Error("The name and function you provided for .action() were incorrect types.");
						}

						return {
							action: methods.action,
							build: methods.build
						};

					},
					build: function () {

						// Set loaded state
						that.status.ready = true;
						if (bbi.isDebugMode() === true) {
							bbi.log(that.alias + " ready. [Event: bbi-" + that.alias + "-ready]", false);
						}
						_events.trigger('bbi-' + that.alias + '-ready');

						// Load requirements
						if (that.settings.requires && that.settings.requires.assets.length) {
							bbi.require(that.settings.requires.assets, function () {
								if (bbi.isDebugMode() === true) {
									bbi.log(that.alias + "'s assets have been loaded.", false);
								}
								that.status.loaded = true;
								_appsHandler.check();
							}, that.settings.requires.loadCSS);
						} else {
							that.status.loaded = true;
							_appsHandler.check();
						}

						// Return the compiled application
						return {
							actions: that.actions,
							scope: that.scope,
							settings: that.settings
						};

					},
					compile: function () {

						// Compile scope:
						if ("function" === typeof that.scope) {
							that.scope = that.scope({
								alias: that.alias,
								settings: that.settings
							}, bbi, $);
						} else if ("object" !== typeof that.scope) {
							that.scope = {};
						}

						// Compile actions:
						// Convert the action's function into a usable object,
						// or, if it doesn't return anything, run it globally
						var action;

						for (var b in that.actions) {
							action = that.actions[b];
							if (typeof action === "function") {
								that.actions[b] = action.call(that, {
									actions: that.actions,
									alias: that.alias,
									scope: that.scope,
									settings: that.settings
								}, bbi, $);
							}
						}

						// Add application to Window.
						_win[that.alias] = {
							actions: that.actions,
							alias: that.alias,
							scope: that.scope,
							settings: that.settings
						};

						// Fire loaded event.
						if (bbi.isDebugMode() === true) {
							bbi.log(that.alias + " loaded. [Event: bbi-" + that.alias + "-loaded]", false);
						}
						_events.trigger('bbi-' + that.alias + '-loaded');

					},
					save: function() {
						_appsHandler.save(that.alias, that);
					},
					scope: function(func) {
						if (typeof func !== "function" && typeof func !== "object") {
							throw new Error("The argument passed to the scope() method either be an object {}, or a function that returns an object. For example: .scope({}) OR .scope(function () { return {} });");
						} else {
							that.scope = func;
						}
						return {
							action: methods.action,
							build: methods.build
						};
					},
					validate: function(opts) {
						var temp = bbi.helper.clone(opts);
						if (typeof opts !== "object") {
							throw new Error("The options passed via the register method must be of type 'object'.");
						}
						if (typeof temp.alias !== "string") {
							throw new Error("The app must have an alias.");
						}
						if (bbi.apps()[temp.alias]) {
							throw new Error("The app's alias, \"" + temp.alias + "\", already exists. Please choose another alias.");
						}
						if (typeof _win[temp.alias] !== "undefined") {
							throw new Error("The app's alias, \"" + temp.alias + "\" is being used by a global object with the same name. This may cause critical issues; changing the application's alias is highly recommended.");
						}
						if (typeof temp.author !== "string" || temp.author === "First Last") {
							throw new Error("Please specify the application's author, via: .register({ author: 'First Last' })");
						}
						if (Object.prototype.toString.call(temp.requires) === "[object Array]") {
							temp.requires = {
								assets: opts.requires,
								loadCSS: true
							};
						}
						return temp;
					}
				};



				var __construct = (function () {

					that.settings = methods.validate(that.settings);
					that.alias = that.settings.alias;
					that.compile = methods.compile;

					methods.save();

				}());



				return {
					action: methods.action,
					build: methods.build,
					scope: methods.scope
				};



			}
		});



	});



}.call({}, window, bbiGetInstance()));
