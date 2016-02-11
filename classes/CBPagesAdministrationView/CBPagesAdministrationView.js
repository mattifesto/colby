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

        element.appendChild(CBUI.createHalfSpace());

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

        /* sorting */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Sorting",
            navigateCallback : navigationView.navigateToSpecCallback,
            navigateToItemCallback : navigationView.navigateToItemCallback,
            propertyName : "sorting",
            spec : parameters,
            specChangedCallback : fetchPagesCallback,
            options : [
                { title : "Modified (Most recent first)", value : undefined },
                { title : "Modified (Most recent last)", value : "modifiedAscending" },
                { title : "Created (Most recent first)", value : "createdDescending" },
                { title : "Created (Most recent last)", value : "createdAscending" },
            ],
        }).element);
        section.appendChild(item);

        /* search */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Search",
            propertyName : "search",
            spec : parameters,
            specChangedCallback : fetchPagesCallback,
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
        var data = new FormData();
        data.append("parametersAsJSON", JSON.stringify(args.parameters));

        var xhr = new XMLHttpRequest();
        xhr.onload = CBPagesAdministrationView.fetchPagesDidLoad.bind(undefined, {
            element: args.element,
            xhr : xhr
        });
        xhr.onerror = CBPagesAdministrationView.fetchPagesDidError.bind(undefined, {
            xhr : xhr
        });

        xhr.open("POST", "/api/?class=CBPages&function=fetchPageList");
        xhr.send(data);
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
            var pages = response.pages;
            var list = CBPageList.createElement(pages);

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
