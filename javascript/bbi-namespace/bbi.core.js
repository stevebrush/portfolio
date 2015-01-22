/*! BBI Core (c) Blackbaud, Inc. */
(function(_win) {
    "use strict";



    var _$;
    var _core;
    var _defaults = {
        alias: "BBI"
    };
    var _namespace = {};
    var _options = {};
    var _scope = {};
    var _settings = {};
    var _on = {
        complete: [],
        preload: [],
        init: []
    };




    function BuildNamespace (base, methods) {
        for (var i in _namespace) {
            delete _namespace[i];
        }
        _namespace = base;
        for (var k in methods) {
            _namespace[k] = methods[k];
        }
        return _namespace;
    }




    function On (when, directive) {
        var list = [];
        var i;
        var length;
        if (typeof directive === "function") {
            _on[when].push(directive);
            return _namespace;
        }
    }




    function Start (options) {

        // Clean up the namespace public methods.
        BuildNamespace(_core, {
            defaults: function(key, value) {
                return GetSet.bind(_defaults).call({}, key, value) || false;
            },
            options: function(key, value) {
                return GetSet.bind(_options).call({}, key, value) || false;
            },
            settings: function(key, value) {
                return GetSet.bind(_settings).call({}, key, value) || false;
            },
            on: On,
            trigger: Trigger
        });

        // Set options
        _options = (typeof options === "object") ? options : {};


		// Extension and jQuery built here.
        Trigger("preload");


        // Set jQuery.
        _$ = _namespace.jQuery();

        // Compile settings.
        _settings = _$.extend(true, {}, _defaults, _options);

        // Extensions will be built here.
        Trigger("init");

        // If an older version of BBI already exists on the page,
        // extend it so we can try to preserve its functionality for older customizations.
        // Or, we can use a custom alias provided by the initializer.
        if (typeof _win[_settings.alias] === "object") {

	        var winBBI = _win[_settings.alias];

            for (var n in winBBI) {
	            if (_namespace.hasOwnProperty(n)) {
		            continue;
	            }
	            _namespace[n] = winBBI[n];
            }

            delete _win[_settings.alias];

            _namespace.log("[BBI.core.Start] An instance of " + _settings.alias + " already exists on the page. For now, the namespace will extend itself to the existing reference; however, it may be a good idea to provide a custom alias to the namespace's initializing function. For example: bbi.init({ alias: 'NewBBI' }).", false);

        }

        // Add the namespace to the window.
        _win[_settings.alias] = _namespace;
        if (_namespace.isDebugMode()) {
            _namespace.log("Blackbaud JavaScript namespace set to: window." + _settings.alias, false);
        }

        // Remove the ability to subscribe.
        delete _namespace["on"];

        // Execute post-initialize event.
        Trigger("complete");

    };




    function Trigger (when, callback) {
        if (typeof _on[when] !== "undefined") {
            var list = _on[when];
            var length = list.length;
            var i;
            for (i = 0; i < length; i++) {
                list[i].call({});
            }
            if (typeof callback === "function") {
                callback();
            }
        }
    }




    function PrepareWindow () {
        // Window access methods.
        _win["bbiGetInstance"] = function() {
            return _namespace;
        };
    }




    function GetSet (key, value) {
        // Set the entire object.
        if (typeof key === "object") {
            for (var k in key) {
                this[k] = key[k];
            }
        } else if (typeof key === "string") {
            // Set a key.
            if (typeof value !== "undefined") {
                this[key] = value;
            }
            // Get a key's value.
            else {
                return this[key];
            }
        }
        // Return the entire object.
        return this;
    }



    var __construct = (function () {
        _core = function(key, value) {
            return GetSet.bind(_scope).call({}, key, value) || false;
        };
        BuildNamespace(_core, {
            init: Start,
            on: On,
            trigger: Trigger,
            yield: function(fn) {
                if (typeof fn === "function") {
                    fn.call({}, this);
                }
                return _namespace;
            }
        });
        PrepareWindow();
    })();



}.call({}, window));
