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

        var fetchPagesCallback = CBPagesAdministrationView.fetchPages.bind(undefined, {
            element : pageListContainer,
            parameters : parameters,
            state : {},
        });

        var navigationView = CBUINavigationView.create({
            defaultSpecChangedCallback : fetchPagesCallback,
            rootItem : {
                element : element,
                title : "Find Pages",
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
                { title : "Modified (most recent first)", value : undefined },
                { title : "Modified (most recent last)", value : "modifiedAscending" },
                { title : "Created (most recent first)", value : "createdDescending" },
                { title : "Created (most recent last)", value : "createdAscending" },
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
     * @param object args.state
     *
     * @return undefined
     */
    fetchPages : function (args) {
        if (args.state.waiting === true) {
            args.state.argsForNextRequest = args;
            return;
        }

        args.state.waiting = true;
        var data = new FormData();
        data.append("parametersAsJSON", JSON.stringify(args.parameters));

        var xhr = new XMLHttpRequest();
        xhr.onload = CBPagesAdministrationView.fetchPagesDidLoad.bind(undefined, {
            element : args.element,
            state : args.state,
            xhr : xhr
        });
        xhr.onerror = CBPagesAdministrationView.fetchPagesDidError.bind(undefined, {
            state : args.state,
        });

        xhr.open("POST", "/api/?class=CBPages&function=fetchPageList");
        xhr.send(data);
    },

    /**
     * @param object args.state
     *
     * @return undefined
     */
    fetchPagesDidError : function (args) {
        Colby.alert("An error occurred when attempting to fetch the list of pages.");

        args.state.waiting = undefined;

        if (args.state.argsForNextRequest) {
            var argsForNextRequest = args.state.argsForNextRequest;
            args.state.argsForNextRequest = undefined;

            CBPagesAdministrationView.fetchPages.call(undefined, argsForNextRequest);
        }
    },

    /**
     * @param {Element} element
     *
     * @return undefined
     */
    fetchPagesDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            var pages = response.pages;
            var list = CBPageList.createElement(pages);

            args.element.textContent = null;
            args.element.appendChild(list);
        } else {
            Colby.displayResponse(response);
        }

        args.state.waiting = undefined;

        if (args.state.argsForNextRequest) {
            var argsForNextRequest = args.state.argsForNextRequest;
            args.state.argsForNextRequest = undefined;

            CBPagesAdministrationView.fetchPages.call(undefined, argsForNextRequest);
        }
    },
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBPagesAdministrationView.createElement());
});
