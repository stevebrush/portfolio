(function (angular, console) { "use strict";



    console.log("Loading directives...Done.");



    var app = angular.module('Station');



    app.directive('stInventory',
        function () {
            return {
                templateUrl: 'app/partials/inventory.html',
                restrict: 'E',
                replace: true,
                scope: true,
                bindToController: {
                    owner: '=',
                    action: '=',
                    showMoney: '=',
                    isOverage: '='
                },
                controller: "InventoryCtrl as inventory"
            };
        });



    app.directive('stVessel',
        function () {
            return {
                templateUrl: 'app/partials/vessel.html',
                restrict: 'E',
                replace: true,
                scope: true,
                bindToController: {
                    roomPlacementId: '=',
                    action: '='
                },
                controller: "VesselCtrl as vessel"
            };
        });



    app.directive('stShop',
        function () {
            return {
                templateUrl: 'app/partials/shop.html',
                restrict: 'E',
                replace: true,
                scope: true,
                bindToController: {
                    roomPlacementId: '=',
                    action: '='
                },
                controller: "ShopCtrl as shop"
            };
        });




}(window.angular, window.console));
