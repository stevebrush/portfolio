/*! BBNC Localize Parts (c) Blackbaud, Inc */
;(function ($) {
	var settings;
	var defaults = {
		cacheTerms: true,
		complete: null,
		crossDomain: false,
		crossDomainProxy: "//api.blackbaud.com/services/proxy/index.php",
		cscPopupUrl: null,
		termsUrl: null,
		translateTo: "French"
	};
	
	var container;
	var json = {};
	
	var methods = {
		fetchTerms: function (callback) {
			
			var json = methods.getTerms();
			
			if (!$.isEmptyObject(json)) {
				callback();
				return;
			}
			
			var url = (settings.crossDomain) ? settings.crossDomainProxy + "?url=" + settings.termsUrl : settings.termsUrl;
			
			$.getJSON(url, function (r) {
				if (typeof window.console === "object") {
					console.log("[SUCCESS] Localization terms loaded: ", r);
				}
				if (settings.crossDomain) {
					methods.setTerms(r.contents.localizer);
				} else {
					methods.setTerms(r.localizer);
				}
				callback();
			});
		},
		fixes: function () {
			container.find('table.BBFormTable[summary="Event Calendar"] span.ListViewDateLabel').each(function () {
				var thisHTML = this.innerHTML.split(': ');
				$(this).html('<span class="labelText">'+thisHTML[0]+': </span><span class="date">'+thisHTML[1]+'</span>');
			});
		},
		getTerms: function () {
			if (typeof sessvars === "object" && typeof sessvars.jsonTerms === "object" && !$.isEmptyObject(sessvars.jsonTerms)) {
				json = sessvars.jsonTerms;
			}
			return json;
		},
		setTerms: function (arr) {
			if (typeof sessvars === "object" && settings.cacheTerms) {
				sessvars.jsonTerms = arr;
			}
			json = arr;
		},
		translate: function () {
			var _cont = container;
			var _terms = methods.getTerms();
			var _to = settings.translateTo;
			var addThis = (function () {
				var img = _cont.find('.addthis_button img');
				var src = img.attr("src");
				if (typeof src === "string") {
					img.attr('src', src.replace('en', 'fr'));
				}
			})();
			var buttons = function (o) {
				var oButton, elements;
				var i, btn;
				while (oButton = o.pop()) {
					_cont.find(oButton["Element"]).each(function (i, btn) {
						if (btn.value === oButton["English"]) {
							btn.value = oButton[_to];
						}
					});
				}
			};
			var countries = function (o) {
				var len = o.length;
				_cont.find('select.BBFormSelectList[id*="Country"] option').each(function (i, option) {
					var text = option.innerHTML;
					var country;
					for (var k = len; k--;) {
						country = o[k];
						if (country["English"] === text) {
							option.innerHTML = country[_to];
							break;
						}
					}
				});
			};
			var dates = function (o) {
				var len = o.length;
				var _from;
				_cont.find('select[id*="ddlMonthYear"] option, span.ListViewDateLabel, span.ListViewEventDate, span.CalendarViewMonthYearLabel, span.CalendarViewTodayText').each(function (i, option) {
					var text = option.innerHTML;
					var oDate;
					for (var k = len; k--;) {
						oDate = o[k];
						_from = oDate["English"];
						if (text.indexOf(_from) > -1) {
							text = text.replace(_from, oDate[_to]);
						}
					}
					option.innerHTML = text;
				});
			};
			var donationCSC = (function () {
				if (typeof settings.cscPopupUrl === "string") {
					_cont.find('a.BBLinkHelpIcon').on('click', function (e) {
						e.preventDefault();
						window.open(settings.cscPopupUrl, "mywindow", "menubar=1,scrollbars=1,resizable=1,width=475,height=400");
						return false;
					});
				}
			})();
			var parts = function (o) {
				var oPart;
				while (oPart = o.pop()) {
					_cont.find(oPart.selector).each(function () {
						
						var part = $(this);
						var oTerm;
						var $e;
						var element;
						var elements;
						var en;
						var fr;
						
						for (var i = 0, len = oPart.terms.length; i < len; i++) {
							
							oTerm = oPart.terms[i];
							elements = part.find(oTerm["Element"]);
							en = oTerm["English"];
							fr = oTerm[_to];
							
							for (var k = 0, kLen = elements.length; k < kLen; k++) {
							
								element = $(elements[k]);
								
								if (element.text().indexOf(en) > -1 && element.hasClass("is-localized") === false) {
									element.html(function () {
										return element.html().replace(en, fr);
									}).addClass("is-localized");
									break;
								}
							}
						}
					});
				}
			};
			var init = (function () {
				var c;
				var temp = JSON.parse(JSON.stringify(methods.getTerms())); // clone
				if (temp) {
					while (c = temp.pop()) {
						if (c.buttons) {
							buttons(c.buttons);
						}
						if (c.countries) {
							countries(c.countries);
						}
						if (c.dates) {
							dates(c.dates);
						}
						if (c.parts) {
							parts(c.parts);
						}
					}
				}
			})();
		},
		format: function () {

			var _cont = container;

			// Datepickers
			_cont.find('input.hasDatepicker').each(function () {
				var input = $(this);
				var placeholder = input.attr("placeholder");
				if (placeholder === "dd/mm/yyyy") {
					input.attr("placeholder", "jj/mm/aaaa");
				} else if (placeholder === "mm/dd/yyyy") {
					input.attr("placeholder", "mm/jj/aaaa");
				}
			});

			// Prevent French users from entering '.' or ',' in amount fields
			_cont.find('input[id*="txtAmount"], input[id*="dgCart_txtDesAmount"]').on('keypress', function (e) {
				if (e.which === 44 || e.which === 46) {
					e.preventDefault();
				}
			});

			// Currency
			_cont.find('.BBFormTable').each(function () {
				if (settings.translateTo !== "French") {
					return false;
				}
				$(this).find(":not(iframe,script)").andSelf().contents().filter(function () {
					var text = $(this).text();
					if (this.nodeType == 3 && text.indexOf('$') > -1) {
						// $50.00
						// $50.00 .ea
						var originalPrice = text.split(" ")[0];
						var localizedPrice = originalPrice.replace(',', '\u00A0').replace('.00', '').replace('.', ',').replace('$', '') + " $";
						this.nodeValue = text.replace(originalPrice, localizedPrice);
					}
				});
			});
		}
	};
	$.fn.BBNCLocalizeParts = function (options) {
	
		container = this;
		settings = $.extend(true, {}, defaults, options);
		
		if (settings.cacheTerms == "false") {
			sessvars.$.clearMem();
		} else {
			$.ajaxSetup({ cache: true });
		}
		
		if ((settings.termsUrl.indexOf("http:") === -1 || settings.termsUrl.indexOf("http:") === -1) && settings.crossDomain == true) {
			settings.termsUrl = "https:" + settings.termsUrl;
		}
		
		methods.fetchTerms(function () {
			methods.fixes();
			methods.translate();
			methods.format();
			if (typeof settings.complete === "function") {
				settings.complete();
			}
		});
		
	};
}(jQuery));