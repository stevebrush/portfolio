/*! BBI Extension (c) Blackbaud, Inc. */
(function(_win, bbi) {
	"use strict";



	function Extension (obj) {

		var ext = {};

		ext.defaults = function() {
			return obj.defaults;
		};
		ext.instances = [];
		ext.getInstance = function(index) {
			if (typeof index === "undefined") {
				if (typeof this.instances[0] !== "undefined") {
					return this.instances[0];
				}
				return this.instances;
			}
			return this.instances[index];
		};
		ext.directive = obj.directive;

		bbi(obj.alias, ext);

		return bbi;

	}



	function Instantiate (slug, options) {

		var item = bbi(slug);
		var directive = item.directive;
		var _defaults = item.defaults();
		var _settings = {};
		var _$ = (typeof bbi.jQuery === "function") ? bbi.jQuery() : null;

		// Clone defaults.
		for (var d in _defaults) {
			_settings[d] = _defaults[d];
		}

		// Update settings with global options.
		for (var o in options) {
			if (_settings.hasOwnProperty(o)) {
				_settings[o] = options[o];
			}
		}

		// Add common functions.
		directive.get = function(key) {
			return this[key];
		};

		directive.merge = function(data) {
			for (var d in data) {
				if (this.hasOwnProperty(d)) {
					this[d] = data[d];
				}
			}
			return this;
		};

		directive.set = function(key, value) {
			this[key] = value;
			return this;
		};

		directive.defaults = function() {
			return _defaults;
		};

		directive.settings = function() {
			return _settings;
		};

		// Instantiate the object and save it to the global scope.
		var thing = directive.call({}, directive, bbi, _$);

		bbi(slug).instances.push(thing);

		return thing;

	}



	function Map (to, from) {
		if (bbi.hasOwnProperty(to) === false) {
			bbi[to] = from;
		} else {
			throw new Error('[BBI.extension.Map] The key "' + to + '" is already in use (' + bbi.settings().alias + '.' + to + '). Please choose another key.');
		}
	}



	bbi.on("preload", function() {
		Map("map", Map);
		Map("extension", Extension);
		Map("instantiate", Instantiate);
	});



}.call({}, window, bbiGetInstance()));
