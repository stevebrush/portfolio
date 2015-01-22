// Add .bind() for older browsers.
if (! Function.prototype.bind) {
    Function.prototype.bind = function (oThis) {

        if (typeof this !== 'function') {
            // closest thing possible to the ECMAScript 5
            // internal IsCallable function
            throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
        }

        var aArgs = Array.prototype.slice.call(arguments, 1);
        var fToBind = this;

        var fNOP = function () {};

        var fBound = function () {
            return fToBind.apply(this instanceof fNOP && oThis ? this : oThis, aArgs.concat(Array.prototype.slice.call(arguments)));
        };

        fNOP.prototype = this.prototype;
        fBound.prototype = new fNOP();

        return fBound;
    }
}




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




/*! BBI BBNC (c) Blackbaud, Inc. */
(function(_win, bbi) {
    "use strict";



    var alias = "bbnc";



    bbi.on("init", function() {



        bbi.extension({
            alias: alias,
            defaults: {
                partTitleKeyword: 'Customization'
            },
            directive: function(ext, bbi, $) {
                var settings = ext.settings();
                var _prm;
                var _usesMicrosoftAjax = false;
                var methods = {
                    attach: function(fn, args, context) {
                        $(function() {
                            $.proxy(fn, context)(args);
                        });
                        if (_usesMicrosoftAjax === true) {
                            _prm.add_endRequest(function() {
                                $.proxy(fn, context)(args);
                            });
                        }
                    },
                    getPageRequestManager: function() {
                        var prm = {};
                        if (typeof _prm === "object") {
                            return _prm;
                        }
                        try {
                            prm = Sys.WebForms.PageRequestManager.getInstance();
                            _usesMicrosoftAjax = true;
                        } catch (e) {
                            if (bbi.isDebugMode() === true) {
                                bbi.log(e.message, false);
                            }
                        }
                        return prm;
                    },
                    showPartTitle: function() {
                        methods.attach(function() {
                            var body = $('#BodyId');
                            if (body.find('.js-part-label').length === 0) {
                                body.find('[id*="_tdPartName"]:contains("' + settings.partTitleKeyword + '")').each(function() {
                                    var popup = $(this);
                                    $('#' + popup.attr('id').replace('tdPartName', 'pnlPart')).prepend('<div class="js-part-label">' + popup.text() + ' <em>(click to modify)</em></div>');
                                });
                            }
                        });
                    }
                };
                var __construct = (function() {
                    _prm = methods.getPageRequestManager();
                    if (bbi("debug").getInstance(0).isPageEditor()) {
                        methods.showPartTitle();
                    }
                }());
                return {
                    attach: methods.attach
                };
            }
        });



        var instance = bbi.instantiate(alias);



        bbi.map("attach", instance.attach);



    });



}.call({}, window, bbiGetInstance()));




