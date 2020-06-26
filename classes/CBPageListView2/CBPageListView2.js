"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageListView2 */
/* global
    CBArtworkElement,
    CBConvert,
    CBImage,
    CBUI,
    CBUIButton,
    CBUIPanel,
    Colby,

    CBPageListView2_currentUserIsDeveloper,
*/



var CBPageListView2 = {

    /**
     * @param object state
     *
     *      {
     *          buttonContainerElement: Element
     *          element: Element
     *          hasFetchedAllElements: bool
     *          maximumPageCount: int|undefined
     *          pagesContainerElement: Element
     *      }
     *
     * @return undefined
     */
    fetchPages: function (state) {
        let classNameForKind = state.element.dataset.classNameForKind;

        let maximumPageCount = CBConvert.valueAsInt(
            state.element.dataset.maximumPageCount
        ) || Number.MAX_SAFE_INTEGER;

        maximumPageCount = Math.max(maximumPageCount, 1);

        let pagesContainerElement;

        if (state.pagesContainerElement === undefined) {
            pagesContainerElement = CBUI.createElement(
                "CBPageListView2_pagesContainer"
            );

            state.element.appendChild(
                pagesContainerElement
            );

            state.pagesContainerElement = pagesContainerElement;
        } else {
            pagesContainerElement = state.pagesContainerElement;
        }

        Colby.callAjaxFunction(
            "CBPageListView2",
            "fetchPages",
            {
                classNameForKind,
                maximumPageCount,
                publishedBeforeTimestamp: state.published,
            }
        ).then(
            function (result) {
                displayFetchedPages(
                    result
                );
            }
        ).catch(
            function (error) {
                if (CBPageListView2_currentUserIsDeveloper) {
                    CBUIPanel.displayAndReportError(error);
                } else {
                    CBUIPanel.displayText(
                        "The site is currently unable to " +
                        "fetch the list of pages."
                    );
                }
            }
        );

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * CBPageListView2.fetchPages() closure
         *
         * @param object result
         *
         *      {
         *          pages: [object]
         *      }
         *
         * @return undefined
         */
        function displayFetchedPages(
            result
        ) {
            var count = 0;

            result.pages.forEach(
                function (pageSummary) {
                    if (count >= maximumPageCount) {
                        return;
                    }

                    let element = pagesSummaryToElement(
                        pageSummary
                    );

                    state.pagesContainerElement.appendChild(
                        element
                    );

                    state.published = pageSummary.publicationTimeStamp;

                    count += 1;
                }
            );

            Colby.updateTimes();

            if (
                maximumPageCount > 10 &&
                state.buttonContainerElement === undefined
            ) {
                state.buttonContainerElement = document.createElement("div");
                state.buttonContainerElement.className = "buttonContainer";

                let button = CBUIButton.create();
                button.textContent = "View More";

                button.addClickListener(
                    CBPageListView2.fetchPages.bind(undefined, state)
                );

                state.buttonContainerElement.appendChild(button.element);
                state.element.appendChild(state.buttonContainerElement);
            }

            if (result.pages.length === 0 && !state.hasFetchedAllElements) {
                state.hasFetchedAllElements = true;

                if (state.buttonContainerElement !== undefined) {
                    state.buttonContainerElement.classList.add("hidden");
                }
            }
        }
        /* display() */



        /**
         * @param object pageSummary
         *
         * @return Element
         */
        function pagesSummaryToElement(
            pageSummary
        ) {
            var element = document.createElement("article");
            var anchorElement = document.createElement("a");
            anchorElement.href = "/" + pageSummary.URI + "/";

            anchorElement.classList.add("content");

            /* image */

            var imageElement = document.createElement("div");
            imageElement.className = "image";

            let URL = CBImage.toURL(pageSummary.image, "rw1280");

            if (URL === "") {
                URL = pageSummary.thumbnailURL;
            }

            let artworkElement = CBArtworkElement.create(
                {
                    URL: URL,
                    aspectRatioWidth: 16,
                    aspectRatioHeight: 9,
                }
            );

            imageElement.appendChild(artworkElement);
            anchorElement.appendChild(imageElement);

            /* text */

            {
                let textElement = document.createElement("div");
                textElement.className = "text";

                let titleElement = document.createElement("h2");
                titleElement.className = "title";
                titleElement.textContent = pageSummary.title;

                textElement.appendChild(titleElement);

                let descriptionElement = CBUI.createElement(
                    "CBPageListView2_pageDescription description"
                );

                descriptionElement.textContent = pageSummary.description;

                textElement.appendChild(descriptionElement);

                var dateElement = CBUI.createElement(
                    "CBPageListView2_pagePublicationDate published"
                );

                dateElement.appendChild(
                    Colby.unixTimestampToElement(
                        pageSummary.publicationTimeStamp
                    )
                );

                textElement.appendChild(dateElement);

                let readModeElement = CBUI.createElement(
                    "CBPageListView2_pageReadMore readmore"
                );

                readModeElement.textContent = "read more >";

                textElement.appendChild(readModeElement);
                anchorElement.appendChild(textElement);
            }

            element.appendChild(anchorElement);

            return element;
        }
        /* pagesSummaryToElement() */

    },
    /* fetchPages() */

};
/* CBPageListView2 */


(function () {

    Colby.afterDOMContentLoaded(
        function () {
            afterDOMContentLoaded();
        }
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName("CBPageListView2");

        for (let index = 0; index < elements.length; index++) {
            let element = elements[index];

            initializeElement(element);
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @param Element element
     *
     *      The CBPageListView2 element.
     *
     * @return undefined
     */
    function initializeElement(
        element
    ) {
        CBPageListView2.fetchPages(
            {
                element: element,
            }
        );
    }
    /* initializeElement() */

})();
