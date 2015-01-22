bbiGetInstance().register({
	alias: "OnlineExpressForms",
	author: "Blackbaud Interactive Services"
}).action("OnReady", function (app, bbi, $) {
	$(document).on("olx-ready", function () {
		//console.log("Hello, from Online Express Forms.");
	});
}).build();
