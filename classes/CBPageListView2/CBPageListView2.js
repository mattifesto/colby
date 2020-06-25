"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageListView2 */
/* global
    CBArtworkElement,
    CBImage,
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
     *          renderStyleIsRecent: bool
     *      }
     *
     * @return undefined
     */
    fetchPages: function (state) {
        var classNameForKind = state.element.dataset.classNameForKind;

        Colby.callAjaxFunction(
            "CBPageListView2",
            "fetchPages",
            {
                classNameForKind: classNameForKind,
                publishedBeforeTimestamp: state.published,
            }
        ).then(
            display
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
        function display(
            result
        ) {
            var count = 0;

            result.pages.forEach(
                function (pageSummary) {
                    if (state.renderStyleIsRecent && count >= 2) {
                        return;
                    }

                    let element = pagesSummaryToElement(
                        pageSummary
                    );

                    state.element.insertBefore(
                        element,
                        state.buttonContainerElement
                    );

                    state.published = pageSummary.publicationTimeStamp;

                    count += 1;
                }
            );

            Colby.updateTimes();

            if (
                !state.renderStyleIsRecent &&
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

                let descriptionElement = document.createElement("div");
                descriptionElement.className = "description";
                descriptionElement.textContent = pageSummary.description;

                textElement.appendChild(descriptionElement);

                var dateElement = document.createElement("div");
                dateElement.className = "published";

                dateElement.appendChild(
                    Colby.unixTimestampToElement(
                        pageSummary.publicationTimeStamp
                    )
                );

                textElement.appendChild(dateElement);

                let readModeElement = document.createElement("div");
                readModeElement.className = "readmore";
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
                renderStyleIsRecent: element.classList.contains("recent"),
            }
        );
    }
    /* initializeElement() */

})();
