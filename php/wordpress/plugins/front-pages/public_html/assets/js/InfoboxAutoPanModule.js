/*******************************************************************************
* Author: Pedro Sousa
* Website: http://build-failed.blogspot.com
* Date: July 12th, 2013
* 
* Description: 
* Adds an additional parameter to the infobox to allow it to automatically pan the map to be fully visible
* 
* Example Usage:
*
* function loadMap() {
*
*	  Microsoft.Maps.registerModule("InfoboxAutoPanModule", "scripts/InfoboxAutoPanModule.min.js");
*     Microsoft.Maps.loadModule("InfoboxAutoPanModule", { callback: function () {

            var map = new Microsoft.Maps.Map(document.getElementById("myMap"),{ credentials: "YOUR_BING_MAPS_KEY" });

            var infobox = new MM.Infobox();
            infobox.setOptions({visible: false, autoPan: true});

*     }});
* }
*
********************************************************************************/

var InitInfoboxAutoPanModule = function (mapInstance, options) {

    var _map = mapInstance;

    var _options = {
        horizontalPadding: 5,
        verticalPadding: 5
    };

    /*********************** Private Methods ****************************/

    //Initialization method
    function _init() {
        _setOptions(options);

        Microsoft.Maps.Infobox.prototype._oldSetOptions = Microsoft.Maps.Infobox.prototype.setOptions;

        Microsoft.Maps.Infobox.prototype.setOptions = function(arguments) {

            this._oldSetOptions(arguments);

            if(arguments.autoPan == true && arguments.visible == true) {

                var infobox = this;
                var mapWidth = _map.getWidth();
                var mapHeight = _map.getHeight();

                var point = _map.tryLocationToPixel(infobox.getLocation());

                var remainderX = (mapWidth / 2) - point.x;
                var remainderY = (mapHeight / 2) + point.y;

                //Empirical values based on the current infobox implementation
                var xExtraOffset = 33;
                var yExtraOffset = 37;

                var pixelsOutsideX = infobox.getWidth() + infobox.getOffset().x - remainderX - xExtraOffset + _options.horizontalPadding;
                var pixelsOutsideY = infobox.getHeight() + infobox.getOffset().y + yExtraOffset - remainderY + _options.verticalPadding;

                var newPoint = new Microsoft.Maps.Point(0, 0);

                if (pixelsOutsideX > 0) {
                    newPoint.x += pixelsOutsideX;
                }

                if (pixelsOutsideY > 0) {
                    newPoint.y -= pixelsOutsideY;
                }

                var newLocation = _map.tryPixelToLocation(newPoint);

                _map.setView({
                    center: new Microsoft.Maps.Location(newLocation.latitude, newLocation.longitude)
                });
            }
        }
    }

    /*
     * Overrides any of the default settings
     */
    function _setOptions(options) {
        for (attrname in options) {
            _options[attrname] = options[attrname];
        }
    }

    _init();

};

//Call the Module Loaded method
Microsoft.Maps.moduleLoaded('InfoboxAutoPanModule');