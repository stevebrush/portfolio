(function ($) {

	var _id = "OLXFormsMCEButton";

	var methods = {
		getMenuData: function (editor) {

			var menus = [];
			var i;
			var forms = window.OnlineExpressFormsData.forms;
			var len = forms.length;

			for (i = 0; i < len; i++) {
				menus.push({
					text: forms[i].title,
					onclick: (function (e,shortcode) {
						return function () {
							e.insertContent(shortcode);
						}
					}(editor, forms[i].shortcode))
				});
			}

			return menus;

		}
	};

	// Create the button.
	tinymce.create("tinymce.plugins." + _id, {
		init: function (editor, url) {
			editor.addButton(_id, {
				type: "menubutton",
				title: "Add an Online Express Form...",
				image: "../wp-content/plugins/blackbaud-olx/public_html/img/dashicon-1.png",
				menu: methods.getMenuData(editor)
			});
		},
		getInfo: function () {
			return {
				longname: "Online Express Forms TinyMCE Shortcode Plugin",
				author: "Blackbaud Interactive Services",
				authorurl: "http://www.blackbaud.com/",
				infourl: "https://www.blackbaud.com/online-marketing/online-express",
				version: "1.0"
			};
		}
	});

	// Register the plugin.
	tinymce.PluginManager.add(_id, tinymce.plugins[_id]);

}(jQuery));