/*! BBI Online Express (c) Blackbaud, Inc. */
(function(_win, bbi) {
    "use strict";



    var alias = "online-express";



    bbi.on("init", function() {



        bbi.extension({
            alias: alias,
            defaults: {},
            directive: function (ext, bbi, $) {

                var settings = ext.settings();

                var bbiReady = false;
                var olxReady = false;

                var bb$;

                var $doc = $(_win.document);
                var $btn;
                var $root;

                var _onError;
                var _onValidation;
                var _onFail;
                var _onSuccess;
                var _onBeforeUpdate;
                var _onAfterUpdate;

                var _updateForm;

                var methods = {
                    block: function () {
                        $root.block({
                            message: "Processing",
                            css: {
                                padding: "10px",
                                border: "none",
                                fontSize: "16px",
                                backgroundColor: "#000",
                                borderRadius: "10px",
                                "-webkit-border-radius": "10px",
                                "-moz-border-radius": "10px",
                                opacity: 0.5,
                                color: "#fff"
                            },
                            overlayCSS: {
                                backgroundColor: "#fff",
                                opacity: 0.5
                            }
                        });
                    },
                    check: function() {
                        if (bbiReady && olxReady) {
                            $doc.trigger("olx-ready");
                        }
                    },
                    on: function(when, fn) {
                        switch (when) {



                        case "error":
                            /**
                             * This function fires when errors are found on the page (pre-processing).
                             **/
                            _onError = fn;
                            break;



                        case "validate":
                            /**
                             * This function allows the developer to add their own validations.
                             * Returning "true" will allow the form to continue processing.
                             * Returning "false" will prevent the form from processing.
                             **/
                            _onValidation = fn;
                            break;



                        case "success":
                            /**
                             * The onSuccess function doesn't stop the form from submitting,
                             * it simply halts the form's update method, to let you do things
                             * before the user sees the confirmation screen.
                             **/
                            _onSuccess = fn;
                            break;



                        case "fail":
                            /**
                             * This function fires when submission errors are found on the page (post-processing).
                             **/
                            _onFail = fn;
                            break;



                        case "beforeUpdate":
                            /**
                             * This function fires before each form update.
                             **/
                            _onBeforeUpdate = fn;
                            break;



                        case "afterUpdate":
                            /**
                             * This function fires after each form update.
                             **/
                            _onAfterUpdate = fn;
                            break;



                        }
                    },
                    overrides: function () {

                        // Add our own validations object.
                        _win.BBOXSectionScripts.BBI_NAMESPACE = {
                            presubmit: function () {

                                var form = {
                                    block: methods.block,
                                    unblock: methods.unblock
                                };
                                var status;

                                if (bbi.isDebugMode()) {
                                    bbi.log("Online Express Form has been submitted. Validations in progress...", false);
                                }

                                // Scroll to top of page.
                                //bbi("helper").getInstance(0).scrollTo($root);

                                status = (typeof _onValidation === "function") ? _onValidation.call(form, form) : true;
                                if (bbi.isDebugMode()) {
                                    bbi.log("Online Express validated? " + status, false);
                                }
                                return status;

                            }
                        };

                        // Hijack the HTML as it is returned from post-processing.
                        _win.bboxOverrides = {
                            handleSubmitCallbackOverride: function (html) {

                                var numErrors = $(html).find('.BBFormErrorItem').length;
                                var form = {
                                    block: methods.block,
                                    update: methods.trigger
                                };

                                // Register the onUpdate functions.
                                _updateForm = function () {
                                    if (typeof _onBeforeUpdate === "function") {
                                        _onBeforeUpdate.call(form, form, html);
                                    }
                                    methods.update(html);
                                    if (typeof _onAfterUpdate === "function") {
                                        _onAfterUpdate.call(form, form, html);
                                    }
                                };

                                // There are errors on the page.
                                if (numErrors > 0) {
                                    if (typeof _onFail === "function") {
                                        _onFail.call(form, form, html);
                                    } else {
                                        methods.trigger();
                                    }
                                }

                                // No errors.
                                else {
                                    if (typeof _onSuccess === "function") {
                                        _onSuccess.call(form, form, html);
                                    } else {
                                        methods.trigger();
                                    }
                                }

                                // Update the form's HTML.
                                if (typeof _win.bbox.squirtMarkup === "function") {
                                    _win.bbox.squirtMarkup('<div class="bbi-olx-form-wrapper"><div class="alert alert-info bbi-olx-message">Processing, please wait...</div><div class="bbi-olx-form bbi-off">'+html+'</div></div>', true);
                                }
                            }
                        };

                        // Hijack the submit button
                        $btn.on("click", function () {
                            var form = {};
                            if ($('#divClientError').is(":visible")) {
                                if (typeof _onError === "function") {
                                    _onError.call(form, form);
                                }
                            }
                        });

                    },
                    trigger: function () {
                        $doc.trigger("olx-form-submitted");
                    },
                    unblock: function () {
                        $root.unblock();
                    },
                    update: function (html) {

                        // Give OLX time to breathe.
                        _win.setTimeout(function () {

                            var $root = bb$("#bbox-root");

                            $root.find('.bbi-olx-message').addClass('bbi-off');
                            $root.find('.bbi-olx-form').removeClass('bbi-off');

                            if (typeof bb$.fn.unblock === "function") {
                                $root.unblock();
                            }

                        }, 50);

                    }
                };



                var __construct = (function () {

                    $doc.off("olx-form-submitted").on("olx-form-submitted", function () {
                        if (typeof _updateForm === "function") {
                            _updateForm();
                        }
                    });

                    // OLX loaded.
                    _win.bboxShowFormComplete = function () {

                        // Get OLX's jQuery.
                        var j = bbi("jQuery").getInstance(0);
                        j.setLocation("olx", _win["bb$"]);
                        bb$ = j.jQuery("olx");

                        // Set the root variable.
                        $root = bb$('#bbox-root');

                        // Hijack the submit button.
                        $btn = $root.find('.BBFormSubmitbutton');

                        olxReady = true;
                        methods.check();
                    };

                    // BBI ready.
                    $doc.on("bbi-ready", function () {
                        bbiReady = true;
                        methods.check();
                    });

                    // OLX ready.
                    $doc.on("olx-ready", function () {
                        methods.overrides();
                    });

                }());



                return {
                    block: methods.block,
                    on: function (when, fn) {
                        if (olxReady) {
                            methods.on(when, fn);
                        } else {
                            $doc.on("olx-ready", function () {
                                methods.on(when, fn);
                            });
                        }
                    },
                    unblock: methods.unblock,
                    update: methods.trigger
                };



            }
        });



        var instance = bbi.instantiate(alias);



        bbi.map("olx", instance);



    });



}.call({}, window, bbiGetInstance()));




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




