"use strict"; /* jshint strict: global */
/* global
    Colby */

var CBImagesAdminPage = {

    /**
     * @return {Element}
     */
    createElement : function() {
        var element = document.createElement("div");
        element.className = "CBImagesAdminPage";

        CBImagesAdminPage.fetchImages({
            element : element
        });

        return element;
    },

    /**
     * @param string args.thumbnailURL
     *
     * @return Element
     */
    createThumbnailElement: function(args) {
        var element = document.createElement("div");
        element.className = "thumbnail";
        var img = document.createElement("img");
        img.src = args.thumbnailURL;

        element.appendChild(img);

        return element;
    },

    /**
     * @param {Element} args.element
     *
     * @return undefined
     */
    fetchImages : function(args) {
        args.element.textContent = "CBImagesAdminPage";

        var xhr = new XMLHttpRequest();
        xhr.onload = CBImagesAdminPage.fetchImagesDidLoad.bind(undefined, {
            element : args.element,
            xhr : xhr
        });
        xhr.onerror = CBImagesAdminPage.fetchImagesDidError.bind(undefined, {
            xhr : xhr
        });

        xhr.open("POST", "/api/?class=CBImagesAdminPage&function=fetchImages");
        xhr.send();
    },

    /**
     * @param {XMLHttpRequest} args.xhr
     *
     * @return undefined
     */
    fetchImagesDidError : function(args) {
        Colby.alert('The list of images failed to load.');
    },

    /**
     * @param {Element} args.element
     * @param {XMLHttpRequest} args.xhr
     *
     * @return undefined
     */
    fetchImagesDidLoad : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.element.textContent = null;

            for (var i = 0; i < response.images.length; i++) {
                args.element.appendChild(CBImagesAdminPage.createThumbnailElement(response.images[i]));
            }
        } else {
            Colby.displayResponse(response);
        }
    }
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBImagesAdminPage.createElement());
});
