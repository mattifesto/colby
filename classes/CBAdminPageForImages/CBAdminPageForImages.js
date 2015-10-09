"use strict";

var CBAdminPageForImages = {

    /**
     * @return {Element}
     */
    createElement : function() {
        var element = document.createElement("div");
        element.className = "CBAdminPageForImages";

        CBAdminPageForImages.fetchImages({
            element : element
        });

        return element;
    },

    /**
     * @param {Element} element
     *
     * @return undefined
     */
    fetchImages : function(args) {
        args.element.textContent = "CBAdminPageForImages";

        var xhr = new XMLHttpRequest();
        xhr.onload = CBAdminPageForImages.fetchImagesDidLoad.bind(undefined, {
            element : args.element,
            xhr : xhr
        });
        xhr.onerror = CBAdminPageForImages.fetchImagesDidError.bind(undefined, {
            xhr : xhr
        });

        xhr.open("POST", "/api/?class=CBAdminPageForImages&function=fetchImages");
        xhr.send();
    },

    fetchImagesDidError : function(args) {

    },

    fetchImagesDidLoad : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        args.element.textContent = null;

        for (var i = 0; i < response.images.length; i++) {
            args.element.appendChild(CBAdminImageThumbnailFactory.createElement(response.images[i]));
        }
    }
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBAdminPageForImages.createElement());
});
