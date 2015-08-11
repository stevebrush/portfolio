(function (angular, console) { "use strict";



    console.log("Loading controllers...Done.");



    var app = angular.module('Station');



    app.controller('MapCtrl', [
        '$scope', 'regionService',
        function ($scope, regionService) {
            regionService.findAll().then(function (data) {
                $scope.regions = data;
            });
            $scope.preview = function (region) {
                $scope.showcase = region;
            };
        }
    ]);



    app.controller('RegionCtrl', [
        '$scope', '$routeParams', 'regionService',
        function ($scope, $routeParams, regionService) {
            regionService.find($routeParams.id).then(function (data) {
                $scope.region = data;
            });
        }
    ]);



    app.controller('LocationCtrl', [
        '$scope', '$routeParams', 'locationService', 'playerService',
        function ($scope, $routeParams, locationService, playerService) {

            $scope.showRoom = true;
            $scope.showVessel = false;
            $scope.showShop = false;

            $scope.openVessel = function (id) {
                console.log("Open vessel:", id);
                $scope.roomPlacementId = id;
                $scope.showRoom = false;
                $scope.showVessel = true;
            };

            $scope.closeVessel = function () {
                $scope.showRoom = true;
                $scope.showVessel = false;
                $scope.roomPlacementId = null;
            };

            $scope.openShop = function (id) {
                $scope.roomPlacementId = id;
                $scope.showRoom = false;
                $scope.showShop = true;
            };

            $scope.closeShop = function () {
                $scope.showRoom = true;
                $scope.showShop = false;
                $scope.roomPlacementId = null;
            };

            $scope.openEnemy = function (id) {
                console.log("Open enemy", id);
            };

            $scope.nextRoom = function (id) {
                console.log("Next room id:", id);
                for (var i in $scope.location.rooms) {
                    console.log($scope.location.rooms[i].roomId, id);
                    if ($scope.location.rooms[i].roomId === id) {
                        $scope.room = $scope.location.rooms[i];
                    }
                }
            };

            playerService.getPlayer().then(function (data) {
                $scope.player = data;
            });

            locationService.find($routeParams.id).then(function (data) {
                console.log("This location: ", data);
                $scope.location = data;
                $scope.room = data.rooms[0];
                console.log("This room: ", $scope.room);
            });

        }
    ]);



    app.controller('ShopCtrl', [
        '$routeParams', 'playerService', 'npcService', 'inventoryService', '$sce', '$rootScope',
        function ($routeParams, playerService, npcService, inventoryService, $sce, $rootScope) {

            var $scope = this;

            /**
             *  Buying and Selling:
             * ---------------------
             * 1) Search through the items in the destination "cart" that are
                  processing (inCart).
             * 2) If one of the items contains the same ID as the one being added,
                  increase the quantity of the existing item, based on the
                  quantitySelected value.
             * 3) If the quantitySelected is the same as the quantity of the one
                  being added, delete the item being added.
             */


            var playerReady = false;
            var traderReady = false;
            var undo = {
                player: null,
                trader: null
            };

            function checkReady() {
                if (playerReady && traderReady) {
                    calculateItemValues();
                    $scope.isReady = true;
                }
            }

            function deleteItem(item, cart) {
                for (var i in cart) {
                    if (cart[i].itemId === item.itemId && cart[i].inCart === item.inCart) {
                        delete cart[i];
                        break;
                    }
                }
            }

            function removeFromCart(item, quantity, owner) {

                var clone = angular.copy(item);

                if (clone.quantity === 1) {
                    deleteItem(item, owner.items);
                } else {

                    item.quantity -= quantity;
                    clone.quantity = quantity;

                    if (item.quantity < 1) {
                        deleteItem(item, owner.items);
                    }
                }

                return clone;

            }

            function addToCart(item, owner) {

                var itemFound = false;
                var cart = owner.items;

                if (typeof item.inCart === "boolean") {
                    item.inCart = !item.inCart;
                } else {
                    item.inCart = true;
                }

                /**
                 * Look through the cart to make sure that another item doesn't exist
                 * with the same properties.
                 * If it does, we'll increase that item's quantity, instead of creating
                 * a new item in the cart.
                 */
                for (var k in cart) {

                    if (typeof cart[k].inCart === "undefined") {
                        cart[k].inCart = false;
                    }

                    // Similar item found in cart...
                    if (cart[k].itemId === item.itemId && cart[k].inCart === item.inCart) {
                        cart[k].quantity += item.quantity;
                        itemFound = true;
                        break;
                    }
                }

                if (!itemFound) {
                    cart.push(item);
                    sortCart(owner);
                }

                calculateDifference();

            }

            function calculateDifference() {

                var sellTotal = 0;
                var buyTotal = 0;
                var i;
                var items;

                // Total sell.
                items = $scope.trader.items;
                for (i in items) {
                    if (items[i].inCart === true) {
                        sellTotal += items[i].value * items[i].quantity;
                    }
                }

                // Total buy.
                items = $scope.player.items;
                for (i in items) {
                    if (items[i].inCart === true) {
                        buyTotal += items[i].value * items[i].quantity;
                    }
                }

                $scope.totals.difference = sellTotal - buyTotal;
                $scope.totals.display = calculateDisplayTotal($scope.totals.difference);
                $scope.isDisabled = ($scope.totals.difference < 0 && $scope.isOverage);
            }

            function calculateDisplayTotal(net) {

                if (net === 0) {
                    $scope.isOverage = false;
                    $scope.totals.label = "--";
                    return "0";
                }

                // Player is taking a loss.
                if (net < 0) {
                    if (($scope.player.money * -1) > net) {
                        $scope.isOverage = true;
                        $scope.totals.label = "Loss";
                        return net;
                    }
                }

                // Player is earning money.
                if ($scope.trader.money < net) {
                    $scope.isOverage = true;
                    net = $scope.trader.money;
                    $scope.totals.label = "Profit";
                    return "+" + $scope.trader.money;
                }

                // Make sure the display total is positive.
                $scope.isOverage = false;
                $scope.totals.label = "Profit";
                return "+" + net;

            }

            function calculateItemValues() {

                var playerMultiplier = 0.5;
                var traderMultiplier = 0.6;

                var item;

                for (var p in $scope.player.items) {
                    item = $scope.player.items[p];
                    item.value = Math.round(item.baseValue - (item.baseValue * playerMultiplier));
                }

                for (var t in $scope.trader.items) {
                    item = $scope.trader.items[t];
                    item.value = Math.round(item.baseValue * (1 + traderMultiplier));
                }
            }

            function sortCart(owner) {
                var inCart = [];
                var owned = [];
                for (var k in owner.items) {
                    if (owner.items[k].inCart === true) {
                        inCart.push(owner.items[k]);
                    } else {
                        owned.push(owner.items[k]);
                    }
                }
                inCart = inventoryService.sortAlpha(inCart);
                owned = inventoryService.sortAlpha(owned);
                owner.items = inCart.concat(owned);
            }

            // Various.
            $scope.player = {};
            $scope.trader = {};
            $scope.npcId = $routeParams.id;
            $scope.isReady = false;
            $scope.totals = {
                difference: 0,
                display: "0",
                label: "--"
            };
            $scope.isDisabled = true;

            // Retrieve player and trader inventories.
            playerService.getPlayer().then(function (data) {
                console.log("playerService: ", data);
                undo.player = angular.copy(data.items);
                $scope.player = data;
                playerReady = true;
                checkReady();
            });

            npcService.find($scope.roomPlacementId).then(function (data) {
                console.log("npcService: ", data);
                undo.trader = angular.copy(data.items);
                $scope.trader = data;
                traderReady = true;
                checkReady();
            });

            // Actions.
            $scope.buy = {
                label: $sce.trustAsHtml("Take"),
                onPreview: function (item) {
                    this.label = (item.inCart === true) ?
                        $sce.trustAsHtml('<span class="fa fa-arrow-up"></span>Take Back') :
                        $sce.trustAsHtml('<span class="fa fa-arrow-up"></span>Take');
                },
                onAccept: function (item, quantityRequested) {
                    var clone = removeFromCart(item, quantityRequested, $scope.trader);
                    addToCart(clone, $scope.player);
                },
                onCancel: function () {}
            };

            $scope.sell = {
                label: $sce.trustAsHtml("Offer"),
                onPreview: function (item) {
                    this.label = (item.inCart === true) ?
                        $sce.trustAsHtml('<span class="fa fa-arrow-down"></span>Give Back') :
                        $sce.trustAsHtml('<span class="fa fa-arrow-down"></span>Offer');
                },
                onAccept: function (item, quantityRequested) {
                    var clone = removeFromCart(item, quantityRequested, $scope.player);
                    addToCart(clone, $scope.trader);
                },
                onCancel: function () {}
            };

            $scope.back = function () {
                $scope.player.items = undo.player;
                $scope.trader.items = undo.trader;
                $rootScope.back();
            };

        }
    ]);



    app.controller('InventoryCtrl',
        function () {

            var scope = this;

            function updateBundleValue(item) {
                scope.bundleValue = scope.quantityRequested * item.value;
            }

            scope.bundleValue = 0;

            scope.increaseQuantity = function (item) {
                scope.quantityRequested++;
                if (scope.quantityRequested > item.quantity) {
                    scope.quantityRequested = item.quantity;
                }
                updateBundleValue(item);
            };

            scope.decreaseQuantity = function (item) {
                scope.quantityRequested--;
                if (scope.quantityRequested < 1) {
                    scope.quantityRequested = 1;
                }
                updateBundleValue(item);
            };

            scope.preview = function (item) {
                if (scope.action && scope.action.onPreview) {
                    scope.action.onPreview.call(scope.action, item);
                }
                scope.showcase = item;
            };

            scope.requestQuantity = function (item) {
                if (item.quantity === 1) {
                    scope.quantityRequested = 1;
                    scope.accept(item);
                } else {
                    scope.showQuantitySelect = true;
                    scope.quantityRequested = item.quantity;
                    updateBundleValue(item);
                }
            };

            scope.accept = function (item) {
                scope.action.onAccept(item, scope.quantityRequested);
                scope.cancel();
            };

            scope.cancel = function () {
                delete scope.quantityRequested;
                delete scope.showcase;
                scope.showQuantitySelect = false;
            };

        });



    app.controller('VesselCtrl', [
        'playerService', 'vesselService', '$sce',
        function (playerService, vesselService, $sce) {

            var scope = this;

            playerService.getPlayer().then(function (data) {
                scope.player = data;
            });

            vesselService.find(scope.roomPlacementId).then(function (data) {
                console.log("Vessel: ", data);
                scope.vessel = data;
            });

            scope.take = {
                label: $sce.trustAsHtml('<span class="fa fa-arrow-up"></span>Take'),
                onPreview: function () {},
                onAccept: function () {},
                onCancel: function () {}
            };

            scope.store = {
                label: $sce.trustAsHtml('<span class="fa fa-arrow-down"></span>Store'),
                onPreview: function () {},
                onAccept: function () {},
                onCancel: function () {}
            };
        }
    ]);



    app.controller('PickpocketCtrl', [
        '$scope', '$routeParams', 'playerService', 'npcService', '$sce', '$rootScope',
        function ($scope, $routeParams, playerService, npcService, $sce, $rootScope) {

            var playerReady = false;
            var npcReady = false;
            var undo = {
                player: null,
                npc: null
            };

            function checkReady() {
                $scope.isReady = (playerReady && npcReady);
            }

            // Various.
            $scope.npcId = $routeParams.id;
            $scope.isReady = false;

            playerService.getPlayer().then(function (data) {
                $scope.player = data;
                playerService.getInventory().then(function (data) {
                    undo.player = angular.copy(data.items);
                    $scope.player.items = data.items;
                    playerReady = true;
                    checkReady();
                });
            });

            npcService.find($scope.npcId).then(function (data) {
                $scope.npc = data;
                npcService.getInventory($scope.npcId).then(function (data) {
                    undo.npc = angular.copy(data.items);
                    $scope.npc.items = data.items;
                    npcReady = true;
                    checkReady();
                });
            });

            $scope.back = function () {
                $scope.player.items = undo.player;
                $scope.npc.items = undo.npc;
                $rootScope.back();
            };

        }
    ]);



}(window.angular, window.console));
