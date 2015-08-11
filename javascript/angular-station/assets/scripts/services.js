(function (angular, console) { "use strict";



    console.log("Loading services...Done.");



    var app = angular.module('Station');



    app.factory('Lawnchair',
        function () {
            return window.Lawnchair;
        });



    app.factory('Resource', ['$http', '$q', 'Lawnchair',
        function ($http, $q, Lawnchair) {

            var _storage = new Lawnchair({ name: "Station" });

            _storage.nuke();

            function Resource(options) {

                var _cache = {};

                function search(id) {

                    var deferred = $q.defer();

                    _storage.get(options.key, function (saved) {

                        saved = saved || {};
                        saved.records = saved.records || {};

                        if (typeof saved.records[id] !== "undefined") {
                            console.log("Storage found! Let's use that...");
                            deferred.resolve(saved.records[id]);

                        } else {

                            find1(id).then(function (data) {
                                saved.records[id] = data[options.key];
                                _storage.save({
                                    key: options.key,
                                    records: saved.records
                                });
                                _cache = saved.records;
                                deferred.resolve(saved.records[id]);
                            });

                        }
                    });
                    return deferred.promise;
                }

                function find1(id) {
                    var deferred = $q.defer();
                    $http
                        .get(options.endpoint.one + id)
                        .success(function (data) {
                            deferred.resolve(data);
                        })
                        .error(function (data) {
                            deferred.resolve(data);
                        });
                    return deferred.promise;
                }

                function findAll() {
                    var deferred = $q.defer();
                    $http
                        .get(options.endpoint.all)
                        .success(function (data) {
                            deferred.resolve(data);
                        })
                        .error(function (data) {
                            deferred.resolve(data);
                        });
                    return deferred.promise;
                }

                function searchAll() {
                    var deferred = $q.defer();

                    _storage.get(options.key, function (saved) {
                        saved = saved || {};
                        if (saved.allCached === true && saved.records) {
                            console.log("All items have been searched already!");
                            deferred.resolve(saved.records);

                        } else {
                            findAll().then(function (data) {
                                _storage.save({
                                    key: options.key,
                                    records: data[options.key],
                                    allCached: true
                                });
                                _cache = data[options.key];
                                deferred.resolve(_cache);
                            });
                        }
                    });
                    return deferred.promise;
                }

                return {
                    find: search,
                    findAll: searchAll,
                    cache: function () {
                        return _cache;
                    }
                };
            }

            return {
                getInstance: function (options) {
                    return new Resource(options);
                },
                storage: function () {
                    return _storage;
                }
            };

        }]);



    app.factory('inventoryService', ['Resource',
        function (Resource) {

            var resource = new Resource.getInstance({
                key: 'Items',
                endpoint: {
                    all: 'api/items'
                }
            }), service = {};

            service.search = function (itemId) {
                var items = resource.cache();
                for (var i in items) {
                    if (items[i].itemId === itemId) {
                        return items[i];
                    }
                }
                return false;
            };

            service.sortAlpha = function (arr) {
                arr.sort(function (a, b) {
                    var label1 = a.name.toUpperCase();
                    var label2 = b.name.toUpperCase();
                    return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
                });
                return arr;
            };

            service.merge = function (items) {
                var parentItem;
                var item;
                var temp = [];
                for (var k in items) {
                    item = items[k];
                    parentItem = service.search(item.itemId);
                    if (parentItem) {
                        temp[k] = item;
                        for (var prop in parentItem) {
                            temp[k][prop] = parentItem[prop];
                        }
                    }
                }
                return temp;
            };

            service.findAll = resource.findAll;

            return service;

        }]);



    app.factory('playerService', ['inventoryService', 'Resource', '$q',
        function (inventoryService, Resource, $q) {

            var resource = Resource.getInstance({
                key: 'Player',
                endpoint: {
                    all: 'models/player.json'
                }
            }), service = {};

            service.getPlayer = function () {
                var deferred = $q.defer();
                inventoryService.findAll().then(function () {
                    resource.findAll().then(function (data) {
                        data.items = inventoryService.merge(data.items);
                        deferred.resolve(data);
                    });
                });
                return deferred.promise;
            };

            return service;

        }]);



    app.factory('npcService', ['inventoryService', 'Resource',
        function (inventoryService, Resource) {

            var resource = Resource.getInstance({
                key: 'NPC',
                endpoint: {
                    one: 'api/npc/'
                }
            }), service = {};

            service.find = resource.find;

            return service;

        }]);



    app.factory('vesselService', ['Resource',
        function (Resource) {
            return Resource.getInstance({
                key: 'Vessel',
                endpoint: {
                    one: 'api/vessel/'
                }
            });
        }]);



    app.factory('regionService', ['Resource',
        function (Resource) {
            return Resource.getInstance({
                key: 'Region',
                endpoint: {
                    all: 'api/regions',
                    one: 'api/region/'
                }
            });
        }]);



    app.factory('locationService', ['Resource',
        function (Resource) {
            return Resource.getInstance({
                key: 'Location',
                endpoint: {
                    one: 'api/location/'
                }
            });
        }]);



}(window.angular, window.console));
