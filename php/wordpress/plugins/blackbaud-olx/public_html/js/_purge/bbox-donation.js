var BBOXFormAddressBlock;
if (!BBOXFormAddressBlock) {
	BBOXFormAddressBlock = {
		initialize: function() {
			var c = bb$;
			function a(e, f, d) {
				e.find('[data-country-field="' + f + '"]').val(d)
			}
			function b(f) {
				var d = f.find(".BBFormCountryDropDown option:selected").attr("data-country-format"),
					e = f.find("[data-country-format-for]");
				if (d) {
					e.filter("[data-country-format-for=" + d + "]").show();
					e.filter("[data-country-format-for!=" + d + "]").hide()
				}
			}
			c(".BBFormCountryDropDown").change(function() {
				var d = c(this).closest(".BBFormAddressBlock");
				b(d)
			});
			c(".BBFormSection [data-country-sync]").change(function() {
				var e = c(this),
					d = c(this).closest(".BBFormAddressBlock");
				a(d, e.attr("data-country-field"), e.val())
			});
			c(".BBFormAddressBlock").each(function() {
				b(c(this))
			})
		}
	}
}
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.address = BBOXFormAddressBlock;
var BBOXBillingSection = {
	initialize: function() {
		var b = bb$;
		function a(c) {
			var d = b("#bboxdonation_billing_chkOrgGift");
			if (d.is(":checked")) {
				BBOXDonationForm.elementShow(".BBFormOrgFields", c);
				b("[data-billing-field=orgname]:visible").focus()
			} else {
				if (d.length) {
					BBOXDonationForm.elementHide(".BBFormOrgFields", c)
				}
			}
		}
		b("#bboxdonation_billing_chkOrgGift").on("change", function() {
			a()
		});
		a(true)
	}
};
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.billing = BBOXBillingSection;
var BBOXPaymentSection = {
	initialize: function() {
		var f = bb$,
			e = {
				sticky: true,
				dropShadow: false,
				activation: "click",
				cluetipClass: "BBForm",
				closePosition: "title",
				ajaxSettings: {
					dataType: "json",
					jsonpCallback: "bboxCallback"
				},
				ajaxProcess: function(g) {
					return g.content
				}
			};
		if (f.cluetip) {
			f("#cscWhatsThis:not(.hasTooltip)").cluetip(f.extend({
				width: "350"
			}, e)).addClass("hasTooltip");
			f("#DDGuaranteeWhatsThis:not(.hasTooltip").cluetip(f.extend({
				width: "350",
				local: true
			}, e)).addClass("hasTooltip)");
			f("#bboxsecure:not(.hasTooltip)").cluetip(f.extend({
				width: "420"
			}, e)).addClass("hasTooltip");
			f("#routingUSWhatsThis:not(.hasTooltip), #routingUKWhatsThis:not(.hasTooltip), #routingCAWhatsThis:not(.hasTooltip)").cluetip(f.extend({
				width: "400"
			}, e)).addClass("hasTooltip")
		}
		f(".BBDFormSectionPaymentInfo input[data-pmtchoice]").change(function() {
			c()
		});
		function c() {
			bb$(".BBFormPaymentRadioOptions input").each(function() {
				var m = f(this).is(":checked");
				f(this).next("label").toggleClass("BBFormRadioPaymentSelected", m);
				f(this).next("label").toggleClass("BBFormRadioPaymentNotSelected", !m)
			});
			var j = f(".BBDFormSectionPaymentInfo input[data-pmtchoice][value='0']"),
				h = f(".BBDFormSectionPaymentInfo input[data-pmtchoice][value='2']"),
				g = j.is(":checked"),
				k = h.is(":checked"),
				i = f("#BBFormCCDetails"),
				l = f(".BBFormDirectDebitDetails");
			if (j.length) {
				i.toggle(g)
			}
			if (h.length) {
				l.toggle(k)
			}
		}
		function a(i) {
			var k = /^4/,
				g = /^5[1-5]/,
				l = /^3[47]/,
				j = /^6(?:011|5|4[4-9]|22(?:1(?:2[6-9]|[3-9])|[2-8]|9(?:[01]|2[0-5])))/,
				h = /^(?:5[0678]|6304|6390|67)/;
			if (k.test(i)) {
				return "visa"
			} else {
				if (g.test(i)) {
					return "mastercard"
				} else {
					if (l.test(i)) {
						return "amex"
					} else {
						if (j.test(i)) {
							return "discover"
						} else {
							if (h.test(i)) {
								return "maestro"
							} else {
								return "unknown"
							}
						}
					}
				}
			}
		}
		function b(g) {
			f("#BBFormCCDetails select[id$=cboCardType]").val(f("[data-card-type=" + g + "]").data("card-type-id"));
			f(".BBCardImage").each(function(i, h) {
				if (g === "unknown") {
					f(h).attr("src", f(h).attr("src").replace("_disabled", "_normal"))
				} else {
					if (f(h).data("card-type") === g) {
						f(h).attr("src", f(h).attr("src").replace("_disabled", "_normal"))
					} else {
						f(h).attr("src", f(h).attr("src").replace("_normal", "_disabled"))
					}
				}
			})
		}
		function d() {
			if (f(".BBCardNumber").val().length > 0) {
				var g = f(".BBCardNumber").val().replace(/ /g, "").replace(/-/g, "");
				b(a(g))
			}
		}
		f("#BBFormCCDetails input[id$=txtCardNumber]").on("keyup change blur", function() {
			d()
		});
		c();
		d()
	}
};
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.payment = BBOXPaymentSection;
var BBOX = BBOX || {};
(function() {
	var a = bb$;
	BBOX.addMetaViewportTag = function() {
		if (a("meta[name=viewport]").length > 0) {
			return
		}
		a("head").append('<meta name="viewport" content="width=device-width">')
	};
	BBOX.adjustFontSize = function(b) {
		if (!b) {
			return
		}
		var c = 2,
			d = parseInt(a(b).css("font-size"), 10);
		if (a(b).height() > a(b).parent().height() || a(b).width() > a(b).parent().width()) {
			a(b).css("font-size", (d - c) + "px").css("line-height", (d - c) + "px");
			BBOX.adjustFontSize(b)
		}
	};
	BBOX.setupDropdowns = function() {
		a("select").on("change", function(c) {
			var b = a(this);
			if (b.children('[default="default"]:selected').length > 0) {
				b.addClass("GhostText")
			} else {
				b.removeClass("GhostText")
			}
		});
		a("select").change()
	}
})();
var BBOXValidation = BBOXValidation || {};
(function() {
	var b = bb$,
		a;
	BBOXValidation.initialize = function(c) {
		a = b('.BBFormContainer[data-bbox-part-id="' + c + '"]')
	};
	BBOXValidation.showErrorMessage = function(c, h, g) {
		function i(j, l) {
			var k = parseInt(j.data("datatypeid"), 10),
				m = l ? " for field: " + l : "";
			switch (k) {
			case 1:
				return "Please enter a value" + m;
			case 2:
				return "Please enter a valid number" + m;
			case 3:
				return "Please enter a valid date" + m;
			case 4:
				return "Please enter a valid currency value" + m;
			case 5:
				return "Please select yes or no" + m;
			case 6:
				return "Please select a value" + m;
			default:
				return "Please enter a value" + m
			}
		}
		function e(n, m, l) {
			var o, k, q, j = l ? "" : "\u2022 ",
				p = l ? "BBFormErrorItem BBFormInlineErrorItem" : "BBFormErrorItem BBFormClientErrorItem";
			if (n) {
				o = n.attr("id");
				k = b("[for=" + o + "]").last().text();
				q = n.closest(".BBFormAttribItem");
				if (!m) {
					if (q.length) {
						m = j + i(q, k)
					} else {
						if (n.is("select")) {
							m = j + "Please select " + k.toLowerCase().replace(":", "")
						} else {
							m = j + "Please enter " + k.toLowerCase().replace(":", "")
						}
					}
				}
			} else {
				if (m) {
					m = j + m
				} else {}
			}
			return '<div class="' + p + '">' + m + "</div>"
		}
		var d, f = b("#divClientError");
		if (c) {
			d = c.closest(".BBFormFieldContainer");
			if (!d.length) {
				return
			}
			d.addClass("BBFormErrorBlock BBFormInlineError");
			d.append(e(c, h, true))
		}
		if (!g) {
			if (f.children().length < 1) {
				f.append('<div class="BBFormErrorItem">We\'ve run into a slight problem. Can you correct the following to continue?</div>')
			}
			f.append(e(c, h, false))
		}
	};
	BBOXValidation.showErrorBlock = function() {
		b("#divClientError").show();
		b("html, body").scrollTop(Math.max(b(".BBFormErrorBlock:visible").first().offset().top - 40, 0))
	};
	BBOXValidation.clearErrorBlock = function() {
		b(".BBFormErrorBlock.BBFormInlineError").each(function() {
			b(this).removeClass("BBFormErrorBlock BBFormInlineError")
		});
		b(".BBFormServerErrorItem, .BBFormClientErrorItem, .BBFormInlineErrorItem").remove();
		b("#divClientError, #divError").hide()
	};
	BBOXValidation.showInlineErrors = function() {
		b("[data-error]").each(function() {
			var c = b(this),
				d = c.data("error");
			BBOXValidation.showErrorMessage(c, d, true)
		})
	};
	BBOXValidation.clientValidate = function() {
		var c = true;
		BBOXValidation.clearErrorBlock();
		a.find("input[required]:visible, textarea[required]:visible, select[required]:visible, .BBFormChecklist[required]:visible").each(function() {
			var e = b(this),
				f = e.attr("id"),
				d;
			if (e.is("input") || e.is("textarea")) {
				if (b.trim(e.val()) === "") {
					BBOXValidation.showErrorMessage(e);
					c = false
				}
			} else {
				if (e.is("select")) {
					d = e.find("option:selected");
					if (d.attr("default") || d.attr("value") === "") {
						BBOXValidation.showErrorMessage(e);
						c = false
					}
				} else {
					if (e.hasClass("BBFormChecklist")) {
						if (e.find("input:checked").length < 1) {
							BBOXValidation.showErrorMessage(e);
							c = false
						}
					} else {}
				}
			}
		}); if (c) {
			BBOXValidation.clearErrorBlock()
		} else {
			BBOXValidation.showErrorBlock()
		}
		return c
	}
})();
var BBOXDesignationSection = {
	initialize: function() {
		var b = bb$;
		function a() {
			var c = b("#bboxdonation_designation_ddDesignations").val() === "0";
			BBOXDonationForm.elementToggle("#bboxdonation_designation_txtOtherDesignation", true, c);
			if (c) {
				b("#bboxdonation_designation_txtOtherDesignation").focus()
			}
		}
		b("#bboxdonation_designation_ddDesignations").change(function() {
			a()
		});
		a()
	}
};


