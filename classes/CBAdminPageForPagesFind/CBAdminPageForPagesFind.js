"use strict"; /* jshint strict: global */
/* globals
    CBPageKindsOptions,
    CBUI,
    CBUINavigationView,
    CBUISelector,
    CBUIStringEditor,
    Colby */

var CBAdminPageForPagesFind = {

    /**
     * @return Element
     */
    createElement: function() {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBAdminPageForPagesFind";
        var pageListContainer = document.createElement("div");

        var parameters = {};

        var fetchPagesCallback = CBAdminPageForPagesFind.fetchPages.bind(undefined, {
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

                CBAdminPageForPagesFind.fetchPages(argsForNextRequest);
            }

            Colby.release(promise);
        }
    },
};

/**
 * Used to be separate file, but only used by the above code.
 */
var CBPageList = {

    /**
     * @param [object] pages
     *
     * @return Element
     */
    createElement: function(pages) {
        var element = document.createElement("div");
        element.className = "CBPageListView";

        var section = CBUI.createSection();

        pages.forEach(function (page) {
            section.appendChild(CBPageList.createPageSectionItem(page));
        });

        element.appendChild(section);

        return element;
    },

    /**
     * @param string page.className
     * @param hex160 page.ID
     * @param object page.keyValueData
     *
     * @return Element
     */
    createPageSectionItem: function (page) {
        var item = CBUI.createSectionItem2();

        /* description */
        var descriptionElement = document.createElement("div");
        descriptionElement.className = "title";
        descriptionElement.textContent = page.keyValueData.title;

        item.titleElement.appendChild(descriptionElement);

        item.titleElement.addEventListener("click", CBPageList.handlePageElementEditWasClicked.bind(undefined, {
            className: page.className,
            ID: page.ID,
        }));

        /* copy */
        var copyCommandElement = document.createElement("div");
        copyCommandElement.className = "command CBPageListCopyButton";
        copyCommandElement.textContent = "Copy";

        item.commandsElement.appendChild(copyCommandElement);

        copyCommandElement.addEventListener("click", CBPageList.handlePageElementCopyWasClicked.bind(undefined, {
            IDToCopy: page.ID,
        }));

        /* trash */
        var trashCommandElement = document.createElement("div");
        trashCommandElement.className = "command CBPageListTrashButton";
        trashCommandElement.textContent = "Trash";

        item.commandsElement.appendChild(trashCommandElement);

        trashCommandElement.addEventListener("click", CBPageList.handlePageElementTrashWasClicked.bind(undefined, {
            element: item.element,
            ID: page.ID,
        }));

        return item.element;
    },

    /**
     * @param {hex160} IDToCopy
     *
     * @return undefined
     */
    handlePageElementCopyWasClicked : function(args) {
        location.href = '/admin/pages/edit/?data-store-id=' +
                        Colby.random160() +
                        '&id-to-copy=' +
                        args.IDToCopy;
    },

    /**
     * @param string args.className
     * @param hex160 args.ID
     *
     * @return undefined
     */
    handlePageElementEditWasClicked : function (args) {
        if (args.className === "CBViewPage") {
            location.href = "/admin/pages/edit/?data-store-id=" + args.ID;
        } else {
            location.href = "/admin/page/?class=CBAdminPageForEditingModels&ID=" + args.ID;
        }
    },

    /**
     * @param Element args.element
     * @param hex160 args.ID
     *
     * @return undefined
     */
    handlePageElementTrashWasClicked: function(args) {
        var promise = Colby.callAjaxFunction("CBPages", "moveToTrash", { ID: args.ID })
            .then(onFulfilled)
            .catch(Colby.displayError)
            .then(onFinally, onFinally);

        Colby.retain(promise);

        function onFulfilled(value) {
            args.element.parentElement.removeChild(args.element);
        }

        function onFinally() {
            Colby.release(promise);
        }
    },
};

document.addEventListener("DOMContentLoaded", function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBAdminPageForPagesFind.createElement());
});
