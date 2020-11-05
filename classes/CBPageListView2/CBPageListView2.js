"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageListView2 */
/* global
    CBAjax,
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

        CBAjax.call(
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
            let element = CBUI.createElement(
                "CBPageListView2_page",
                "article"
            );

            /* anchorElement */

            let anchorElement = CBUI.createElement(
                /* the "content" class is deprecated */
                "CBPageListView2_pageAnchor content",
                "a"
            );

            element.appendChild(
                anchorElement
            );

            anchorElement.href = (
                "/" +
                pageSummary.URI +
                "/"
            );


            /* imageElement */

            let imageElement = CBUI.createElement(
                /* the "image" class is deprecated */
                "CBPageListView2_pageImage image"
            );

            anchorElement.appendChild(
                imageElement
            );

            let imageURL = CBImage.toURL(
                pageSummary.image,
                "rw1280"
            );

            if (imageURL === "") {
                imageURL = pageSummary.thumbnailURL;
            }


            /* artworkElement */

            let artworkElement = CBArtworkElement.create(
                {
                    URL: imageURL,
                    aspectRatioWidth: 16,
                    aspectRatioHeight: 9,
                }
            );

            imageElement.appendChild(
                artworkElement
            );


            /* text */

            {
                let textElement = document.createElement("div");
                textElement.className = "text";

                let titleElement = CBUI.createElement(
                    /* the "title" class is deprecated */
                    "CBPageListView2_pageTitle title",
                    "h2"
                );

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