var BBOXSectionScripts = BBOXSectionScripts || {};

BBOXSectionScripts.designation = BBOXDesignationSection;



var BBOXDonationForm,
BBOXForm = {};


BBOXForm.display = function(c, a, b) {
	var d = bb$;
	if (!BBOXDonationForm) {
		BBOXDonationForm = {
			initialize: function() {
				var f = d(".labelEditor").is("[data-label-edit='1']");
				function j() {
					var k = d("#bboxdonation_recurrence_lblRecurrenceDate"),
						m;
					if (k.length) {
						try {
							m = d(".BBFormRadioLabelGivingLevelSelected .BBFormRadioAmount").html();
							if (m === null) {
								m = d(".BBFormTextbox.BBFormCurrency").val();
								m = d("<input>").val(m || 0).formatCurrency({
									region: d(".BBFormCurrency").data("culture")
								}).val()
							}
							if (m.indexOf("$") === 0) {
								m = "$" + m
							}
							k.html(k.attr("data-label").replace(/\{AMT\}/, m))
						} catch (l) {}
					}
				}
				function g(l) {
					var m = parseFloat(d(".BBFormProgress-Amount.BBFormProgress-Goal").attr("value")),
						k = parseFloat(d(".BBFormProgress-Bar.BBFormProgress-Bar-Raised").attr("value")),
						n = (100 * l) / m;
					return (n + k) > 100 ? (100 - k) : n
				}
				function e() {
					var k = d(".BBFormProgressContainer"),
						l = d(".BBFormProgress-Bar.BBFormProgress-Bar-New"),
						n = d(".BBFormTextbox.BBFormCurrency"),
						m, o;
					if (!f) {
						if (k.length) {
							m = parseFloat(d(".BBFormRadioGivingLevelSelected").attr("value"));
							if (isNaN(m)) {
								m = n.asNumber({
									region: n.data("culture")
								});
								if (isNaN(m)) {
									m = 0
								}
							}
							o = g(m)
						}
					} else {
						o = 10
					}
					l.css("width", o + "%");
					l.attr("value", o)
				}
				d(".BBFormCurrency").blur(function() {
					var l = d(this),
						k = {};
					if (l.attr("id").indexOf("txtOtherAmountButtons") >= 0) {
						k.groupDigits = false;
						k.roundToDecimalPlace = 2;
						k.symbol = ""
					} else {
						k.region = l.data("culture")
					}
					l.formatCurrency(k);
					j();
					e()
				});
				d(".BBFormCurrency").on("keyup", function() {
					e()
				});
				d(".BBFormRadioGivingLevel").change(function() {
					var l = d(this).closest(".BBFormRadioList").find(".BBFormGiftOtherAmount"),
						k = d(this).hasClass("BBFormRadioGivingLevelOther");
					bbFormToggleGivingLevels();
					if (d(this).hasClass("BBFormRadioGivingLevelSelected")) {
						j();
						e();
						if (k) {
							l.attr("required", "required")
						} else {
							l.removeAttr("required")
						}
					}
				});
				if (typeof(bbFormToggleGivingLevels) !== "undefined") {
					bbFormToggleGivingLevels();
					j();
					e()
				}
				d.each(BBOXSectionScripts, function() {
					if (typeof this.initialize === "function") {
						this.initialize()
					}
				});
				function i() {
					var k = window,
						l = "inner";
					if (!("innerWidth" in window)) {
						l = "client";
						k = document.documentElement || document.body
					}
					return k[l + "Width"]
				}
				function h() {
					var k = bb$(".BBFormContainer"),
						m = i(),
						l = k.width();
					k.removeClass("BBFormWidthNarrow-Less600").removeClass("BBFormWidthNarrow-Less500").removeClass("BBFormWidthNarrow-Less400");
					if (m > 600) {
						if (l < 600) {
							k.addClass("BBFormWidthNarrow-Less600")
						}
						if (l < 500) {
							k.addClass("BBFormWidthNarrow-Less500")
						}
						if (l < 400) {
							k.addClass("BBFormWidthNarrow-Less400")
						}
					}
				}
				h();
				if (d(".hdnMetaTag").val()) {
					BBOX.addMetaViewportTag()
				}
				BBOX.setupDropdowns();
				BBOXValidation.initialize(b);
				BBOXValidation.showInlineErrors()
			},
			elementShow: function(e, f) {
				if (f) {
					d(e).show()
				} else {
					d(e).slideDown()
				}
			},
			elementHide: function(e, f) {
				if (f) {
					d(e).hide()
				} else {
					d(e).slideUp()
				}
			},
			elementToggle: function(e, f, g) {
				if (g) {
					BBOXDonationForm.elementShow(e, f)
				} else {
					BBOXDonationForm.elementHide(e, f)
				}
			}
		}
	}
	BBOXDonationForm.initialize()
};


