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
