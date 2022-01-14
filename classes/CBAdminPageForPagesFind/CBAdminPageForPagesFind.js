/* globals
    CBAjax,
    CBConvert,
    CBImage,
    CBUI,
    CBUINavigationView,
    CBUIPanel,
    CBUISelector,
    CBUIStringEditor,
    CBUIThumbnailPart,
    Colby,

    CBPageKindsOptions,
*/


(function () {
    "use strict";

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        var elements = document.getElementsByClassName(
            "CBAdminPageForPagesFind"
        );

        if (elements.length > 0) {
            let element = elements.item(0);

            {
                let navigationView = CBUINavigationView.create();

                element.appendChild(
                    navigationView.element
                );
            }

            CBUINavigationView.navigate(
                {
                    element: createRootPanelElement(),
                    title: "Find Pages",
                }
            );
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @param [object] pages
     *
     * @return Element
     */
    function
    createListElement(
        pages
    ) {
        let sectionContainerElement = CBUI.createElement(
            "CBAdminPageForPagesFind_pageListView CBUI_sectionContainer"
        );

        let sectionElement = CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(
            sectionElement
        );

        pages.forEach(
            function (
                page
            ) {
                let sectionItemElement = CBUI.createElement(
                    "CBUI_sectionItem"
                );

                sectionElement.appendChild(
                    sectionItemElement
                );

                sectionItemElement.addEventListener(
                    "click",
                    function () {
                        let URI = `/admin/?c=CBModelEditor&ID=${page.ID}`;
                        window.location = URI;
                    }
                );

                let thumbnailPart = CBUIThumbnailPart.create();

                if (
                    page.keyValueData.image
                ) {
                    thumbnailPart.src = CBImage.toURL(
                        page.keyValueData.image,
                        "rs200clc200"
                    );
                } else if (
                    page.keyValueData.thumbnailURL
                ) {
                    thumbnailPart.src = page.keyValueData.thumbnailURL;
                }

                sectionItemElement.appendChild(
                    thumbnailPart.element
                );

                let textContainerElement = CBUI.createElement(
                    "CBUI_container_topAndBottom CBUI_flexGrow"
                );

                sectionItemElement.appendChild(
                    textContainerElement
                );

                let textElement1 = CBUI.createElement(
                    "CBAdminPageForPagesFind_foundPagesItem_pageTitle"
                );

                textContainerElement.appendChild(
                    textElement1
                );

                textElement1.textContent = page.keyValueData.title;

                let textElement2 = CBUI.createElement(
                    "CBUI_textSize_small CBUI_textColor2 CBUI_ellipsis"
                );

                textContainerElement.appendChild(
                    textElement2
                );

                textElement2.textContent =  page.keyValueData.description;

                sectionItemElement.appendChild(
                    CBUI.createElement(
                        "CBUI_navigationArrow"
                    )
                );
            }
        );

        return sectionContainerElement;
    }
    /* createListElement() */



    /**
     * @return Element
     */
    function createRootPanelElement() {
        let rootPanelElement;

        {
            let elements = CBUI.createElementTree(
                "CBAdminPageForPagesFind_rootPanelElement",
                "CBUI_title1"
            );

            rootPanelElement = elements[0];

            let titleElement = elements[1];

            titleElement.textContent = "Search Criteria";
        }


        let pageListContainerElement = CBUI.createElement(
            "CBAdminPageForPagesFind_pageListContainer"
        );

        var parameters = {};

        var fetchPagesCallback = fetchPages.bind(
            undefined,
            {
                element: pageListContainerElement,
                parameters: parameters,
                state: {},
            }
        );


        let sectionElement;

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            rootPanelElement.appendChild(
                elements[0]
            );

            sectionElement = elements[1];
        }


        /* classNameForKind */
        sectionElement.appendChild(
            CBUISelector.create(
                {
                    labelText: "Kind",
                    propertyName: "classNameForKind",
                    spec: parameters,
                    specChangedCallback: fetchPagesCallback,
                    options: CBPageKindsOptions,
                }
            ).element
        );


        /* published */
        sectionElement.appendChild(
            CBUISelector.create(
                {
                    labelText: "Published",
                    propertyName: "published",
                    spec: parameters,
                    specChangedCallback: fetchPagesCallback,
                    options: [
                        {
                            title: "All",
                            value: undefined,
                        },
                        {
                            title: "Published",
                            value: true,
                        },
                        {
                            title: "Unpublished",
                            value: false,
                        },
                    ],
                }
            ).element
        );


        /* sorting */
        sectionElement.appendChild(
            CBUISelector.create(
                {
                    labelText: "Sorting",
                    propertyName: "sorting",
                    spec: parameters,
                    specChangedCallback: fetchPagesCallback,
                    options: [
                        {
                            title: "Modified (most recent first)",
                            value: undefined,
                        },
                        {
                            title: "Modified (most recent last)",
                            value: "modifiedAscending",
                        },
                        {
                            title: "Created (most recent first)",
                            value: "createdDescending",
                        },
                        {
                            title: "Created (most recent last)",
                            value: "createdAscending",
                        },
                    ],
                }
            ).element
        );


        /* search for */
        {
            let searchForEditor = CBUIStringEditor.create();

            searchForEditor.title = "Search For";

            searchForEditor.changed = function () {
                parameters.search = searchForEditor.value;
                fetchPagesCallback();
            };

            sectionElement.appendChild(
                searchForEditor.element
            );
        }
        /* search for */


        /* found pages title */
        {
            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            rootPanelElement.appendChild(
                titleElement
            );

            titleElement.textContent = "Found Pages";
        }
        /* found pages title */


        rootPanelElement.appendChild(
            pageListContainerElement
        );

        rootPanelElement.appendChild(
            CBUI.createHalfSpace()
        );

        fetchPagesCallback();

        return rootPanelElement;
    }
    /* createRootPanelElement() */



    /**
     * @param object args
     *
     *      {
     *          element: Element
     *          parameters: object
     *          state: object
     *      }
     *
     * @return Promise
     */
    async function
    fetchPages(
        args
    ) {
        try {
            if (
                args.state.waiting === true
            ) {
                args.state.argsForNextRequest = args;

                return;
            }

            args.state.waiting = true;

            let pages = await CBAjax.call(
                "CBAdminPageForPagesFind",
                "fetchPages",
                CBConvert.valueToObject(
                    args.parameters
                )
            );

            let listElement = createListElement(
                pages
            );

            args.element.textContent = null;

            args.element.appendChild(
                listElement
            );

        } catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        } finally {
            args.state.waiting = undefined;

            if (
                args.state.argsForNextRequest
            ) {
                let argsForNextRequest = args.state.argsForNextRequest;
                args.state.argsForNextRequest = undefined;

                fetchPages(
                    argsForNextRequest
                );
            }
        }
    }
    /* fetchPages() */

})();