BBOXForm.presubmit = function () {

	var b = bb$,
		a = true;

	if (BBOXValidation) {
		a = BBOXValidation.clientValidate();
	}

	b.each(BBOXSectionScripts, function () {
		if (typeof this.presubmit === "function") {
			if (a) {
				a = this.presubmit()
			}
		}
	});

	return a

};



var BBOXGiftAttributesSection = {
	initialize: function(a) {},
	createAttribute: function() {
		return {
			typeid: 0,
			datatypeid: 0,
			oneperrecord: true,
			value: ""
		}
	},
	presubmit: function() {
		var e = bb$,
			d = this,
			b = [],
			a;
		function c() {
			var f = d.createAttribute();
			f.typeid = e(this).attr("data-attribtypeid");
			f.oneperrecord = (e(this).attr("data-oneperrecord") === "true");
			f.datatypeid = e(this).attr("data-datatypeid");
			f.value = "";
			switch (f.datatypeid) {
			case "1":
			case "2":
			case "3":
			case "4":
				f.value = e(this).find(".BBFormAttrText").val();
				break;
			case "5":
				f.value = e(this).find(".BBFormAttrYesNo").val();
				break;
			case "6":
				if (f.oneperrecord) {
					f.value = e(this).find(".BBFormAttrSelect").val()
				} else {
					f.value = "";
					e(this).find(".BBFormChecklistCheck:checked").each(function(g, h) {
						f.value = f.value + e(this).val() + ";"
					})
				}
				break
			}
			b.push(f)
		}
		e(".BBFormSubSectionGiftAttributes .BBFormAttribItem").each(c);
		a = JSON.stringify(b);
		e(".hdnJsonGiftAttributes").val(a);
		return true
	}
};
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.giftattributes = BBOXGiftAttributesSection;

