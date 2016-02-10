"use strict";

var CBPagesAdministrationView = {

    /**
     * @return {Element}
     */
    createElement : function() {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPagesAdministrationView";
        var pageListContainer = document.createElement("div");

        var parameters = {};
        var pages = [];

        var fetchPagesCallback = CBPagesAdministrationView.fetchPages.bind(undefined, {
            element : pageListContainer,
            pages : pages,
            parameters : parameters,
        });

        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback : fetchPagesCallback,
            rootItem : {
                element : element,
                title : "Pages",
            },
        });

        section = CBUI.createSection();

        /* published */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Published",
            navigateCallback : navigationView.navigateToSpecCallback,
            navigateToItemCallback : navigationView.navigateToItemCallback,
            propertyName : "published",
            spec : parameters,
            specChangedCallback : fetchPagesCallback,
            options : [
                { title : "All", value : undefined },
                { title : "Published", value : true },
                { title : "Unpublished", value : false },
            ],
        }).element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(pageListContainer);

        fetchPagesCallback();

        return navigationView.element;
    },

    /**
     * @param Element args.element
     * @param object args.parameters
     *
     * @return undefined
     */
    fetchPages : function(args) {
        var xhr = new XMLHttpRequest();
        xhr.onload = CBPagesAdministrationView.fetchPagesDidLoad.bind(undefined, {
            element: args.element,
            xhr : xhr
        });
        xhr.onerror = CBPagesAdministrationView.fetchPagesDidError.bind(undefined, {
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

    main.appendChild(CBPagesAdministrationView.createElement());
});
