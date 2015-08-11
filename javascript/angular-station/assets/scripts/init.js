(function (angular, console) { "use strict";



    console.log("Hello, World!");



    var app = angular.module('Station', ['ngTouch', 'ngRoute']);



    app.run(['$rootScope', '$location',
        function ($rootScope, $location) {
            var history = [];
            $rootScope.$on('$routeChangeSuccess', function() {
                history.push($location.$$path);
            });
            $rootScope.back = function () {
                var prevUrl = history.length > 1 ? history.splice(-2)[0] : "/";
                $location.path(prevUrl);
            };
        }]);



    app.config(['$routeProvider',
        function ($routeProvider) {
            $routeProvider
            .when('/map', {
                templateUrl: 'app/views/map.html',
                controller: "MapCtrl"
            })
            .when('/region/:id', {
                templateUrl: 'app/views/region.html',
                controller: 'RegionCtrl'
            })
            .when('/location/:id', {
                templateUrl: 'app/views/location.html',
                controller: 'LocationCtrl'
            })
            .when('/pack', {
                templateUrl: 'app/views/pack.html',
                controller: ['$scope', 'playerService', function ($scope, playerService) {
                    playerService.getInventory().then(function (data) {
                        $scope.player = data;
                    });
                }]
            })
            .when('/pickpocket/:id', {
                templateUrl: 'app/views/pickpocket.html',
                controller: "PickpocketCtrl"
            })
            .otherwise({
                redirectTo: '/map'
            });
        }]);



}(window.angular, window.console));
