"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForPagesFind */
/* globals
    CBImage,
    CBUI,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUISectionItem4,
    CBUISelector,
    CBUIStringEditor,
    CBUIStringsPart,
    CBUIThumbnailPart,
    Colby,

    CBPageKindsOptions,
*/

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
        section.appendChild(CBUISelector.create({
            labelText : "Kind",
            navigateToItemCallback : navigationView.navigateToItemCallback,
            propertyName : "classNameForKind",
            spec : parameters,
            specChangedCallback : fetchPagesCallback,
            options : CBPageKindsOptions,
        }).element);

        /* published */
        section.appendChild(CBUISelector.create({
            labelText : "Published",
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

        /* sorting */
        section.appendChild(CBUISelector.create({
            labelText : "Sorting",
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

        Colby.fetchAjaxResponse("/api/?class=CBPages&function=fetchPageList", data)
            .then(onResolve)
            .catch(Colby.displayAndReportError)
            .then(onFinally, onFinally);

        function onResolve(response) {
            var pages = response.pages;
            var list = CBPageList.createElement(pages);

            args.element.textContent = null;
            args.element.appendChild(list);
        }

        function onFinally() {
            args.state.waiting = undefined;

            if (args.state.argsForNextRequest) {
                var argsForNextRequest = args.state.argsForNextRequest;
                args.state.argsForNextRequest = undefined;

                CBAdminPageForPagesFind.fetchPages(argsForNextRequest);
            }
        }
    },
};
/* CBAdminPageForPagesFind */


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
        element.className = "CBAdminPageForPagesFind_pageListView";

        var section = CBUI.createSection();

        pages.forEach(function (page) {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                let URI = `/admin/?c=CBModelEditor&ID=${page.ID}`;
                window.location = URI;
            };

            let thumbnailPart = CBUIThumbnailPart.create();

            if (page.keyValueData.image) {
                thumbnailPart.src = CBImage.toURL(
                    page.keyValueData.image,
                    "rs200clc200"
                );
            } else if (page.keyValueData.thumbnailURL) {
                thumbnailPart.src = page.keyValueData.thumbnailURL;
            }

            sectionItem.appendPart(thumbnailPart);

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = page.keyValueData.title;
            stringsPart.string2 = page.keyValueData.description;

            stringsPart.element.classList.add("titledescription");

            sectionItem.appendPart(stringsPart);
            sectionItem.appendPart(CBUINavigationArrowPart.create());
            section.appendChild(sectionItem.element);
        });

        element.appendChild(section);

        return element;
    },
};
/* CBPageList (CBAdminPageForPagesFind) */


Colby.afterDOMContentLoaded(
    function() {
        var elements = document.getElementsByClassName(
            "CBAdminPageForPagesFind"
        );

        if (elements.length > 0) {
            let element = elements.item(0);

            element.appendChild(
                CBAdminPageForPagesFind.createElement()
            );
        }
    }
);