/*! BBI Application Script Handler (c) Blackbaud, Inc. */
(function(_win, bbi) {
    "use strict";



    /**
     * The _Script object looks for special tags on the page that have the attribute 'data-bbi-src'.
     * It then loads each of these scripts dynamically on the page, and lets BBI know when they've been loaded.
     **/



    var alias = "applications-script-handler";



    bbi.on("init", function () {



        bbi.extension({
            alias: alias,
            defaults: {
                dataAttr_script: 'data-bbi-src'
            },
            directive: function (ext, bbi, $) {



                var _settings = ext.settings();
                var _scripts = [];



                var methods = {
                    find: function() {
                        var scripts = $('[' + _settings.dataAttr_script + '], [bbi-src]');
                        var length = scripts.length;
                        var i;
                        if (length > 0) {
                            for (i = 0; i < length; i++) {
                                methods.process(scripts[i]);
                            }
                        }
                    },
                    getScripts: function() {
                        return _scripts;
                    },
                    isUnique: function(src) {
                        for (var i = 0, len = _scripts.length; i < len; i++) {
                            if (_scripts[i].src === src) {
                                return false;
                            }
                        }
                        // Check if there's another script with the same source attribute.
                        // We don't want to load duplicates of the same file.
                        return true;
                    },
                    process: function(elem) {
                        var script = {
                            src: elem.getAttribute('bbi-src') || elem.getAttribute(_settings.dataAttr_script)
                        };
                        if (script.src && methods.isUnique(script.src)) {
                            _scripts.push(script);
                            bbi.helper.loadScript(script.src);
                        }
                    }
                };



                var __construct = (function() {
                    $(_win.document).on('bbi-ready', function() {
                        $(methods.find);
                    });
                }());



                return {
                    scripts: methods.getScripts
                };



            }
        });



        var instance = bbi.instantiate(alias);



    });



}.call({}, window, bbiGetInstance()));




