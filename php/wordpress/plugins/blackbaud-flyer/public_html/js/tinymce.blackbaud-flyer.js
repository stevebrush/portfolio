/*! (c) Blackbaud, Inc. */
(function(settings, methods) {




    // Register the plugin.
    tinymce.create("tinymce.plugins." + settings.id, {
        init: function(editor, url) {
            settings.api.addButton.menu = methods.getMenuData(editor, settings);
            editor.addButton(settings.id, settings.api.addButton);
        },
        getInfo: function() {
            return settings.info;
        }
    });




    // Add it!
    tinymce.PluginManager.add(settings.id, tinymce.plugins[settings.id]);




}({
    id: "BBFlyerMCEButton",
    api: {
        addButton: {
            type: "menubutton",
            title: "Add a Flyer...",
            image: "../wp-content/plugins/blackbaud-flyer/public_html/img/dashicon-1.png"
        }
    },
    info: {
        longname: "Blackbaud Flyer TinyMCE Shortcode Plugin",
        author: "Blackbaud Interactive Services",
        authorurl: "http://www.blackbaud.com/",
        infourl: "https://www.blackbaud.com/",
        version: "1.0"
    }
}, {
    getMenuData: function(editor, args) {
        var menus = [];
        var i;
        var posts = window.BlackbaudTinyMCEData[args.id];
        var len = posts.length;
        for (i = 0; i < len; i++) {
            menus.push({
                text: posts[i].title,
                onclick: (function(e, shortcode) {
                    return function() {
                        e.insertContent(shortcode);
                    }
                }(editor, posts[i].shortcode))
            });
        }
        return menus;
    }
}));
