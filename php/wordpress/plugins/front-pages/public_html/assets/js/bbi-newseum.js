/*! (c) Blackbaud, Inc. */
window.bbiGetInstance()
.register({
	alias: "Newseum",
	author: "Steve Brush"
})
.action("TodaysFrontPages", function (app, bbi, $) {

	var _pinId = 0;
	var _mapItems = [];
	var _map;
	var _x = {
		pins: [],
		infoboxes: []
	};

	var methods = {
		events: function () {

			$(".tfp-disabled").on("click", function (e) {
				e.preventDefault();
				return false;
			});

			$(".tfp-utility select").on("change", function () {
				window.location.href = $(this).val();
			});

			$(".tfp-print").on("click", function (e) {
				e.preventDefault();
				window.print();
			});

			$(".tfp-back-button").on("click", function (e) {
				e.preventDefault();
				window.history.back();
				return false;
			});
		},
		map: {
			load: function (data) {

				Microsoft.Maps.registerModule("InfoboxAutoPanModule", "../wp-content/plugins/front-pages/public_html/assets/js/InfoboxAutoPanModule.js");

				Microsoft.Maps.loadModule("Microsoft.Maps.Themes.BingTheme", {
					callback: function () {
						Microsoft.Maps.loadModule("InfoboxAutoPanModule", {
							callback: function () {
						    	methods.map.create(data);
						    }
						});
					}
				});

			},
			create: function (data) {

				var appId = (typeof TFP_DATA === "object" && TFP_DATA.map) ? TFP_DATA.map : "";

				if (appId === "") {
					return false;
				}

				_map = new Microsoft.Maps.Map(document.getElementById("tfp-bing-map"), {
					credentials: appId,
					mapTypeId: Microsoft.Maps.MapTypeId.road
				});

				InitInfoboxAutoPanModule(_map, { horizontalPadding: 50, verticalPadding: 50 });

				var locations = [];
				for (var i = 0, len = data.papers.length; i < len; i++) {
					locations.push(methods.map.addPin(data.papers[i]));
				}

				methods.map.updateView(locations);

			},
			addPin: function (data) {
				var item = new MapItem(data)
				_mapItems.push(item);
				return item.location;
			},
			clearTimeouts: function (name) {
				for (var i = 0, len = _mapItems.length; i < len; i++) {
					window.clearTimeout(_mapItems[i].timeouts[name]);
				}
			},
			updateView: function (locations) {
				_map.setView({
					bounds: Microsoft.Maps.LocationRect.fromLocations(locations)
				});
			},
			hideAllInfoBoxes: function () {
				for (var i = 0, len = _x.infoboxes.length; i < len; i++) {
					_x.infoboxes[i].setOptions({
						visible: false
					});
				}
			},
			hideAllLoaders: function () {
				$(".tfp-loader").hide();
			}
		}
	};


	function MapItem (data) {

		_pinId++;

		var location = new Microsoft.Maps.Location(data.latitude, data.longitude);
		var pin = new Microsoft.Maps.Pushpin(location, {
			htmlContent: '<div id="tfp-pushpin-' + _pinId + '" class="tfp-pushpin"><div class="tfp-loader"></div></div>',
			width: 15,
			height: 15,
			icon: null
		});
		var infobox = new Microsoft.Maps.Infobox(pin.getLocation(), {
			title: data.title,
			titleClickHandler: function () {
				var win = window.open(data.links.detail, '_blank');
				win.focus();
			},
			description: '<p>' + data.location + '</p>' + '<p class="thumbnail tfp-thumbnail"><a href="' + data.links.detail + '" target="_blank"><img src="' + data.images.md + '"></a></p>',
			offset: new Microsoft.Maps.Point(0, 15),
			visible: false,
			showCloseButton: false,
			width: 200,
			height: 262,
			zIndex: _pinId,
			showPointer: true
		});
		infobox.myId = _pinId;
		var _timeouts = {};

		_map.entities.push(pin);
		_map.entities.push(infobox);

		_x.pins.push(pin);
		_x.infoboxes.push(infobox);

		Microsoft.Maps.Events.addHandler(pin, "click", function () {

			methods.map.hideAllInfoBoxes();
			methods.map.clearTimeouts("mouseover");
			methods.map.clearTimeouts("mouseout");
			methods.map.hideAllLoaders();

			infobox.setOptions({ visible: true });

		});

		Microsoft.Maps.Events.addHandler(pin, "mouseover", function (e) {

			var loader = $('#' + $(e.target._htmlContent).attr("id") + ' .tfp-loader').show();

			window.clearTimeout(_timeouts.mouseover);

			_timeouts.mouseover = window.setTimeout(function () {

				// Show the popup
				infobox.setOptions({ visible: true, zIndex: _mapItems.length, autoPan: true });
				loader.hide();
				//methods.map.updateView([infobox.getLocation()]);
				//var $map = $('#tfp-bing-map');
				//$map.find('.Infobox').parent().prependTo($map);

			}, 500);

		});

		Microsoft.Maps.Events.addHandler(pin, "mouseout", function (e) {

			var loader = $('#' + $(e.target._htmlContent).attr("id") + ' .tfp-loader').hide();

			window.clearTimeout(_timeouts.mouseout);

			_timeouts.mouseout = window.setTimeout(function () {

				// Hide the popup
				infobox.setOptions({ visible: false, zIndex: infobox.myId });

			}, 2000);

		});

		Microsoft.Maps.Events.addHandler(infobox, "mouseenter", function () {
			methods.map.clearTimeouts("mouseover");
			window.clearTimeout(_timeouts.mouseleave);
			window.clearTimeout(_timeouts.mouseout);
		});

		Microsoft.Maps.Events.addHandler(infobox, "mouseleave", function () {
			window.clearTimeout(_timeouts.mouseleave);
			_timeouts.mouseleave = window.setTimeout(function () {
				infobox.setOptions({ visible: false });
			}, 1000);
		});

		return {
			location: location,
			timeouts: _timeouts
		};
	}


	return {
		init: function () {

			$(function () {

				methods.events();

				if (typeof TFP_DATA === "object" && TFP_DATA.options.display === "map") {
					methods.map.load(TFP_DATA);
				}

			});

		}
	};


})



.build();
