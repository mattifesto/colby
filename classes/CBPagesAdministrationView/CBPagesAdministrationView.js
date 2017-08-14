"use strict"; /* jshint strict: global */
/* globals
    CBPageKindsOptions,
    CBPageList,
    CBUI,
    CBUINavigationView,
    CBUISelector,
    CBUIStringEditor,
    Colby */

var CBPagesAdministrationView = {

    /**
     * @return Element
     */
    createElement: function() {
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
        element.appendChild(CBUI.createSectionHeader({ text : "Search Criteria" }));

        section = CBUI.createSection();

        /* classNameForKind */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Kind",
            navigateCallback : navigationView.navigateToSpecCallback,
            navigateToItemCallback : navigationView.navigateToItemCallback,
            propertyName : "classNameForKind",
            spec : parameters,
            specChangedCallback : fetchPagesCallback,
            options : CBPageKindsOptions,
        }).element);
        section.appendChild(item);

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
        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({ text : "Results" }));
        element.appendChild(pageListContainer);
        element.appendChild(CBUI.createHalfSpace());

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
    fetchPages: function (args) {
        if (args.state.waiting === true) {
            args.state.argsForNextRequest = args;
            return;
        }

        args.state.waiting = true;

        var data = new FormData();
        data.append("parametersAsJSON", JSON.stringify(args.parameters));

        var promise = Colby.fetchAjaxResponse("/api/?class=CBPages&function=fetchPageList", data)
            .then(onResolve, onReject)
            .then(onFinally, onFinally);

        Colby.retain(promise);

        function onResolve(response) {
            var pages = response.pages;
            var list = CBPageList.createElement(pages);

            args.element.textContent = null;
            args.element.appendChild(list);
        }

        function onReject(error) {
            Colby.report(error);
            Colby.displayError(error);
        }

        function onFinally() {
            args.state.waiting = undefined;

            if (args.state.argsForNextRequest) {
                var argsForNextRequest = args.state.argsForNextRequest;
                args.state.argsForNextRequest = undefined;

                CBPagesAdministrationView.fetchPages(argsForNextRequest);
            }

            Colby.release(promise);
        }
    },
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBPagesAdministrationView.createElement());
});
