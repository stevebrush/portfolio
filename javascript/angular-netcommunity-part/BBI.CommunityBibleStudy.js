/*! (c) Blackbaud, Inc. */
(function (bbi) {
    'use strict';



    var app = bbi.register({
        alias: "CommunityBibleStudy",
        author: "Steve Brush"
    });



    app.action("Editor", function (app, bbi, $) {

        var settings, defaults = {
            donationFormUrl: '//50099.thankyou4caring.org/admin/designation-categories'
        };

        var methods = {
            angularControllerScope: function (controller) {
                return angular.element('[ng-controller="' + controller + '"]').scope();
            },
            fetchDonationFormDesignations: function (callback) {
                $.get(settings.donationFormUrl, function (html) {
                    settings.donationFormDesignations = [];
                    $(html).find('select[id*="_ddlDesignations"] option').each(function () {
                        var $option = $(this);
                        settings.donationFormDesignations.push({
                            value: $option.val(),
                            label: $option.text()
                        });
                    });
                    if (settings.donationFormDesignations.length > 0) {
                        callback.call({}, true);
                    } else {
                        callback.call({}, false);
                    }
                });
            },
            getDonationFormDesignations: function () {
                return settings.donationFormDesignations;
            },
            updateDonationFormDesignations: function (url, callback) {
                settings.donationFormUrl = url;
                methods.fetchDonationFormDesignations(callback);
            }
        };

        this.$scope = methods.angularControllerScope;
        this.existingDesignations = methods.getDonationFormDesignations;
        this.donationFormUrl = methods.getDonationFormUrl;
        this.updateDonationFormDesignations = methods.updateDonationFormDesignations;
        this.init = function (options, element) {
            settings = $.extend(true, {}, defaults, options);
            bbi.helper.loadScript(settings.angularAppSrc, function () {
                methods.fetchDonationFormDesignations(function () {
                    angular.bootstrap(element, ['ngBBI']);
                });
            });
        };
    });



    app.action("Display", function (app, bbi, $) {
        var _settings, _defaults = {
            defaultFundId: 27
        };
        var blueprint = [
            '<div class="dc-selects">',
                '<select class="BBFormSelectList DonationSelectList dc-select-parents">',
                    '<option value="-1">Please select</option>',
                    '{{#each categories}}',
                        '<option data-target-category="{{@index}}" value="{{@index}}">{{label}}</option>',
                    '{{/each}}',
                '</select>',
                '<div class="dc-select-children">',
                    '{{#each categories}}',
                        '<select class="BBFormSelectList DonationSelectList dc-select-child" data-category="{{@index}}">',
                            '{{#each designations}}',
                                '<option value="{{value}}" data-target-designation="{{value}}">{{label}}</option>',
                            '{{/each}}',
                        '</select>',
                    '{{/each}}',
                '</div>',
            '</div>'
        ];
        this.init = function (options, element) {
            if (!bbi.isPageEditor()) {
                bbi.require(['handlebars-helpers'], function () {
                    $(function () {

                        _settings = $.extend(true, {}, _defaults, options);
                        var template = Handlebars.compile(blueprint.join(""));

                        bbi.attach(function () {

                            var $container = $('.DonationFormTable');
                            var $parent_select, $children_selects, $designations;
                            var current_designation;

                            var buildSelects = function () {
                                // Generate HTML.
                                $designations = $('select[id*="_ddlDesignations"]').prepend('<option value="0">Please select</option>').hide();//.css({'opacity':'0.4'})
                                $designations.before(template({ categories: options.settings.categories }));
                            };

                            var getCurrentDesignation = function () {
                                if (!$('input[name*="givingLevels"]:checked').val()) {
                                    $designations.val(0);
                                }
                                current_designation = $designations.find('option:selected').text();
                            };

                            var setElements = function () {
                                $parent_select = $container.find('select.dc-select-parents');
                                $children_selects = $container.find('select.dc-select-child');
                            };

                            var postError = function (message) {
                    			var cont = $('.BBFormValidatorSummary:eq(0)').show();
                    			if (cont.find('ul').length) {
                    				cont.find('ul').append('<li>' + message + '</li>');
                    			} else {
                    				cont.append('Error(s) encountered:<br><ul><li>' + message + '</li></ul>');
                    			}
                    			window.scrollTo(0,0);
                    		};

                            var events = function () {

                                // Show the child select when the parent is selected.
                                $parent_select.on('change', function () {
                                    var catId = $(this).find('option:selected').attr('data-target-category');
                                    if ($(this).val() == -1) {
                                        $designations.val(0);
                                    }
                                    $children_selects.each(function () {
                                        var $select = $(this);
                                        if ($select.attr('data-category') == catId) {
                                            $select.addClass('dc-on').trigger("change");
                                        } else {
                                            $select.removeClass('dc-on');
                                        }
                                    });
                                });

                                // Select the actual designation on the form when
                                // the child selects are changed.
                                $children_selects.on("change", function () {
                                    var designation = $(this).val();
                                    $designations.find('option').each(function () {
                                        var $designation_option = $(this);
                                        if ($designation_option.text() == designation) {
                                            $designation_option.prop("selected", 1);
                                        } else {
                                            $designation_option.prop("selected", 0);
                                        }
                                    });
                                });

                                // Error handling if user doesn't select a designation
                    			var $button = $('input.BBFormSubmitButton[id*="_btnAddToCart"], input.BBFormSubmitButton[id*="_btnNext"]');
                				var fn = $button.prop('onclick');
                				$button.unbind('click').prop('onclick', null).on('click', function (e) {
                					if ($designations.val() === "0") {
                						postError("Please select a designation.");
                						return false;
                					} else {
                						fn();
                					}
                					e.preventDefault();
                				});
                            };

                            var autoSelect = function () {
                                /**
                                 * Select the dummy select based on the current
                                 * value of the form's designation dropdown.
                                 */
                                $children_selects.find('option').each(function () {
                                    var $option = $(this);
                                    if ($option.val() === current_designation) {
                                        $parent_select.val($option.parent().attr('data-category')).trigger("change");
                                        $option.parent().val(current_designation);
                                        return false;
                                    }
                                });
                            };

                            var removeLoader = function () {
                                $(element).find('.bbi-loader').remove();
                            };

                            buildSelects();
                            setElements();
                            getCurrentDesignation();
                            events();
                            autoSelect();
                            removeLoader();

                        });
                    });
                });
            } else {
                $(element).html('');
            }
        };
    });



    app.build();



}(bbiGetInstance()));