/*! BBI Applications Tag Handler (c) Blackbaud, Inc. */
(function(_win, bbi) {
    "use strict";



    /**
     * Tags function as each App's action initializers.
     * A tag is simply any HTML element with the "data-bbi-app" and "data-bbi-action" attributes appended to it.
     * When BBI locates these attributes, it will attempt to fire the "init" function returned by each action.
     * HTML5 data attributes will be sent as options to each specified action.
     **/



    var alias = "applications-tag-handler";



    bbi.on("init", function() {



        bbi.extension({
            alias: alias,
            defaults: {
                dataAttr_action: 'data-bbi-action',
                dataAttr_app: 'data-bbi-app'
            },
            directive: function(ext, bbi, $) {


                var _settings = ext.settings();
                var _appsHandler = bbi("applications-handler").getInstance(0);
                var _tags = [];


                var methods = {
                    execute: function() {

                        var apps = bbi.apps();
                        var tag;
                        var context;
                        var i;
                        var length = _tags.length;

                        // Give IE8 a chance to breathe
                        _win.setTimeout(function() {

                            // Execute init function, passing in arguments
                            for (i = 0; i < length; i++) {
                                tag = _tags[i];
                                if (typeof tag.action === "string") {
                                    if (typeof apps[tag.app] !== "object") {
                                        bbi.log("The app with the alias \"" + tag.app + "\" does not exist, or the alias on the tag does not match the alias used to register the application: <div data-bbi-action=\"" + tag.app + "\" data-bbi-action=\"" + tag.action + "\"></div>\nIn some instances this error occurs when the namespace is being overwritten by another reference. Double-check that the namespace is only being initialized once on the page.");
                                    }
                                    context = apps[tag.app].actions[tag.action];
                                    if (typeof context === "object" && typeof context.init === "function") {
                                        bbi.helper.executeFunctionByName("init", [tag.data, tag.element], context);
                                    } else if (tag.data.length) {
                                        throw new Error("The action, " + tag.action + ", in the app, " + tag.app + ", is expecting to receive options but does not have an initializing function. Add an 'init' function to your action to receive options.", false);
                                    } else {
                                        throw new Error("The action, " + tag.action + ", in the app, " + tag.app + ", does not exist. Double-check the data-bbi-action and data-bbi-app attributes on your tag.");
                                    }
                                }
                            }
                        }, 0);
                    },
                    find: function() {
                        var tags = $('[' + _settings.dataAttr_app + '], [bbi-app]');
                        var length = tags.length;
                        var i;
                        if (length > 0) {
                            for (i = 0; i < length; i++) {
                                methods.process(tags[i]);
                            }
                            methods.execute();
                        } else {
                            if (bbi.isDebugMode() === true) {
                                bbi.log("No tags found.", false);
                            }
                        }
                    },
                    process: function(elem) {
                        var app = elem.getAttribute('bbi-app') || elem.getAttribute(_settings.dataAttr_app);
                        var action = elem.getAttribute('bbi-action') || elem.getAttribute(_settings.dataAttr_action);
                        _tags.push({
                            app: app,
                            action: action,
                            data: bbi.helper.data(elem),
                            element: elem
                        });
                    }
                };


                var __construct = (function() {
                    $(_win.document).on('bbi-apps-loaded', function() {
                        $(methods.find);
                    });
                }());


                return {};


            }
        });



        var instance = bbi.instantiate(alias);



    });



}.call({}, window, bbiGetInstance()));




/*! BBI Initializer (c) Blackbaud, Inc. */
(function(_win, bbi) {
    "use strict";



    var bbiSettings;
    var _events;



    bbi.on("init", function() {

        var $ = bbi.jQuery();
        bbiSettings = bbi.settings();
        _events = bbi("events").getInstance(0);

        $(_win.document).on('bbi-apps-loaded', function () {

            if (bbi.isDebugMode() === true) {
                bbi.log(bbiSettings.alias + " loaded. [Event: bbi-loaded]", false);
            }

            _events.trigger("bbi-loaded");

            bbi.log("Type '" + bbiSettings.alias + ".info()' in the console to view customization information for this page.", false);

        });

        // BBI is ready.
        bbi.log(bbiSettings.alias + " ready. [Event: bbi-ready]", false);
        _events.trigger("bbi-ready");

    });



    // Initialize with global options object.
    if (typeof _win.BBIOPTIONS === "object") {

        bbi.init(_win.BBIOPTIONS);

        if (typeof _win.console === "object") {
            console.log("[BBI.init] Global options object found. Initialized with options.");
        }
    }



    // Initialize with global function.
    else if (typeof _win.bbiAsyncInit === "function") {

        _win.bbiAsyncInit.call({}, bbi);

        if (typeof _win.console === "object") {
            console.log("[BBI.init] Initialized with options, via bbiAsyncInit().");
        }
    }



    // No initializer. Bootup with defaults.
    else {

        bbi.init({});

        if (typeof _win.console === "object") {
            console.log("[BBI.init] Options not found. Initialized with defaults.");
        }
    }



}.call({}, window, bbiGetInstance()));




