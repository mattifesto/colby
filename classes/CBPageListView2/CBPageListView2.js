"use strict";
/* jshint strict: global */
/* exported CBPageListView2 */
/* global
    CBArtworkElement,
    CBUI,
    Colby */

var CBPageListView2 = {

    /**
     * @param object state
     *
     *      {
     *          buttonContainerElement: Element
     *          element: Element
     *          renderStyleIsRecent: bool
     *      }
     *
     * @return undefined
     */
    fetchPages: function (state) {
        var classNameForKind = state.element.dataset.classnameforkind;

        var formData = new FormData();
        formData.append("classNameForKind", classNameForKind);

        if (state.published) {
            formData.append("publishBeforeTimestamp", state.published);
        }

        Colby.fetchAjaxResponse("/api/?class=CBPageListView2&function=fetchPages", formData)
             .then(display)
             .catch(Colby.report);

        function display(result) {
            var count = 0;

            result.pages.forEach(function (page) {
                if (state.renderStyleIsRecent && count >= 2) {
                    return;
                }

                var element = document.createElement("article");
                var anchorElement = document.createElement("a");
                anchorElement.href = "/" + page.URI + "/";

                /* header */
                var dateElement = document.createElement("div");
                dateElement.className = "published";
                dateElement.appendChild(Colby.unixTimestampToElement(page.publicationTimeStamp));

                /* image */
                var imageElement = document.createElement("div");
                imageElement.className = "image";
                var artworkElement = CBArtworkElement.create({
                    image: page.image,
                    src: page.thumbnailURL,
                });

                imageElement.appendChild(artworkElement);

                var titleElement = document.createElement("h2");
                titleElement.className = "title";
                titleElement.textContent = page.title;
                var descriptionElement = document.createElement("div");
                descriptionElement.className = "description";
                descriptionElement.textContent = page.description;
                var readModeElement = document.createElement("div");
                readModeElement.className = "readmore";
                readModeElement.textContent = "read more >";

                anchorElement.appendChild(dateElement);
                anchorElement.appendChild(imageElement);
                anchorElement.appendChild(titleElement);
                anchorElement.appendChild(descriptionElement);
                anchorElement.appendChild(readModeElement);
                element.appendChild(anchorElement);

                state.element.insertBefore(element, state.buttonContainerElement);
                state.published = page.publicationTimeStamp;

                count += 1;
            });

            Colby.updateTimes();

            if (!state.renderStyleIsRecent && state.buttonContainerElement === undefined) {
                state.buttonContainerElement = document.createElement("div");
                state.buttonContainerElement.className = "buttonContainer";
                var button = CBUI.createButton({
                    text: "View More",
                    callback: CBPageListView2.fetchPages.bind(undefined, state),
                });

                state.buttonContainerElement.appendChild(button.element);
                state.element.appendChild(state.buttonContainerElement);
            }

            if (result.pages.length == 0) {
                state.buttonContainerElement.classList.add("hidden");
            }
        }
    },

    /**
     * @param string URL
     *
     * @return undefined
     */
    navigate: function (URL) {
        location.href = URL;
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
