(function () {
    'use strict';


    // Register the custom AngularJS app.
    var app = angular.module("ngBBI", ['ui.sortable']);


    // HTML templates.
    app.run(['$templateCache', 'dataService',
        function ($templateCache, dataService) {
            for (var i in dataService.templates) {
                $templateCache.put(i, dataService.templates[i]);
            }
        }]);


    // Add our BBI app to the AngularJS app so we can use its data.
    app.factory('bbApp',
        function () {
            return window.bbiGetInstance().apps()['CommunityBibleStudy'];
        });


    // Add the BBNC Custom Part API as a service.
    app.factory('customPart',
        function () {
            return window.BLACKBAUD.api.customPartEditor;
        });


    // Editor data service.
    app.service('dataService', ['bbApp', 'customPart',
        function (bbApp, customPart) {
            this.templates = {
                'editor.html':
                    ['<div class="container" ng-controller="DesignationCategories">',
                        '<div ng-if="isSaving" class="overlay-disabler"><span class="loader">Please wait...</span></div>',
                        '<div class="page-header">',
                            '<h2>Designation Categories</h2>',
                        '</div>',
                        '<div class="row">',
                            '<div class="col-sm-4">',
                                '<h4>Donation Form</h4>',
                                '<p>The form selected here will be used to validate the Fund Names below. The donation form you select must have configured all funds/designations you wish to categorize.</p>',
                                '<div class="form-group">',
                                    '<span class="input-group">',
                                        '<input type="text" ng-model="donationFormUrl" class="form-control" value="{{donationFormUrl}}" placeholder="https://">',
                                        '<span class="input-group-btn">',
                                            '<button type="button" class="btn btn-default" ng-click="selectPage()">Select...</button>',
                                        '</span>',
                                    '</span>',
                                '</div>',
                                '<div ng-if="donationFormDesignations">',
                                    '<h4>Possible Fund Names:</h4>',
                                    '<ul class="list-group">',
                                        '<li ng-repeat="fund in donationFormDesignations" class="list-group-item" ng-class="{\'dc-used\': found(fund.label)}">{{fund.label}}</li>',
                                    '</ul>',
                                '</div>',
                            '</div>',
                            '<div class="col-sm-8" ng-if="donationFormUrl">',
                                '<h4>Build the Category Dropdown</h4>',
                                '<p>A category is not directly tied to a Fund in RE. The Fund Name for each Designation is the case-sensitive label given to the designation on the donation page, above. The Display As label will overwrite the Fund Name when displayed to the end user.</p>',
                                '<div ng-include="\'designations.html\'"></div>',
                            '</div>',
                        '</div>',
                    '</div>'].join(''),

                'designations.html':
                    [
                    '<div id="sortable-container" as-sortable="sortableOptions" ng-model="categories">',
                        '<div class="panel panel-default panel-has-table" ng-repeat="category in categories" as-sortable-item>',
                            '<div class="panel-heading" as-sortable-item-handle>',
                                '<h4 class="panel-title">',
                                    '<input type="text" ng-model="category.label" class="form-control" value="{{category.label}}" placeholder="Category name">',
                                '</h4>',
                            '</div>',
                            '<div class="panel-body">',
                                '<table class="table">',
                                    '<thead ng-if="hasDesignations(category)">',
                                        '<tr>',
                                            '<th>',
                                                'Fund Name:',
                                            '</th>',
                                            '<th style="width: 2%;"></th>',
                                            '<th>',
                                                'Display As:',
                                            '</th>',
                                            '<th></th>',
                                        '</tr>',
                                    '</thead>',
                                    '<tbody as-sortable="sortableSecondaryOptions" ng-model="category.designations">',
                                        '<tr ng-if="!hasDesignations(category)"><td colspan="4"><div class="droppable-target">Drop new items here.</div></td></tr>',
                                        '<tr ng-repeat="designation in category.designations" as-sortable-item>',
                                            '<td>',
                                                '<input class="form-control" ng-model="designation.value" type="text" value="{{designation.value}}">',
                                            '</td>',
                                            '<td as-sortable-item-handle><span class="fa fa-sort fa-only"></span></td>',
                                            '<td>',
                                                '<input class="form-control" ng-model="designation.label" type="text" placeholder="(optional)" value="{{designation.label}}">',
                                            '</td>',
                                            '<td>',
                                                '<button type="button" class="btn btn-default btn-block" title="Remove designation from this category." ng-click="removeDesignation($parent.$index, $index)"><i class="fa fa-trash fa-only"></i></button>',
                                            '</td>',
                                        '</tr>',
                                    '</tbody>',
                                '</table>',
                            '</div>',
                            '<div class="panel-footer">',
                                '<button type="button" class="btn btn-default" ng-click="addDesignation($index)"><i class="fa fa-plus"></i>Add Designation</button> ',
                                '<button type="button" class="btn btn-default pull-right" ng-click="removeCategory($index)"><i class="fa fa-trash"></i>Remove Category</button>',
                            '</div>',
                        '</div>',
                        '</div>',
                        '<div class="panel panel-default">',
                            '<div class="panel-body">',
                                '<button type="button" class="btn btn-primary btn-lg" ng-click="addCategory()">New Category</button>',
                            '</div>',
                        '</div>'

                    ].join('')

            };
            this.defaults = {
                category: function () {
                    return {
                        "label": "",
                        "designations": []
                    };
                },
                designation: function () {
                    return {
                        "value": "",
                        "label": ""
                    };
                }
            };
            this.saved = (customPart.settings.categories) ? customPart.settings.categories : [];
            this.donationFormUrl = (customPart.settings.donationFormUrl) ? customPart.settings.donationFormUrl : '';
            this.existingDesignations = bbApp.actions.Editor.existingDesignations();
        }]);


    // Editor directive.
    app.directive('ngBbncEditor', ['$templateCache',
        function ($templateCache) {
            return {
                restrict: 'ACME',
                template: $templateCache.get('editor.html')
            };
        }]);


    // Editor controller.
    app.controller('DesignationCategories', ['$scope', 'dataService', 'customPart', 'bbApp',
        function ($scope, dataService, customPart, bbApp) {

            // ng-sortable options.
            $scope.sortableOptions = {
                containment: '#sortable-container',
                containerPositioning: 'relative',
                accept: function (sourceItemHandleScope, destSortableScope, destItemScope) {
                    return (sourceItemHandleScope.itemScope.sortableScope.element[0].nodeName === "DIV");
                }
            };
            $scope.sortableSecondaryOptions = {
                containerPositioning: 'relative',
                accept: function (sourceItemHandleScope, destSortableScope, destItemScope) {
                    var nodeName = sourceItemHandleScope.itemScope.sortableScope.element[0].nodeName;
                    return (nodeName === "TBODY");
                }
            };

            $scope.isSaving = false;
            $scope.donationFormUrl = dataService.donationFormUrl;
            $scope.selectPage = function () {
                customPart.links.launchLinkPicker({
                    callback: function (selectedLink) {
                        var url = selectedLink.url;
                        $scope.$apply(function () {
                            $scope.isSaving = true;
                            if (url.indexOf("https") == -1) {
                                url = url.replace('http', 'https');
                            }
                            bbApp.actions.Editor.updateDonationFormDesignations(url, function (isDonationForm) {
                                if (!isDonationForm) {
                                    alert("The page you selected did not include a donation form with designations/funds.");
                                    url = "";
                                }
                                $scope.$apply(function () {
                                    $scope.donationFormUrl = url;
                                    $scope.isSaving = false;
                                });
                            });
                        });
                    }
                });
            };

            $scope.hasDesignations = function (category) {
                return (category.designations && category.designations.length > 0);
            };

            $scope.categories = dataService.saved;

            $scope.addCategory = function () {
                $scope.categories.push(dataService.defaults.category());
            };

            $scope.removeCategory = function (categoryIndex) {
                $scope.categories.splice(categoryIndex, 1);
            };

            $scope.addDesignation = function (categoryIndex) {
                $scope.categories[categoryIndex]['designations'].push(dataService.defaults.designation());
            };

            $scope.removeDesignation = function (categoryIndex, designationIndex) {
                $scope.categories[categoryIndex]['designations'].splice(designationIndex, 1);
            };

            $scope.getOptionLabel = function (obj) {
                if (obj.label && obj.label != "") {
                    return obj.label;
                }
                return obj['value'];
            };

            $scope.donationFormDesignations = dataService.existingDesignations;
            $scope.found = function (label) {
                var categories = $scope.categories;
                var c, d;
                var len;
                for (c in categories) {
                    if (!categories[c].designations) {
                        return false;
                    }
                    len = categories[c]['designations'].length;
                    for (d = 0; d < len; d++) {
                        if (label === categories[c]['designations'][d]['value']) {
                            return true;
                        }
                    }
                }
                return false;
            };

            customPart.onSave = function () {

                $scope.$apply(function () {
                    $scope.isSaving = true;
                });

                var existing = $scope.donationFormDesignations;
                var found = false;
                var culprit = null;
                var categories = $scope.categories;
                var c, d, i;
                var len1, len2;

                outerLoop:
                for (c in categories) {
                    if (!categories[c].designations) {
                        found = false;
                        break;
                    }
                    len1 = categories[c]['designations'].length;
                    for (d = 0; d < len1; d++) {
                        found = false;
                        len2 = existing.length;
                        for (i = 0; i < len2; i++) {
                            if (existing[i]['label'] === categories[c]['designations'][d]['value']) {
                                found = true;
                                break outerLoop;
                            }
                        }
                        if (!found) {
                            culprit = categories[c]['designations'][d];
                            break;
                        }
                    }
                    if (!found) {
                        break;
                    }
                }

                if (!found) {
                    if (culprit != null && typeof culprit['value'] === "string") {
                        alert("The Fund Name \"" + culprit['value'] + "\" does not match a fund on the donation form.");
                    } else {
                        alert("You left some required fields blank.");
                    }
                    $scope.$apply(function () {
                        $scope.isSaving = false;
                    });
                    return false;
                }

                // Make sure there is a label and value for each option.
                for (c in categories) {
                    for (d in categories[c].designations) {
                        if (categories[c].designations[d]['label'] === "") {
                            categories[c].designations[d]['label'] = $scope.getOptionLabel(categories[c].designations[d]);
                        }
                    }
                }

                customPart.settings = {
                    categories: $scope.categories,
                    donationFormUrl: $scope.donationFormUrl
                };

                return true;

            };

        }]);


}());