function bbFormToggleGivingLevels() {
	var a = bb$(".hdnGivingLevelButtonsEnabled").val() === "true";
	bb$(".BBFormRadioGivingLevel").each(function() {
		var d = bb$,
			c = d(this).is(":checked"),
			b = d(".BBFormGiftOtherAmount").attr("placeholder");
		d(this).toggleClass("BBFormRadioGivingLevelSelected", c);
		d(this).next("label").toggleClass("BBFormRadioLabelGivingLevelSelected", c);
		d(this).next("label").toggleClass("BBFormRadioLabelGivingLevelNotSelected", !c);
		if (!a) {
			if (d(this).hasClass("BBFormRadioGivingLevelOther")) {
				if (c) {
					if (!d.browser.msie || parseInt(d.browser.version, 10) > 7) {
						if (d(".BBFormGiftOtherAmount").is(":focus") === false) {
							d(".BBFormGiftOtherAmount").focus()
						}
					}
				} else {
					d(".BBFormGiftOtherAmount").val("")
				}
			}
		}
	})
}
var BBOXGiftSection = {
	initialize: function() {
		var b = bb$;
		function a() {
			setTimeout(function() {
				b(".BBFormGiftOtherAmount").focus()
			}, 0)
		}
		if (b(".hdnGivingLevelButtonsEnabled").val() === "true") {
			b(".BBFormRadioLabelGivingLevelOther").on("click", function() {
				a()
			});
			b(".BBFormGiftOtherAmount").keyup(function(c) {
				if ((c.which >= 48 && c.which <= 90) || (c.which >= 96 && c.which <= 105) || (c.which === 110) || (c.which === 190)) {
					b(".BBFormRadioGivingLevel").prop("checked", false).change();
					b(".BBFormRadioGivingLevelOther").prop("checked", true).change()
				}
			})
		} else {
			b(".BBFormGiftOtherAmount").on("click", function() {
				a()
			});
			b(".BBFormGiftOtherAmount").on("focus", function() {
				if (b(this).parent().siblings("input:radio").is(":checked") === false) {
					b(this).parent().siblings("input:radio").attr("checked", "checked").change()
				}
			})
		}
	}
};
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.gift = BBOXGiftSection;
var BBOXRecurrenceSection = {
	initialize: function() {
		var b = bb$;
		function a(c) {
			BBOXDonationForm.elementToggle(".BBFormFieldRecurrenceInfo", c, b("#bboxdonation_recurrence_chkMonthlyGift").is(":checked"))
		}
		b("#bboxdonation_recurrence_chkMonthlyGift").change(function() {
			a()
		});
		a(true)
	}
};
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.recurrence = BBOXRecurrenceSection;
var BBOXTributeSection = {
	initialize: function() {
		var c = bb$;
		function a(d) {
			var e = c("#bboxdonation_tribute_chkTributeGift");
			if (e.is(":checked")) {
				BBOXDonationForm.elementShow("#divGeneralTributeInfo, #divTributeAcknowledge", d);
				c("#bboxdonation_tribute_ddTributeTypes").focus()
			} else {
				if (e.length) {
					BBOXDonationForm.elementHide("#divGeneralTributeInfo, #divTributeAcknowledge", d)
				}
			}
		}
		function b(e) {
			var d = c("#bboxdonation_tribute_chkTributeAcknowledgee").is(":checked");
			BBOXDonationForm.elementToggle("#divTributeAcknowledgeeInfo", e, d);
			if (d) {
				c("#bboxdonation_tribute_txtFirstName").focus()
			}
		}
		c("#bboxdonation_tribute_chkTributeGift").change(function() {
			a()
		});
		c("#bboxdonation_tribute_chkTributeAcknowledgee").change(function() {
			b()
		});
		a(true);
		b(true)
	}
};
var BBOXSectionScripts = BBOXSectionScripts || {};
BBOXSectionScripts.tribute = BBOXTributeSection;
