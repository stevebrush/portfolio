try {

    (function (_win) {
		"use strict";
		
		
		
		// Make sure jQuery is installed
		if (typeof _win.jQuery !== "function") {
			throw new Error("The GD namespace requires jQuery 1.7.2 or greater to operate.");
		}
		
		
		
		// Private
		var $ = _win.jQuery;
		var _ajax = {
			fbLogin: "../ajax/facebook-login.ajax.php",
			fbSignup: "../ajax/facebook-signup.ajax.php"
		};
		var _blueprints = {
			forms: {
				search: [
					'{{#if results}}',
						'<div class="list-group">',
							'{{#each results}}',
								'<a class="list-group-item" href="{{this.url}}">',
									'<div class="media">',
										'<div class="thumbnail">',
											'<img src="{{this.thumbnail}}" alt="User avatar">',
										'</div>',
										'<div class="media-body">',
											'<h4 class="media-heading">',
												'{{this.fullName}}',
											'</h4>',
										'</div>',
									'</div>',
								'</a>',
							'{{/each}}',
						'</div>',
					'{{else}}',
						'<div class="alert alert-info">',
							'No users found.',
						'</div>',
					'{{/if}}'
				]
			}
		};
		var _callbacks = {
			search: function (response, element) {
			
				var form = $(element);
				var template = Handlebars.compile(_blueprints.forms.search.join(""));
				var results = $(response.package.target).html(template({
					results: response.package.list
				}));
				
				form.find(".btn-submit").css("display","none");
				form.find(".btn-cancel").css("display","block").on("click", function (e) {
					e.preventDefault();
					$(_document).trigger("mouseup.gd-search");
				});
				
				$(_document).on("mouseup.gd-search", function () {
					results.html("");
					$(_document).off("mouseup.gd-search");
					form.find(".btn-cancel").css("display","none");
					form.find(".btn-submit").css("display","block");
				});
			}
		};
		var _container;
		var _document = _win.document;
		var _gd;
		var _scope = {
			forms: []
		};
		
		/*
		var Modules = (function () {
			var methods = {
				dataButtons: function () {
					_container.find(".btn-data").each(function () {
						var btn = $(this);
						var url = btn.attr("href");
						if (typeof url !== "string") {
							url = btn.attr('data-href');
						}
						var defaults = {
							url: btn.attr('href'),
							data: btn.data()
						};
						var options = $.extend(true, {}, _defaultFormOptions, defaults);
						btn.on('click', function (e) {
							e.preventDefault();
							$.ajax(options);
							btn.button('loading');
						});
					});
				}
			};
			return {
				activate: function () {
					$(function () {	
						methods.dataButtons();
						methods.radioTabs();
					});
				}
			};
		}());
		*/
		
		
		function Facebook() {
			
			var methods = {
				fetchUserInfo: function (query, callback) {
					FB.api('/me?' + query, callback);
				},
				login: function (button, response, callback) {
				
					var ar;
					var data = {};
				
					if (response && response.authResponse) {
				
						ar = response.authResponse;
						
						data.redirect = Method.getUrlVars()['redirect'] || button.attr('data-url-redirect');
						data.facebookAccessToken = ar.accessToken;
						data.facebookUserId = ar.userID;
						
						methods.fetchUserInfo("email", function (r) {
							data.facebookEmailAddress = (typeof r.email === "string") ? r.email.toLowerCase() : "";
							$.post(_ajax.fbLogin, data, callback, "json");
						});
					}
				},
				signup: function (button, response, callback, options) {
				
					var ar;
					var data = $.extend(true, {}, {}, options);
					if (response && response.authResponse) {
					
						ar = response.authResponse;
						data.facebookAccessToken = ar.accessToken,
						data.facebookUserId = ar.userID;
						data.leaderId = Method.getUrlVars()['userId'] || 0;
						
						methods.fetchUserInfo("first_name,last_name,email,gender,birthday", function (r) {
						
							var birthday;
						
							data.facebookFirstName = r.first_name;
							data.facebookLastName = r.last_name;
							data.facebookGender = r.gender;
							data.facebookEmailAddress = (typeof r.email === "string") ? r.email.toLowerCase() : "";
								
							if (typeof r.birthday === "string") {
								birthday = r.birthday.split('/');
								data.facebookBirthday = birthday[2] + "-" + birthday[0] + "-" + birthday[1];
							} else {
								data.facebookBirthday = "";
							}
							
							data.facebookThumbnail = "https://graph.facebook.com/" + data.facebookUserId + "/picture?type=large";
							
							$.post(_ajax.fbSignup, data, callback, "json");
						});
					}
				},
				processResponse: function (button, response) {
					if (response) {
						switch (response.status) {
							case "error":
							if (response.message) {
								button.closest('form').find('.gd-form-alert').addClass("alert-danger").html(response.message).addClass("in");
							}
							break;
							case "success":
							if (response.redirect) {
								_win.location.href = response.redirect;
								return false;
							}
							break;
						}
					}
				}
			};
		
			$(function () {
		
				_container.find('.btn-facebook-login, .btn-facebook-link-account').on('click', function (e) {
				
					var btn = $(this);
					var options = {
						scope: "email,user_birthday"
					};
					
					e.preventDefault();
					
					FB.login(function (response) {
						if (response.status === "connected") {
							methods.login(btn, response, function (r) {
								methods.processResponse(btn, r);
							});
						} else {
							btn.button("reset");
						}
					}, options);
					
					return false;
					
				});
				
				_container.find('.btn-facebook-signup').on('click', function (e) {
					var btn = $(this);
					var options = {
						scope: "email,user_birthday"
					};
					e.preventDefault();
					FB.login(function (response) {
						if (response.status === "connected") {
							// First, check if user already exists:
							methods.login(btn, response, function (result) {
								if (result.status === "error") {
									// If no user exists, create a new one:
									methods.signup(btn, response, function (r) {
										methods.processResponse(btn, r);
									}, {});
								} else {
									// User is logged in to Facebook, and was found in GiftDibs, but something went wrong
									methods.processResponse(btn, result);
								}
							});
							
						} else {
							btn.button('reset');
						}
					}, options);
				});
				
				_container.find('.btn-facebook-update-profile').on('click', function (e) {
				
					var btn = $(this);
					var options = {
						scope: "email,user_birthday"
					};
					
					e.preventDefault();
					
					FB.login(function (response) {
						if (response.status === "connected") {
							methods.signup(btn, response, function (r) {
								methods.processResponse(btn, r);
							}, {
								userId: btn.attr("data-gd-user-id") || null,
								redirect: btn.attr("data-gd-redirect") || null
							});
						} else {
							btn.button('reset');
						}
					}, options);
				});
				
				_container.find('.btn-facebook-invite').on('click', function (e) {
					e.preventDefault();
					FB.ui({
						method: "send",
						link: $(this).attr('href')
					});
				});
				
				_container.find('.btn-facebook-logout').on('click', function (e) {
					e.preventDefault();
					FB.logout(function () {
						_win.location.reload();
					});
				});
			
			});
			
		};
		
		
		function Form(options) {
		
			var settings;
			var defaults = {
				type: "post",
				dataType: "json",
				element: null
			};
		
			var $alert;
			var $buttons;
			var $form;
			var that = this;
		
			var methods = {
				alert: function (message, type) {
					switch (type) {
						case "error":
						$alert.addClass("alert-danger").html(message).addClass("in");
						break;
					}
				},
				reset: function () {
					$buttons.prop("disabled", 0).button("reset");
					$alert.removeClass("alert-danger").removeClass("alert-success").removeClass("in").html("");
				},
				processResponse: function (response, statusText, xhr, form) {
				
					var r = response;
					var callback = $form.attr("data-gd-callback");
					
					console.log("Form Response: ", r);
					
					if (r) {
						switch (r.status) {
						
							case "success":
								if (r.package && (typeof callback === "string" && typeof _callbacks[callback] === "function")) {
									// The form's callback should handle the response's package
									_callbacks[callback].call(_gd, r, $form);
								}
								else if (r.redirect) {
									_win.location.href = r.redirect;
									return false;
								}
								else if (r.message) {
									methods.reset();
									methods.alert(r.message, r.status);
								}
							break;
							
							case "error":
								methods.reset();
								if (r.message) {
									methods.alert(r.message, r.status);
								}
							break;
							
						}
					} else {
						methods.reset();
					}
					
				},
				build: function () {
					
					// Radio tabs
					$form.find('[data-gd-radio-tabs]').each(function () {
						var that = $(this);
						var radios = that.find('input[type=radio]');
						radios.on('click', function () {
							that.find('.gd-tab-content').removeClass('gd-active');
							$($(this).attr('data-target')).addClass('gd-active');
						});
					});
					
					// Collapse and clear
					$form.find('[data-gd-clear-target]').each(function () {
						var checkbox = $(this);
						var target = $form.find(checkbox.attr("data-gd-clear-target"));
						checkbox.on('click', function () {
							if (target.hasClass('in') === true || target.hasClass('collapsing') === true) {
								// Empty the values of all inputs in the collapsed container
								target.find('input, textarea').val("");
							}
						});
						
					});
					
					// Toggle checkboxes
					$form.find('[data-gd-check-all]').each(function () {
					
						var checkbox = $(this);
						var target = $form.find(checkbox.attr("data-target"));
						var checkboxes = target.find('input[type="checkbox"]');
						
						checkbox.on('click', function () {
							if (target.hasClass('collapse') === false) {
								if (checkbox.is(':checked')) {
									checkboxes.prop("checked", 1);
								} else {
									checkboxes.prop("checked", 0);
								}
							}
						});
						
						(function () {
							var allChecked = true;
							checkboxes.each(function () {
								if ($(this).is(':checked') === false) {
									allChecked = false;
									return false;
								}
							});
							checkbox.prop("checked", allChecked);
						}());
						
					});
					
					// Prevent default form submission
					$form.on("submit", function (e) {
						e.preventDefault();
						return false;
					});
					
					// Submit the form on click
					$buttons.on("click", function () {
						$form.ajaxSubmit(settings);
					});
				}
			};
			
			defaults.success = methods.processResponse;
			defaults.error = methods.processResponse;
			
			var __construct = (function () {
				settings = $.extend(true, {}, defaults, options);
				$form = $(settings.element);
				$alert = $form.find(".gd-form-alert");
				$buttons = $form.find("button.btn-submit");
				methods.build();
			}());
			
			return {
				form: that,
				settings: settings
			};
		};
		
		
		var Method = {
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
            initForms: function () {
				var forms = _container.find(".gd-form");
				for (var i = 0, len = forms.length; i < len; i++) {
					_scope.forms.push(new Form({
						element: forms[i]
					}));
				}
			},
			moveModals: function () {
				$(".modal").appendTo("body");
			},
			safeConsole: function () {
				_win.log = function () {
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
				(function (d) {
					// Add properties to console object, if they exist.
					// Otherwise, assign empty function.
					var f;
					var fns = ["assert", "count", "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed", "groupEnd", "info", "log", "timeStamp", "profile", "profileEnd", "time", "timeEnd", "trace", "warn"];
					var a = function () {};
					while (f = fns.pop()) {
						d[f] = d[f] || a;
					}
				})((function () {
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
			ui: function () {
				
				// Boostrap buttons
				_container.find("[data-loading-text]").on("click", function () {
					$(this).prop("disabled", 1).button("loading");
				});
				
				// Cancel buttons
				_container.find('.btn-go-back').on('click', function () {
					_win.history.back();
				});
			}
		};
		
		
		var __construct = (function () {
		
			var m = Method;
		
			_container = $(".page");
			
			m.safeConsole();
			
			$(function () {
				m.ui();
				m.moveModals();
				m.initForms();
			});
			
			Facebook();
			
			_gd = _win.GD = {
				callback: _callbacks
			};
			
		}());
		
		
		// Make it safe to use console.log always
		
		
	}.call({}, window));
    
} catch (e) {

	if (typeof window.console === "object") {
		console.log("[GiftDibs Error] ", e.message);
	}

}