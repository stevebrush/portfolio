(function (win) {
	try {
	
	
		var $ = win.jQuery;
		
		
		$(function () {
			
			
			var formDonation = $(".lo-form-donate");
			var formLogin = $(".lo-form-login");
			var formRegister = $(".lo-form-register");
			var formSurvey = $(".lo-form-survey");
			
			
			/*
			LUMINATE EXTEND CALLBACKS
			*/
			win.loForms_donateCallback = {
				error: function (data) {
					console.log("[L-OL] Donate Response (error): ", data);
					methods.form.failed(formDonation, data.errorResponse.message);
				},
				success: function (data) {
				
					console.log("[L-OL] Donate Response (success): ", data);
				
					var r = data.donationResponse;
					var html = "";
					
					if (r.errors) {
						html += (r.errors.message) ? '<strong>' + r.errors.message + '</strong><br>' : '';
						if (r.errors.fieldError) {
							var fieldErrors = luminateExtend.utils.ensureArray(r.errors.fieldError);
							html += "<ul>";
							$.each(fieldErrors, function () {
								html += '<li>' + this + '</li>';
							});
							html += "</ul>";
						}
						methods.form.error(formDonation, html);
						
					} else {
						html = "Your donation has been processed!";
						html += "<p>Thank you for your donation of $" + data.donationResponse.donation.amount.decimal + ".</p>";
						if (typeof data.donationResponse.donation.confirmation_code === "string") {
							html += "<p>Your confirmation code is " + data.donationResponse.donation.confirmation_code + ".</p>";
						}
						methods.form.success(formDonation, html);
					}
				}
			};
			
			win.loForms_loginCallback = {
				error: function (data) {
					console.log("[L-OL] Login Response (error): ", data);
				},
				success: function (data) {
					console.log("[L-OL] Login Response (success): ", data);
					methods.getUser();
				}
			};
			
			win.loForms_profileCallback = {
				error: function (data) {
					console.log("[L-OL] Profile Response (error): ", data);
				},
				success: function (data) {
					console.log("[L-OL] Profile Response (success): ", data);
				}
			};
			
			win.loForms_registerCallback = {
				error: function (data) {
					console.log("[L-OL] Register Response (error): ", data);
					methods.form.failed(formRegister, data.errorResponse.message);
				},
				success: function (data) {
					console.log("[L-OL] Register Response (success): ", data);
				}
			};
			
			win.loForms_resetPasswordCallback = {
				error: function (data) {
					console.log("[L-OL] Reset Password Response (error): ", data);
				},
				success: function (data) {
					console.log("[L-OL] Reset Password Response (success): ", data);
				}
			};
			
			win.loForms_surveyCallback = {
				error: function (data) {
					console.log("[L-OL] Survey Response (error): ", data);
					methods.form.failed(formSurvey, data.errorResponse.message);
				},
				success: function (data) {
				
					console.log("[L-OL] Survey Response (success): ", data);
				
					var r = data.submitSurveyResponse;
					var html = "";
					
					if (r.errors) {
					
						html += '<strong>There was an error with your submission. Please try again.</strong><br>';
						
						if (r.errors) {
							var fieldErrors = luminateExtend.utils.ensureArray(data.submitSurveyResponse.errors);
							html += "<ul>";
							$.each(fieldErrors, function () {
								var field;
								if (this.errorField) {
									field = formSurvey.find('[name="' + this.errorField + '"]');
								} else {
									field = formSurvey.find('[name*="question_' + this.questionInError + '"]:eq(0)');
								}
								html += '<li>' + field.closest('.form-group').find('label:eq(0)').text().replace("*","") + ": " + this.errorMessage + '</li>';
							});
							html += "</ul>";
						}
						methods.form.error(formSurvey, html);
						
					} else {
						html = "<h4>Submission successful!</h4>";
						methods.form.success(formSurvey, html);
					}
				}
			};
			
			
			/*
			PRIVATE METHODS
			*/
			var methods = {
				crossBrowserPlaceholder: function () {
					$('[placeholder]').focus(function () {
					  var input = $(this);
					  if (input.val() == input.attr('placeholder')) {
						input.val('');
						input.removeClass('placeholder');
					  }
					}).blur(function() {
					  var input = $(this);
					  if (input.val() == '' || input.val() == input.attr('placeholder')) {
						input.addClass('placeholder');
						input.val(input.attr('placeholder'));
					  }
					}).blur();
					$('[placeholder]').parents('form').submit(function() {
					  $(this).find('[placeholder]').each(function() {
						var input = $(this);
						if (input.val() == input.attr('placeholder')) {
						  input.val('');
						}
					  })
					});
				},
				form: {
					bind: function () {
						luminateExtend.api.bind();
					},
					error: function (form, message) {
						form.find('.lo-alert').addClass("alert-danger").removeClass("alert-success").html(message);
						methods.form.onResponse(form);
					},
					failed: function (form, message) {
						form.find('.lo-alert').addClass("alert-danger").removeClass("alert-success").html(message);
						methods.form.onResponse(form);
					},
					onResponse: function (form) {
						form.find('.lo-alert').css({"display": "block"});
						form.css({"display": "block"}).find('.lo-loader').remove();
					},
					prepare: function () {
					
						$('form.luminateApi').on("submit", function () {
							win.scrollTo(0, 0);
							$(this).hide().prepend('<div class="well loader lo-loader">Loading...</div>');
						});
					
						if (formDonation.length > 0) {
					
							// Show/hide billing information
							methods.showHideBilling();
							formDonation.find('[name="same-as-donor"]').on('click', methods.showHideBilling);
							formDonation.find('.fieldset-donor input').on('blur', methods.showHideBilling);
							formDonation.find('.fieldset-donor select').on('change', methods.showHideBilling);
							
							// Show/hide other amount
							methods.showHideOtherAmount();
							
						} 
						
						/* DATE SELECT BOXES */
						$('.form-date-select-container').each(function () {
							var div = $(this);
							var input = div.find('input[type="hidden"]');
							var selects = div.find('select');
							selects.on('change', function () {
								var string = "";
								selects.each(function () {
									string += $(this).val() + "-";
								});
								string = string.slice(0, -1);
								input.val(string);
							});
						});
						
						/* COMBO SELECT RADIOS */
						$('.lo-combo-select-container').each(function () {
							var container = $(this);
							container.find('input[type="radio"]').on('click', function () {
								var radio = $(this);
								if (radio.hasClass('lo-combo-select-other')) {
									radio.next().prop("disabled", 0);
								} else {
									console.log(container.find('.lo-combo-select-other'));
									container.find('.lo-combo-select-other').next().attr("disabled", "disabled").val("");
								}
							});
						});
						
					},
					success: function (form, message) {
						form.find('.lo-alert').addClass("alert-success").removeClass("alert-danger").html(message);
						methods.form.onResponse(form);
					}
				},
				getUser: function () {
					luminateExtend.api({
						api: "cons",
						callback: function (data) {
							console.log("[L-OL] getUser: ", data);
							if (data.getConsResponse && data.getConsResponse.name) {
								formLogin.replaceWith("<p class=\"navbar-text pull-right\" id=\"welcome-back\">" + "Welcome back" + ((data.getConsResponse.name.first) ? (", " + data.getConsResponse.name.first) : "") + "! " + "<a href=\"" + luminateExtend.global.path.nonsecure + "UserLogin?logout=&NEXTURL=" + encodeURIComponent(win.location.href) + "\">Logout</a></p>");
							}
						},
						data: "method=getUser",
						requestType: "POST",
						requiresAuth: true
					});
				},
				loginTest: function () {
					luminateExtend.api({
						api: "cons",
						callback: {
							success: function (data) {
								console.log("[L-OL] Login Test (success): ", data);
								methods.getUser();
							},
							error: function (data) {
								console.log("[L-OL] Login Test (error): ", data);
								formLogin.removeClass("hide");
							}
						},
						data: "method=loginTest"
					}); 
				},
				logoutLink: function () {
					if (typeof loApiData !== "undefined") {
						$('a.lo-log-out-link, .lo-log-out-link > a').each(function () {
							$(this).attr('href', loApiData.http + "CRConsAPI/?method=logout&api_key=" + loApiData.key + "&v=" + loApiData.version + "&redirect=" + loApiData.permalinks.login + "&sign_redirects=true");
						});
					}
				},
				showHideBilling: function () {
				
					var billingFields = formDonation.find('.fieldset-billing');
					var donorFields = formDonation.find('.fieldset-donor');
					
					if (!formDonation.find('[name="same-as-donor"]').is(':checked')) {
						billingFields.css({'display': 'block'});
						billingFields.find('input[type="text"], select').each(function () {
							$(this).val("");
						});
					} else {
						var billingInputs = billingFields.find('input, select');
						var donorInputs = donorFields.find('input, select');
						billingInputs.each(function () {
							var that = $(this);
							var name = this.name.replace("billing.","");
							donorInputs.each(function () {
								if (this.name.indexOf(name) > -1) {
									that.val($(this).val());
								}
							});
						});
						billingFields.css({'display':'none'});
					}
				},
				showHideOtherAmount: function () {
					formDonation.find('input[name="level_id"]').on('click', function () {
						var textbox = $(this).closest('.form-group').find('input[type="text"]');
						if ($(this).attr('data-user-specified') == "true") {
							textbox.prop('disabled', 0).val("").css({'display':'block'});
						} else {
							textbox.prop('disabled', 1).val("").css({'display':'none'});
						}
					});
				}
			};
			
			
			/*
			INIT
			*/
			var __construct = (function () {
				methods.crossBrowserPlaceholder();
				methods.logoutLink();
				methods.loginTest();
				methods.form.prepare();
				methods.form.bind();
			}());
			
			
			/*
			SAFE CONSOLE
			*/
			win.log = function() {
				// Make it safe to use console.log always
				log.history = log.history || [];
				log.history.push(arguments);
				if (win.console) {
					arguments.callee = arguments.callee.caller;
					var a = [].slice.call(arguments);
					if (typeof console.log === "object") {
						log.apply.call(console.log, console, a);
					} else {
						console.log.apply(console, a);
					}
				}
			};
			(function(d) {
				// Add properties to console object, if they exist.
				// Otherwise, assign empty function.
				var f;
				var fns = ['assert', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'timeStamp', 'profile', 'profileEnd', 'time', 'timeEnd', 'trace', 'warn'];
				var a = function () {};
				while (f = fns.pop()) {
					d[f] = d[f] || a;
				}
			})((function() {
				// Return win.console if it exists.
				// Otherwise, return an empty object.
				try {
					console.log();
					return win.console;
				} catch (b) {
					return win.console = {};
				}
			})());
			
			
		});
	} catch (e) {
		if (win.console) {
			console.log(e.message);
		}
	};
})(window);
