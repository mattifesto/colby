"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageListView2 */
/* global
    CBArtworkElement,
    CBImage,
    CBUIButton,
    Colby,
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
        var classNameForKind = state.element.dataset.classnameforkind;

        Colby.callAjaxFunction(
            "CBPageListView2",
            "fetchPages",
            {
                classNameForKind: classNameForKind,
                publishedBeforeTimestamp: state.published,
            }
        ).then(display).catch(Colby.report);

        return;

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
        function display(result) {
            var count = 0;

            result.pages.forEach(function (page) {
                if (state.renderStyleIsRecent && count >= 2) {
                    return;
                }

                var element = document.createElement("article");
                var anchorElement = document.createElement("a");
                anchorElement.href = "/" + page.URI + "/";

                anchorElement.classList.add("content");

                /* image */

                var imageElement = document.createElement("div");
                imageElement.className = "image";

                let URL = CBImage.toURL(page.image, "rw1280");

                if (URL === "") {
                    URL = page.thumbnailURL;
                }

                let artworkElement = CBArtworkElement.create({
                    URL: URL,
                    aspectRatioWidth: 16,
                    aspectRatioHeight: 9,
                });

                imageElement.appendChild(artworkElement);
                anchorElement.appendChild(imageElement);

                /* text */

                {
                    let textElement = document.createElement("div");
                    textElement.className = "text";

                    let titleElement = document.createElement("h2");
                    titleElement.className = "title";
                    titleElement.textContent = page.title;

                    textElement.appendChild(titleElement);

                    let descriptionElement = document.createElement("div");
                    descriptionElement.className = "description";
                    descriptionElement.textContent = page.description;

                    textElement.appendChild(descriptionElement);

                    var dateElement = document.createElement("div");
                    dateElement.className = "published";
                    dateElement.appendChild(Colby.unixTimestampToElement(page.publicationTimeStamp));

                    textElement.appendChild(dateElement);

                    let readModeElement = document.createElement("div");
                    readModeElement.className = "readmore";
                    readModeElement.textContent = "read more >";

                    textElement.appendChild(readModeElement);
                    anchorElement.appendChild(textElement);
                }

                element.appendChild(anchorElement);

                state.element.insertBefore(element, state.buttonContainerElement);
                state.published = page.publicationTimeStamp;

                count += 1;
            });

            Colby.updateTimes();

            if (!state.renderStyleIsRecent && state.buttonContainerElement === undefined) {
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

                state.buttonContainerElement.classList.add("hidden");
            }
        }
    },
};

Colby.afterDOMContentLoaded(function () {
    var elements = document.getElementsByClassName("CBPageListView2");

    for (var i = 0; i < elements.length; i++) {
        CBPageListView2.fetchPages({
            element: elements[i],
            renderStyleIsRecent: elements[i].classList.contains("recent"),
        });
    }
});
