/*! BBI Namespace (c) Blackbaud, Inc. */
try {

    (function (_win) {
        "use strict";

        // Make sure jQuery is installed
        if (typeof _win.jQuery !== "function") {
            throw new Error("The BBI namespace requires jQuery 1.7.2 (or greater) to operate.");
        }

        /**
         * VARIABLE NAMING CONVENTIONS:
         * ============================
         * '_' (prefix) = private, non-static variable
         * '$' (prefix) = jQuery element
         * All-caps = private, static variable
         * '_' (prefix) + Uppercase-first = OOP Object
         * All lowercase = local-scoped variables
         * If possible, include the variable type as the first character in the variable
         */

        var _oBbi;
        var _oDoc = _win.document;
        
        var $ = _win.jQuery;
        var $root = _oDoc.getElementById("bbi-namespace");
        
        var DEFAULTS = {
            adminViewSelector: '.bb_menu',
            bbiLogContainerDisclaimer: '* This message pane is visible only to administrators.',
            bbiLogContainerId: 'bbi-message',
            bbiLogPrependSelector: 'body',
            bbiLogContainerTitle: 'Customization Alerts:',
            bbiStylesHref: '//api.blackbaud.com/assets/namespace/css/styles.min.css',
            dataAttr_action: 'data-bbi-action',
            dataAttr_app: 'data-bbi-app',
            dataAttr_script: 'data-bbi-src',
            debug: false,
            loadBBIStyles: true,
            pageEditorUrlRegex: 'edit=|/cms/',
            partTitleKeyword: 'Customization',
            scriptLoaderUrl: '//api.blackbaud.com/services/asset-loader/index.php'
        };
        
        var _oPrm;
        var _oScope = {
            apps: {},
            assets: {},
            tags: [],
            scripts: []
        };
        var _oSettings;
        
        var _bAppsLoaded = false;
        var _bAppsReady = false;
        var _bIsAdminView = false;
        var _bIsPageEditor = false;
        var _bIsPartEditor = false;
        var _bIsDebugMode = false;
        var _bUsesMicrosoftAjax = false;
        var _bVersionConflict = false;




        function __construct(options) {
            
            var m = _Method;
            
            _oSettings = $.extend(true, {}, DEFAULTS, options);
            _oPrm = m.getPageRequestManager();
            _bIsDebugMode = _bIsDebugMode;
            
            if (m.isPageEditor()) {
                m.showPartTitle();
            }
            
            $(function () {
                m.isAdminView();
                m.preparePage();
            });
            
            // Load scripts
            $(_oDoc).on('bbi-loaded', function () {
                _Script.findAll();
            });
            
            // Activate apps
            $(_oDoc).on('bbi-apps-ready', function () {
                m.apps.activate();
                $(function () {
                    _Tag.findAll();
                    m.trigger.ready();
                });
            });
            
            _oBbi = _win.BBI = {
                apps: m.getApps,
                attach: m.attach,
                info: m.getInfo,
                isAdminView: m.isAdminView,
                isPageEditor: m.isPageEditor,
                isPartEditor: m.isPartEditor,
                log: m.log,
                register: function (options) {
                    return new App(options);
                },
                require: m.require,
                settings: m.getSettings,
                storage: _Storage.build(),
                helper: _Helper
            };
            if (_bVersionConflict === true) {
                m.log("The BBI Namespace is being initialized more than once. Consider consolidating all references to the namespace into a single call.", _bIsDebugMode);
            }
            m.trigger.loaded();
        }



         /**
          * The App object represents each of the customizations on the page.
          * Applications live in separate files, and are activated when BBI has loaded.
          * Ideally, the applications will be loaded from "psuedo" script tags, 
          * using the "data-bbi-src" attribute (see _Script object below).
          **/
        function App(options) {
        
            var that = this;
            
            var defaults = {
                requires: {
                    assets: [],
                    loadCSS: true
                }
            };
            
            that.actions = {};
            that.alias = "";
            that.scope = {};
            that.settings = {};
            that.status = {
                loaded: false,
                ready: false
            };
            
            var methods = {
                action: function (name, func) {
                    if (that.actions.hasOwnProperty(name)) {
                        throw new Error("The name you provided for the action \"" + name + "\" already exists in the app \"" + alias + "\".");
                    }
                    if ("function" === typeof name) {
                        that.actions.push(name);
                    } 
                    else if ("string" === typeof name && "function" === typeof func) {
                        that.actions[name] = func;
                    } 
                    else {
                        throw new Error("The name and function you provided for .action() were incorrect types.");
                    }
                    return {
                        action: methods.action,
                        build: methods.build
                    };
                },
                build: function () {
                
                    // Set loaded state
                    that.status.loaded = true;
                    _Method.trigger.appLoaded(that.alias);
                    
                    // Load requirements
                    if (that.settings.requires && that.settings.requires.assets.length) {
                        _Method.require(that.settings.requires.assets, function () {
                            that.status.ready = true;
                            _Method.apps.check();
                        }, that.settings.requires.loadCSS);
                    } else {
                        that.status.ready = true;
                        _Method.apps.check();
                    }
                    
                    // Return the compiled application
                    return {
                        actions: that.actions,
                        scope: that.scope,
                        settings: that.settings
                    };
                    
                },
                save: function () {
                    _oScope.apps[that.alias] = that;
                },
                compile: function () {
                
                    // Compile scope:
                    if ("function" === typeof that.scope) {
                        that.scope = that.scope({
                            alias: that.alias,
                            settings: that.settings
                        }, _oBbi, $);
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
                            that.actions[b] = action({
                                actions: that.actions,
                                alias: that.alias,
                                scope: that.scope,
                                settings: that.settings
                            }, _oBbi, $);
                        }
                    }
                    
                    // Add application to Window:
                    _win[that.alias] = {
                        actions: that.actions,
                        alias: that.alias,
                        scope: that.scope,
                        settings: that.settings
                    };
                },
                scope: function (func) {
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
                validate: function (opts) {
                    var temp;
                    if (typeof opts !== "object") {
                        throw new Error("The options passed via the register method must be of type 'object'.");
                    }
                    temp = $.extend({}, true, defaults, opts);
                    if (typeof temp.alias !== "string") {
                        throw new Error("The app must have an alias.");
                    }
                    if (_oScope.apps[temp.alias]) {
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
            
            // Set
            that.settings = methods.validate(options);
            that.alias = that.settings.alias;
            that.compile = methods.compile;
            methods.save();
            
            return {
                action: methods.action,
                build: methods.build,
                scope: methods.scope
            };
        }
        
        
        
        
        var _Assets = (function () {
            var dependencies = {
                "accordion-content": ["handlebars", "handlebars-helpers", "accordion-content"],
                "bbnc-carousel": ["handlebars", "handlebars-helpers", "simple-carousel", "bbnc-carousel"],
                "bbnc-donation": ["handlebars", "handlebars-helpers", "bbnc-donation"],
                "bbnc-localize-parts": ["sessvars", "cookie", "bbnc-localize-parts"],
                "bbnc-virtual-tour": ["jquery-tools", "jquery-easing", "png-fix", "hover-intent", "slideset-1.0.0",  "bbnc-virtual-tour"],
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
            var queue = [];
            var methods = {
                check: function () {
                    var assetsLoaded = true;
                    var temp = _oScope.assets;
                    var list = [];
                    for (var x in temp) {
                        if (temp[x].loaded === false) {
                            assetsLoaded = false;
                            break;
                        }
                        list.push(x);
                    }
                    if (assetsLoaded) {
                        _Method.trigger.assetsLoaded(list);
                    }
                },
                checkDependencies: function (query) {
                    var queryLength = query.length;
                    var label;
                    var temp = [];
                    if (queryLength > 0) {
                        for (var i = 0; i < queryLength; i++) {
                            label = query[i];
                            if (dependencies.hasOwnProperty(label)) {
                                // A query was found in the dependencies array
                                // Add the dependencies array for this particular query
                                temp.push.apply(temp, dependencies[label]);
                            } else {
                                temp.push(label);
                            }
                        }
                    }
                    return temp;
                },
                getAll: function () {
                    return _oScope.assets;
                },
                queue: function (key) {
                    queue.push(key);
                },
                resetQueue: function () {
                    queue = [];
                },
                set: function (key, value) {
                    if (_oScope.assets.hasOwnProperty(key)) {
                        _oScope.assets[key] = $.extend(true, _oScope.assets[key], value);
                    } else {
                        _oScope.assets[key] = value;
                    }
                }
            };
            return {
                addDependencies: methods.addDependencies,
                check: methods.check,
                checkDependencies: methods.checkDependencies,
                getAll: methods.getAll,
                getQueue: function () {
                    return queue;
                },
                queue: methods.queue,
                resetQueue: methods.resetQueue,
                set: methods.set
            };
        }());
        
        
        
        
        var _Helper = {
            clone: function (obj) {
                return JSON.parse(JSON.stringify(obj));
            },
            data: function (elem) {
                var attrs = elem.attributes;
                var i = attrs.length;
                var temp = {};
                var name;
                while (i--) {
                    if (attrs[i]) {
                        name = attrs[i].name;
                        if (name.indexOf("data-") === 0) {
                            name = jQuery.camelCase(name.slice(5));
                            temp[name] = attrs[i].value
                        }
                    }
                }
                return temp;
            },
            executeFunctionByName: function (name, args, context) {
                var fn = _Helper.functionExists(name, context);
                if (fn) {
                    fn.apply(this, args);
                }
            },
            functionExists: function (name, context) {
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
                    throw new Error("The function \"" + func + "\" was not found in the context specified.", false);
                }
            },
            getUrlVars: function (str) {
                var url;
                var vars = {};
                var parts;
                if (typeof str === "string") {
                    url = str;
                } else {
                    url = _win.location.href;
                }
                parts = _win.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
                    vars[key] = value;
                });
                return vars;
            },
            isMobile: (function () {
                var a = navigator.userAgent;
                var is = {
                    android: function () {
                        return (a.indexOf("Android") > -1);
                    },
                    blackberry: function () {
                        return (a.indexOf("BlackBerry") > -1);
                    },
                    ios: function () {
                        return (a.indexOf("iPhone") > -1 || a.indexOf("iPad") > -1 || a.indexOf("iPod") > -1);
                    },
                    opera: function () {
                        return (a.indexOf("Opera Mini") > -1);
                    },
                    windows: function () {
                        return (a.indexOf("IEMobile") > -1);
                    }
                };
                return {
                    Android: is.android,
                    BlackBerry: is.blackberry,
                    iOS: is.ios,
                    Opera: is.opera,
                    Windows: is.windows,
                    any: function () {
                        return (is.android() || is.blackberry() || is.ios() || is.opera() || is.windows());
                    }
                };
            })(),
            loadScript: function (src, callback) {
                if (typeof callback !== "function") {
                    callback = function () {};
                }
                var s = _oDoc.createElement('script');
                s.type = 'text/' + (src.type || 'javascript');
                s.src = src.src || src;
                s.async = false;
                s.onreadystatechange = s.onload = function () {
                    var state = s.readyState;
                    if (!callback.done && (!state || /loaded|complete/.test(state))) {
                        callback.done = true;
                        callback();
                    }
                };
                // use body if available. more safe in IE
                (_oDoc.body || head).appendChild(s);
            },
            objectLength: function (obj) {
                var counter = 0;
                for (var x in obj) {
                    if (obj.hasOwnProperty(x)) {
                        counter++;
                    }
                }
                return counter;
            },
            urlContains: function (keyword) {
                return _win.location.href.indexOf(keyword) > -1;
            }
        };




        var _Method = {
            apps: {
                activate: function () {
                    var a;
                    var app;
                    var apps = _oScope.apps;
                    for (a in apps) {
                        app = apps[a];
                        app.compile();
                        _Method.trigger.appReady(a);
                    }
                },
                check: function () {
                    if (_bAppsLoaded === true && _bAppsReady === true) {
                        return;
                    }
                
                    var allLoaded = true;
                    var allReady = true;
                    
                    var apps = _oScope.apps;
                    var numApps = 0;
                    var numScripts = _oScope.scripts.length;
                    
                    for (var x in apps) {
                        numApps++;
                        if (apps[x].loaded === false) {
                            allLoaded = false;
                            break;
                        }
                        if (apps[x].ready === false) {
                            allReady = false;
                            break;
                        }
                    }
                    
                    // The number of unique scripts must be the same number of apps.
                    // (Each app has its own file, but can be initialized more than once.)
                    if (numApps === numScripts || numScripts === 0) {
                        if (_bAppsLoaded === false && allLoaded === true) {
                            _bAppsLoaded = true;
                            _Method.trigger.appsLoaded();
                        }
                        if (_bAppsReady === false && allReady === true) {
                            _bAppsReady = true;
                            _Method.trigger.appsReady();
                        }
                    }
                }
            },
            attach: function (fn, args, context) {
                $(function () {
                    $.proxy(fn, context)(args);
                });
                if (_bUsesMicrosoftAjax === true) {
                    _oPrm.add_endRequest(function () {
                        $.proxy(fn, context)(args);
                    });
                }
            },
            getApps: function () {
                var app;
                var apps = _oScope.apps;
                var temp = {};
                for (var a in apps) {
                    app = apps[a];
                    temp[a] = {
                        actions: app.actions,
                        alias: app.alias,
                        scope: app.scope,
                        settings: app.settings
                    };
                }
                return temp;
            },
            getAssets: function () {
                 return _oScope.assets;
            },
            getInfo: function () {
                var settingsString = "";
                for (var s in _oSettings) {
                    settingsString += "[" + s + "] " + _oSettings[s] + "\n";
                }
                console.log("Scope:\n", _oScope);
                console.log("\nBBI Settings:\n" + settingsString + "\n");
            },
            getPageRequestManager: function () {
                var prm = {};
                if (typeof _oPrm === "object") {
                    return _oPrm;
                }
                try {
                    prm = Sys.WebForms.PageRequestManager.getInstance();
                    _bUsesMicrosoftAjax = true;
                } catch (e) {
                    if (_bIsDebugMode === true) {
                        _Method.log(e.message, false);
                    }
                }
                return prm;
            },
            getSettings: function () {
                return _oSettings;
            },
            isAdminView: function () {
                if (_bIsAdminView === true) {
                    return true;
                }
                _bIsAdminView = !! $(_oSettings.adminViewSelector).length;
                return _bIsAdminView;
            },
            isPageEditor: function () {
                if (_bIsPageEditor === true) {
                    return true;
                }
                _bIsPageEditor = !! _win.location.href.match(_oSettings.pageEditorUrlRegex);
                return _bIsPageEditor;
            },
            isPartEditor: function () {
                if (typeof BLACKBAUD !== "object") {
                    return false;
                }
                if (_bIsPartEditor === true) {
                    return true;
                }
                _bIsPartEditor = (typeof BLACKBAUD.api.customPartEditor === "object");
                return _bIsPartEditor;
            },
            log: function (message, addToDOM) {
                console.log("[BBI.log]", message);
                if (typeof addToDOM !== "boolean") {
                    addToDOM = true;
                }
                if (addToDOM === false || _bIsAdminView === false) {
                    return;
                }
                var container = $('#' + _oSettings.bbiLogContainerId + ' .bbi-message-list');
                var html = '<li>' + message + '</li>';
                if (container.length) {
                    $(container).append(html);
                } else {
                    $(_oSettings.bbiLogPrependSelector).prepend('<div id="' + _oSettings.bbiLogContainerId + '"><h4 class="bbi-message-title">' + _oSettings.bbiLogContainerTitle + '</h4><ul class="bbi-message-list">' + html + '</ul><p class="bbi-message-helplet">' + _oSettings.bbiLogContainerDisclaimer + '</p></div>');
                }
            },
            preparePage: function () {
                var body = _oDoc.getElementsByTagName('body')[0],
                    className = _Method.isPageEditor() ? 'isEditor': 'isViewer';
                body.className += (body.className == '' ? '' : ' ') + className;
                if (_oSettings.loadBBIStyles) {
                    if (_oDoc.createStyleSheet) {
                        _oDoc.createStyleSheet(_oSettings.bbiStylesHref);
                    } else {
                        $('head').append('<link rel="stylesheet" href="' + _oSettings.bbiStylesHref + '" />');
                    }
                    if (_bIsDebugMode === true) {
                        _Method.log("BBI stylesheet loaded.", false);
                    }
                }
            },
            require: function (query, callback, loadCSS) {
                
                var assets;
                var assetsLength;
                
                var queryLength;
                var queryLabel;
                
                var queue;
                var queueLength;
                
                var url;
                
                var i;
                var x;
                var k;
                
                
                // Clean the queue so we don't add assets from previous .require() calls
                _Assets.resetQueue();
                
                
                // Save callback inside an event
                // This will be triggered when all assets have been loaded
                $(document).one('bbi-assets-loaded', callback);
                
                
                // Make sure assets are passed as an array
                if (Object.prototype.toString.call(query) !== "[object Array]") {
                    throw new Error("Invalid types passed to BBI.require(). This method accepts two arguments: an Array and a Function.");
                }
                
                // Should we include the assets' stylesheets?
                if (typeof loadCSS !== "boolean") {
                    loadCSS = true;
                }
                
                
                // Check dependecies against the query
                query = _Assets.checkDependencies(query);
                
                
                // Add the assets to the global Asset object, if unique
                assets = _Assets.getAll();
                assetsLength = _Helper.objectLength(assets);
                queryLength = query.length;
                for (i = 0; i < queryLength; i++) {
                    queryLabel = query[i];
                    
                    // This is the first time we've checked, 
                    // so just add the entire query at once
                    if (assetsLength === 0) {
                        _Assets.set(queryLabel, {
                            loading: true,
                            loaded: false
                        });
                        _Assets.queue(queryLabel);
                    } 
                    
                    // Only add the Asset if it's unique
                    else {
                        if (assets.hasOwnProperty(queryLabel) === false) {
                            _Assets.set(queryLabel, {
                                loading: true,
                                loaded: false
                            });
                            _Assets.queue(queryLabel);
                        } 
                        else if (_bIsDebugMode === true) {
                            Method.log("Asset already loaded, and will be ignored: " + queryLabel, false);
                        }
                    }
                }
                
                
                // Load the required assets
                queue = _Assets.getQueue();
                queueLength = queue.length;
                if (queueLength > 0) {
                    if (_bIsDebugMode === true) {
                        Method.log("Attempt to load assets: " + queue.join(","), false);
                    }
                    url = _oSettings.scriptLoaderUrl + "?query=" + queue.join(",") + "&include_css=" + loadCSS.toString();
                    _Helper.loadScript(url, function () {
                    
                        // Set each asset's loaded state
                        for (k = queueLength; k--;) {
                            if (assets.hasOwnProperty(queue[k])) {
                                _Assets.set(queue[k], {
                                    loaded: true
                                });
                            }
                        }
                        _Assets.check();
                    });
                } else {
                    _Assets.check();
                }
                
            },
            safeConsole: function () {
                _win.log = function () {
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
                    var a = function () {};
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
            },
            showPartTitle: function () {
                _Method.attach(function() {
                    var body = $('#BodyId');
                    if (body.find('.js-part-label').length === 0) {
                        body.find('[id*="_tdPartName"]:contains("' + _oSettings.partTitleKeyword + '")').each(function () {
                            var popup = $(this);
                            $('#' + popup.attr('id').replace('tdPartName', 'pnlPart')).prepend('<div class="js-part-label">' + popup.text() + ' <em>(click to modify)</em></div>');
                        });
                    }
                });
            },
            trigger: {
                /*
                Events (in order):
                    bbi-loaded          // namespace has been initialized
                    bbi-[alias]-loaded  // an app's build method has been called
                    bbi-apps-loaded     // custom scripts (data-bbi-src) have been loaded
                    bbi-apps-ready      // all apps have been initialized, and their global assets have been loaded
                    bbi-[alias]-ready   // an app has been initialized, and its assets have been loaded
                    bbi-assets-loaded   // assets loaded via BBI.require() have been loaded
                    bbi-ready           // all scripts, app assets, and apps have been loaded and initialized
                */
                loaded: function () {
                    if (_bIsDebugMode === true) {
                        _Method.log("BBI loaded. [Event: bbi-loaded]", false);
                    }
                    $(_oDoc).trigger('bbi-loaded').unbind('bbi-loaded');
                },
                ready: function () {
                    _Method.log("BBI ready. [Event: bbi-ready]", false);
                    $(_oDoc).trigger('bbi-ready').unbind('bbi-ready');
                    _Method.log("Type 'BBI.info()' in the console to view customization information for this page.", false);
                },
                appLoaded: function (alias) {
                    if (_bIsDebugMode === true) {
                        _Method.log(alias + " loaded. [Event: bbi-" + alias + "-loaded]", false);
                    }
                    $(_oDoc).trigger('bbi-' + alias + '-loaded').unbind('bbi-' + alias + '-loaded');;
                },
                appReady: function (alias) {
                    if (_bIsDebugMode === true) {
                        _Method.log(alias + " ready. [Event: bbi-" + alias + "-ready]", false);
                    }
                    $(_oDoc).trigger('bbi-' + alias + '-ready').unbind('bbi-' + alias + '-ready');
                },
                appsLoaded: function () {
                    if (_bIsDebugMode === true) {
                        _Method.log("Apps loaded. [Event: bbi-apps-loaded]", false);
                    }
                    $(_oDoc).trigger("bbi-apps-loaded").unbind("bbi-apps-loaded");
                },
                appsReady: function () {
                    if (_bIsDebugMode === true) {
                        _Method.log("Apps ready. [Event: bbi-apps-ready]", false);
                    }
                    $(_oDoc).trigger("bbi-apps-ready").unbind("bbi-apps-ready");
                },
                assetsLoaded: function (assetsArray) {
                    if (_bIsDebugMode === true) {
                        _Method.log("Assets loaded: " + assetsArray.join(", ") + " [Event: bbi-assets-loaded]", false);
                    }
                    $(_oDoc).trigger("bbi-assets-loaded");
                }
            }
        };
        
        
        
        /**
         * The _Script object looks for special tags on the page that have the attribute 'data-bbi-src'.
         * It then loads each of these scripts dynamically on the page, and lets BBI know when they've been loaded.
         **/
        var _Script = (function () {
            var methods = {
                find: function () {
                    $(function () {
                        var scripts = $('[' + _oSettings.dataAttr_script + '], [bbi-src]');
                        var length = scripts.length;
                        if (length > 0) {
                            for (var i = 0; i < length; i++) {
                                methods.process(scripts[i]);
                            }
                        }
                    });
                },
                isUnique: function (src) {
                    for (var i = 0, len = _oScope.scripts.length; i < len; i++) {
                        if (_oScope.scripts[i].src === src) {
                            return false;
                        }
                    }
                    // Check if there's another script with the same source attribute.
                    // We don't want to load duplicates of the same file.
                    return true;
                },
                process: function (elem) {
                    var script = {
                        src: elem.getAttribute('bbi-src') || elem.getAttribute(_oSettings.dataAttr_script)
                    };
                    if (script.src && methods.isUnique(script.src)) {
                        _oScope.scripts.push(script);
                        _Helper.loadScript(script.src);
                    }
                }
            };
            return {
                findAll: methods.find
            }
        }());
        
        
        
        
        var _Storage = (function () {
            var x = {};
            var methods = {
                clear: function (key) {
                    if (typeof key === "string") {
                        delete x[key];
                    } else {
                        x = undefined;
                    }
                },
                expose: function () {
                    return x;
                },
                get: function (key) {
                    return x[key];
                },
                set: function (key, value) {
                    if (typeof _win.sessvars === "object") {
                        throw new Error("You are attempting to use BBI.storage when Sessvars currently exists on the page. Sessvars overwrites the BBI storage object, so only one method can be used at a given time.");
                    }
                    x[key] = value;
                }
            };
            return {
                build: function () {
                    var temp;
                    var n = _win.name;
                    try {
                        if (n && n.length > 0) {
                            if (typeof $.parseJSON === "function") {
                                if (_bIsDebugMode === true) {
                                    _Method.log("Parsing Window.name via $.parseJSON:" + n, false);
                                }
                                temp = $.parseJSON(n);
                            } else {
                                if (_bIsDebugMode === true) {
                                    _Method.log("Parsing Window.name via eval():" + n, false);
                                }
                                temp = eval('(' + n + ')');
                            }
                        } else {
                            if (_bIsDebugMode === true) {
                                _Method.log("Window.name is empty; creating empty storage object.", false);
                            }
                            temp = {};
                        }
                    } catch (e) {
                        _Method.log("[BBI Storage Error]: " + e.message, false);
                    } finally {
                        if (typeof temp === "object") {
                            x = temp;
                        }
                    }
                    // When the page refreshes or is closed, 
                    // save the temp object into the Window
                    _win.onunload = function () {
                        if (typeof _win.sessvars !== "object") {
                            window.name = JSON.stringify(x);
                        }
                    };
                    return methods;
                }
            };
        }());
        
        
        
        /**
         * Tags function as each App's action initializers.
         * A tag is simply any HTML element with the "data-bbi-app" and "data-bbi-action" attributes appended to it.
         * When BBI locates these attributes, it will attempt to fire the "init" function returned by each action.
         * HTML5 data attributes will be sent as options to each specified action.
         **/
        var _Tag = (function () {
            var methods = {
                execute: function () {
                    var apps = _oScope.apps;
                    var tags = _oScope.tags;
                    var tag;
                    var context;
                    
                    // Give IE8 a chance to breathe
                    setTimeout(function () { 
                    
                        // Execute init function, passing in arguments
                        for (var i = 0, len = tags.length; i < len; i++) {
                            tag = tags[i];
                            
                            if (typeof tag.action === "string") {
                            
                                if (typeof apps[tag.app] !== "object") {
                                    _Method.log("The app with the alias \"" + tag.app + "\" does not exist, or the alias on the tag does not match the alias used to register the application: <div data-bbi-action=\"" + tag.app + "\" data-bbi-action=\"" + tag.action + "\"></div>\nIn some instances this error occurs when the namespace is being overwritten by another reference. Double-check that the namespace is only being initialized once on the page.");
                                }
                                
                                context = apps[tag.app].actions[tag.action];
                                
                                if (typeof context === "object" && typeof context.init === "function") {
                                    _Helper.executeFunctionByName("init", [tag.data, tag.element], context);
                                } else if (tag.data.length) {
                                    throw new Error("The action, " + tag.action + ", in the app, " + tag.app + ", is expecting to receive options but does not have an initializing function. Add an 'init' function to your action to receive options.", false);
                                } else {
                                    throw new Error("The action, " + tag.action + ", in the app, " + tag.app + ", does not exist. Double-check the data-bbi-action and data-bbi-app attributes on your tag.");
                                }
                            }
                        }
                    
                    }, 0);
                },
                find: function () {
                    var tags = $('[' + _oSettings.dataAttr_app + '], [bbi-app]');
                    var length = tags.length;
                    
                    if (length > 0) {
                        for (var i = 0; i < length; i++) {
                            methods.process(tags[i]);
                        }
                        
                        methods.execute();
                        
                    } else {
                        if (_bIsDebugMode === true) {
                            _Method.log("No tags found.", false);
                        }
                    }
                },
                process: function (elem) {
                    var app = elem.getAttribute('bbi-app') || elem.getAttribute(_oSettings.dataAttr_app);
                    var action = elem.getAttribute('bbi-action') || elem.getAttribute(_oSettings.dataAttr_action);
                    _oScope.tags.push({
                        app: app,
                        action: action,
                        data: _Helper.data(elem),
                        element: elem
                    });
                }
            };
            return {
                findAll: methods.find
            }
        }());




        // Initializers...
        (function () {
        
            // Only attach the init method to the BBI object
            // to force BBI to be initialized before it can be used.
            _oBbi = {
                init: __construct
            };
            
            // An older version of BBI already exists on the page.
            // Extend it so we can try to preserve its functionality for older customizations.
            if (typeof _win.BBI === "object") {
                _oBbi = $.extend(true, {}, _win.BBI, _oBbi);
                _bVersionConflict = true;
            }
            
            // Set the global BBI object so it can be referenced publicly.
            _win.BBI = _oBbi;
            
            // Make it safe to use console.log in IE:
            _Method.safeConsole();
            
            // First, look for global BBI options object
            if (typeof _win.BBIOPTIONS === "object") {
                __construct(_win.BBIOPTIONS);
                if (_bIsDebugMode === true) {
                    _Method.log("Global options object found. Initializing with options...", false);
                }
            }
            
            // If the global options object doesn't exist, 
            // execute the innerHTML of the namespace script tag
            else if ($root && $root.innerHTML !== "" && $root.innerHTML.indexOf("BBI.init") > -1) {
                eval($root.innerHTML);
                if (_bIsDebugMode === true) {
                    _Method.log("InnerHTML located in namespace script tag. Initializing with options...", false);
                }
            }
            
            // No initializers found.
            // Auto-initialize the namespace with defaults, if the asynchronous init function is not found.
            else if (typeof _win.bbiAsyncInit !== "function") {
                __construct();
                if (_bIsDebugMode === true) {
                    _Method.log("Options not found. Initializing with defaults...", false);
                }
            }
            
            // Execute global async function
            if (typeof _win.bbiAsyncInit === "function") {
                _win.bbiAsyncInit(_oBbi);
            }
             
        }());




    }.call({}, window));

} catch (e) {
    if (typeof window.console === "object") {
        console.log("[BBI Error] ", e.message);
    }
}
