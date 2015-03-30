var _MongoServerUrlBase = "https://216.235.203.172/webforms/";
var _MongoServerUrl = _MongoServerUrlBase + "custom/mongo/scripts/";
if (!window.bbox) {
    window.bbox = {
        _partId: 0,
        _preview: false,
        _logging: false,
        _fetchResponse: null,
        loadScript: function (b, a) {
            var c = document.createElement("script");
            c.type = "text/javascript";
            c.async = true;
            c.src = b;
            bbox.log("loading script: " + b);
            document.getElementById("bbox-root").appendChild(c);
            if (c.addEventListener) {
                bbox.log("loading script. adding listener");
                c.addEventListener("load", a, false)
            } else {
                bbox.log("loading script. attaching event");
                c.attachEvent("onreadystatechange", readyHandler = function () {
                    if (/complete|loaded/.test(script.readyState)) {
                        a();
                        c.detatchEvent("onreadystatechange", readyHandler)
                    }
                })
            }
        },
        loadStylesheet: function loadStylesheet(b, a) {
            var c = document.createElement("link");
            c.rel = "stylesheet";
            c.type = "text/css";
            c.href = b;
            var e = document.getElementsByTagName("script")[0];
            e.parentNode.insertBefore(c, e);
            var d = document.createElement("span");
            d.id = "mongo-css-ready";
            e.parentNode.insertBefore(d, e);
            (function () {
                var f = document.getElementById("mongo-css-ready");
                if (window.getComputedStyle) {
                    value = document.defaultView.getComputedStyle(f, null).getPropertyValue("color")
                } else {
                    if (d.currentStyle) {
                        value = f.currentStyle.color
                    }
                } if (value && value === "rgb(121, 121, 121)" || value === "#797979") {
                    a()
                } else {
                    setTimeout(arguments.callee, 100)
                }
            })()
        },
        log: function (a) {
            if (typeof console == "undefined") {} else {
                if (this._logging) {
                    console.log(a)
                }
            }
        },
        message: function (b) {
            if (bb$("#bbox-msg-wrapper").length == 0) {
                var a = bb$("<div id='bbox-msg-wrapper'><img src='" + _MongoServerUrlBase + "images/ajax_loader_border2.gif' style='vertical-align:middle;'><span id='bbox-msg' style='padding:10px;'></span></div>");
                bb$("#bbox-root").append(a)
            }
            if (b == null) {
                bb$("#bbox-msg").html("");
                bb$("#bbox-msg-wrapper").hide()
            } else {
                if (b != null) {
                    bb$("#bbox-msg").html(b)
                }
            }
        },
        comment: function (a) {
            bb$("#bbox-root").append("<!--" + a + " -->")
        },
        notifyError: function (a) {
            bbox.log("bbox.notifyError(): " + a);
            if (this._preview) {
                alert(a)
            } else {
                bbox.message();
                bbox.comment(a)
            }
        },
        initDebug: function (b, a) {
            this._logging = true;
            bbox.log("bbox.initDebug()");
            return this.showForm(b, a)
        },
        showForm: function (d, b) {
            var c = this;
            c.log("bbox.showForm() partID: " + d + " preview:" + b);
            c._partId = d;
            if (b != null) {
                c._preview = b
            }
            c.message("");
            if (c._partId) {
                var a = bbox.server.getMarkup(c._partId, window.location.href, document.referrer)
            }
        },
        render: function (d) {
            var b = bbox;
            b.log("bbox.render()");
            b.message();
            var c = d.d || d;
            b._fetchResponse = c;
            if (c.NotFound) {
                b.comment("Form was not found. FormId:" + b._partId)
            } else {
                if (c.Inactive) {
                    b.comment("Form is no longer active. FormId:" + b._partId)
                } else {
                    if (c.ErrorLogged) {
                        b.comment("Error Logged. See server error log.")
                    } else {
                        if (c.StylesheetID > 0) {
                            b.log("bbox.render adding css link");
                            var a = bb$("<link>");
                            a.attr({
                                type: "text/css",
                                rel: "stylesheet",
                                href: _MongoServerUrlBase + c.StylesheetURL
                            });
                            bb$("head").append(a)
                        }
                        if (c.OverrideStylesheetID > 0) {
                            b.log("bbox.render adding override css link");
                            var a = bb$("<link>");
                            a.attr({
                                type: "text/css",
                                rel: "stylesheet",
                                title: "mongousersheet",
                                href: _MongoServerUrlBase + c.OverrideStylesheetURL
                            });
                            bb$("head").append(a)
                        }
                        bbox.squirtMarkup(c.Markup, false);
                        if (typeof window.bboxShowFormComplete == "function") {
                            window.bboxShowFormComplete()
                        }
                    }
                }
            }
        },
        squirtMarkup: function (c, d) {
            var a = bbox;
            var e = bb$;
            a.log("squirtMarkup()");
            if (e("#mongo-form").length == 0) {
                var b = e("<form>");
                b.attr({
                    id: "mongo-form"
                });
                e("#bbox-root").append(b)
            } else {
                e("#mongo-form").empty()
            }
            e("#mongo-form").append(c);
            e("#mongo-form").append(e("<input></input>").attr("name", "instanceId").attr("id", "instanceId").attr("type", "hidden").attr("value", this._fetchResponse.InstanceID));
            e("#mongo-form").append(e("<input></input>").attr("name", "partId").attr("id", "partId").attr("type", "hidden").attr("value", this._partId));
            e("#mongo-form").append(e("<input></input>").attr("name", "srcUrl").attr("id", "srcUrl").attr("type", "hidden").attr("value", window.location.href));
            if (e(".BBFormErrorBlock, .BBFormConfirmation").length > 0) {
                e("html,body").scrollTop(Math.max(e(".BBFormErrorBlock, .BBFormConfirmation").offset().top - 40, 0))
            }
            if ((typeof BBOXForm != "undefined") && (typeof BBOXForm.display === "function")) {
                BBOXForm.display(d)
            }
            if (!this._preview) {
                a.log("bbox.squirtMarkup: attaching submit handler");
                e(".BBFormSubmitbutton").click(function (f) {
                    a.log("mongo form submit!");
                    f.preventDefault();
                    if ((typeof BBOXForm != "undefined") && (typeof BBOXForm.presubmit === "function")) {
                        var g = false;
                        g = !BBOXForm.presubmit();
                        if (g) {
                            return
                        }
                    }
                    e("#bbox-root").block({
                        message: "Processing",
                        css: {
                            padding: "10px",
                            border: "none",
                            fontSize: "16px",
                            backgroundColor: "#000",
                            borderRadius: "10px",
                            "-webkit-border-radius": "10px",
                            "-moz-border-radius": "10px",
                            opacity: 0.5,
                            color: "#fff"
                        },
                        overlayCSS: {
                            backgroundColor: "#fff",
                            opacity: 0.5
                        }
                    });
                    e(this).attr("disabled", true);
                    bbox.handleSubmit(this)
                })
            } else {
                e(".BBFormSubmitbutton").click(function (f) {
                    f.preventDefault()
                })
            }
        },
        handleSubmit: function (c) {
            var b = this;
            b.log("enter handleSubmit()");
            var d = null;
            var a = bb$(c).attr("name") + "=" + bb$(c).val();
            d = bb$("#mongo-form").serialize() + "&" + a;
            b.log("bbox.postForm: data:" + d);
            bbox.server.handleSubmit(d);
            b.log("exit handleSubmit()")
        },
        server: new easyXDM.Rpc({
            remote: function () {
                if (typeof bboxOverrides !== "undefined" && bboxOverrides.MongoServerHTML) {
                    return _MongoServerUrl + bboxOverrides.MongoServerHTML
                } else {
                    return _MongoServerUrl + "MongoServer.html"
                }
            }()
        }, {
            local: {
                pingBack: function (a) {
                    alert("Ping back recieved " + a)
                },
                getMarkupCallback: function (a) {
                    bbox.render(a)
                },
                getMarkupCallbackError: function (a) {
                    bbox.notifyError(a)
                },
                handleSubmitCallback: function (a) {
                    if (typeof bboxOverrides !== "undefined" && typeof bboxOverrides.handleSubmitCallbackOverride === "function") {
                        bboxOverrides.handleSubmitCallbackOverride(a)
                    } else {
                        bbox.log("bbox.postFormCallback()");
                        bb$("#bbox-root").unblock();
                        bbox.squirtMarkup(a, true)
                    }
                },
                postFormCallbackError: function (a) {
                    bbox.log("bbox.postFormCallbackError()");
                    bb$("#bbox-root").unblock();
                    bbox.notifyError(a)
                }
            },
            remote: {
                ping: {},
                handleSubmit: {},
                getMarkup: {},
                enableLogging: {}
            }
        })
    }
}
if (window.bboxInit && !window.bboxInit.hasRun) {
    window.bboxInit.hasRun = true;
    window.bb$ = jQuery.noConflict();
    bboxInit()
};
(function (a) {
    a.formatCurrency.regions["en-US"] = {
        symbol: "$",
        positiveFormat: "%s%n",
        negativeFormat: "(%s%n)",
        decimalSymbol: ".",
        digitGroupSymbol: ",",
        groupDigits: true
    }
})(jQuery);
(function (a) {
    a.formatCurrency.regions["en-GB"] = {
        symbol: "£",
        positiveFormat: "%s%n",
        negativeFormat: "-%s%n",
        decimalSymbol: ".",
        digitGroupSymbol: ",",
        groupDigits: true
    }
})(jQuery);
(function (a) {
    a.formatCurrency.regions["de-DE"] = {
        symbol: "€",
        positiveFormat: "%n %s",
        negativeFormat: "-%n %s",
        decimalSymbol: ",",
        digitGroupSymbol: ".",
        groupDigits: true
    }
})(jQuery);
(function (a) {
    a.formatCurrency.regions["en-CA"] = {
        symbol: "$",
        positiveFormat: "%s%n",
        negativeFormat: "-%s%n",
        decimalSymbol: ".",
        digitGroupSymbol: ",",
        groupDigits: true
    }
})(jQuery);
(function (a) {
    a.formatCurrency.regions["en-AU"] = {
        symbol: "$",
        positiveFormat: "%s%n",
        negativeFormat: "-%s%n",
        decimalSymbol: ".",
        digitGroupSymbol: ",",
        groupDigits: true
    }
})(jQuery);
(function (a) {
    a.formatCurrency.regions["en-NZ"] = {
        symbol: "$",
        positiveFormat: "%s%n",
        negativeFormat: "-%s%n",
        decimalSymbol: ".",
        digitGroupSymbol: ",",
        groupDigits: true
    }
})(jQuery);
