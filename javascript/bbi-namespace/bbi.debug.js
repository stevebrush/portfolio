/*! BBI Debug (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	var alias = "debug";



	bbi.on("init", function() {



		bbi.extension({
			alias: alias,
			defaults: {
				bbiStylesHref: '//api.blackbaud.com/assets/namespace/2.0.0/css/styles.min.css',
				debug: false,
				isAdminView: false,
				isPageEditor: false,
				isPartEditor: false,
				bbiLogContainerDisclaimer: '* This message pane is visible only to administrators.',
				bbiLogContainerId: 'bbi-message',
				bbiLogPrependSelector: 'body',
				bbiLogContainerTitle: 'Customization Alerts:',
				loadBBIStyles: true,
				pageEditorUrlRegex: 'edit=|/cms/',
				adminViewSelector: '.bb_menu'
			},
			directive: function(ext, bbi, $) {
				var settings = ext.settings();
				var methods = {
					info: function() {
						var settingsString = "";
						var bbiSettings = bbi.settings();
						for (var s in bbiSettings) {
							settingsString += "[" + s + "] " + bbiSettings[s] + "\n";
						}
						console.log("[ " + bbiSettings.alias + " Scope, exposed via: BBI(slug) ]\n", bbi());
						console.log("\nExplicit Settings:\n" + settingsString + "\n");
					},
					isAdminView: function() {
						if (settings.isAdminView === true) {
							return true;
						}
						settings.isAdminView = !! $(settings.adminViewSelector).length;
						return settings.isAdminView;
					},
					isDebugMode: function() {
						return settings.debug;
					},
					isPageEditor: function() {
						if (settings.isPageEditor === true) {
							return true;
						}
						settings.isPageEditor = !! _win.location.href.match(settings.pageEditorUrlRegex);
						return settings.isPageEditor;
					},
					isPartEditor: function() {
						if (typeof BLACKBAUD !== "object") {
							return false;
						}
						if (settings.isPartEditor === true) {
							return true;
						}
						settings.isPartEditor = (typeof BLACKBAUD.api.customPartEditor === "object");
						return settings.isPartEditor;
					},
					log: function(message, addToDOM) {
						console.log("[BBI.debug.log]", message);
						if (typeof addToDOM !== "boolean") {
							addToDOM = true;
						}
						if (addToDOM === false || settings.isAdminView === false) {
							return;
						}
						var container = $('#' + settings.bbiLogContainerId + ' .bbi-message-list');
						var html = '<li>' + message + '</li>';
						if (container.length) {
							$(container).append(html);
						} else {
							$(settings.bbiLogPrependSelector).prepend('<div id="' + settings.bbiLogContainerId + '"><h4 class="bbi-message-title">' + settings.bbiLogContainerTitle + '</h4><ul class="bbi-message-list">' + html + '</ul><p class="bbi-message-helplet">' + settings.bbiLogContainerDisclaimer + '</p></div>');
						}
					},
					preparePage: function() {
						var body = _win.document.getElementsByTagName('body')[0];
						var className = methods.isPageEditor() ? 'isEditor' : 'isViewer';
						if (methods.isAdminView() === true) {
							className += " isAdmin";
						}
						if (methods.isDebugMode() === true) {
							className += " isDebug";
						}
						body.className += (body.className == '' ? '' : ' ') + className;
						if (settings.loadBBIStyles) {
							if (_win.document.createStyleSheet) {
								_win.document.createStyleSheet(settings.bbiStylesHref);
							} else {
								$('head').append('<link rel="stylesheet" href="' + settings.bbiStylesHref + '" />');
							}
							if (methods.isDebugMode() === true) {
								methods.log("BBI stylesheet loaded.", false);
							}
						}
					},
					safeConsole: function() {
						_win.log = function() {
							// Make it safe to use console.log always
							log.history = log.history || [];
							log.history.push(arguments);
							if (_win.console) {
								arguments.callee = arguments.callee.caller;
								var a = [].slice.call(arguments);
								if (typeof console.log === "object") {
									log.apply.call(console.log, console, a);
								} else {
									console.log.apply(console, a);
								}
							}
						};
						(function(d) {
							// Add properties to console object, if they exist.
							// Otherwise, assign empty function.
							var f;
							var fns = ['assert', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'timeStamp', 'profile', 'profileEnd', 'time', 'timeEnd', 'trace', 'warn'];
							var a = function() {};
							while (f = fns.pop()) {
								d[f] = d[f] || a;
							}
						})((function() {
							// Return _win.console if it exists.
							// Otherwise, return an empty object.
							try {
								console.log();
								return _win.console;
							} catch (b) {
								return _win.console = {};
							}
						})());
					}
				};
				var __construct = (function() {
					methods.safeConsole();
					methods.isPageEditor();
					bbi.jQuery()(function() {
						methods.isAdminView();
						methods.preparePage();
					});
				}());
				return {
					info: methods.info,
					isAdminView: methods.isAdminView,
					isDebugMode: methods.isDebugMode,
					isPageEditor: methods.isPageEditor,
					isPartEditor: methods.isPartEditor,
					log: methods.log
				};
			}
		});



		var instance = bbi.instantiate(alias, bbi.options());



		bbi.map("info", instance.info);
		bbi.map("isAdminView", instance.isAdminView);
		bbi.map("isDebugMode", instance.isDebugMode);
		bbi.map("isPageEditor", instance.isPageEditor);
		bbi.map("isPartEditor", instance.isPartEditor);
		bbi.map("log", instance.log);



	});



}.call({}, window, bbiGetInstance()));
