"use strict";

var CBUnpublishedPagesAdmin = {

    /**
     * @return {Element}
     */
    createElement : function() {
        var element = document.createElement("div");
        element.className = "CBUnpublishedPagesAdmin";

        CBUnpublishedPagesAdmin.fetchPages({
            element : element
        });

        return element;
    },

    /**
     * @param {Element} element
     *
     * @return undefined
     */
    fetchPages : function(args) {
        var xhr = new XMLHttpRequest();
        xhr.onload = CBUnpublishedPagesAdmin.fetchPagesDidLoad.bind(undefined, {
            element: args.element,
            xhr : xhr
        });
        xhr.onerror = CBUnpublishedPagesAdmin.fetchPagesDidError.bind(undefined, {
            xhr : xhr
        });

        xhr.open("POST", "/api/?class=CBViewPage&function=fetchUnpublishedPagesList");
        xhr.send();
    },

    /**
     * @return undefined
     */
    fetchPagesDidError : function(args) {
        alert('An error occurred when attempting to fetch the list of unpublished pages.');
    },

    /**
     * @param {Element} element
     *
     * @return undefined
     */
    fetchPagesDidLoad : function(args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var pages = response.pages.sort(function(left, right) {
                if (left.created > right.created) {
                    return -1;
                } else if (left.created < right.created) {
                    return 1;
                } else {
                    return 0;
                }
            });
            var list = CBPageList.createElement({
                pages : pages
            });

            args.element.textContent = null;
            args.element.appendChild(list);
        } else {
            Colby.displayResponse(response);
        }
    },
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBUnpublishedPagesAdmin.createElement());
});
