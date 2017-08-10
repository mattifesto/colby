"use strict"; /* jshint strict: global */
/* global
    CBArtworkElement,
    Colby */

var CBPageListView2 = {

    /**
     * @param Element state.element
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
                var artworkElement = CBArtworkElement.create({
                    image: page.image,
                    src: page.thumbnailURL,
                });

                /* footer */
                var footerElement = document.createElement("div");
                footerElement.className = "footer";
                var contentElement = document.createElement("div");
                contentElement.className = "content";
                var titleElement = document.createElement("h2");
                titleElement.className = "title";
                titleElement.textContent = page.title;
                var descriptionElement = document.createElement("div");
                descriptionElement.className = "description";
                descriptionElement.textContent = page.description;
                var arrowElement = document.createElement("div");
                arrowElement.className = "arrow";
                arrowElement.textContent = ">";
                contentElement.appendChild(titleElement);
                contentElement.appendChild(descriptionElement);
                footerElement.appendChild(contentElement);
                footerElement.appendChild(arrowElement);

                anchorElement.appendChild(dateElement);
                anchorElement.appendChild(artworkElement);
                anchorElement.appendChild(footerElement);
                element.appendChild(anchorElement);

                state.element.insertBefore(element, state.buttonContainerElement);
                state.published = page.publicationTimeStamp;

                count += 1;
            });

            Colby.updateTimes();

            if (!state.renderStyleIsRecent && state.buttonContainerElement === undefined) {
                state.buttonContainerElement = document.createElement("div");
                state.buttonContainerElement.className = "buttonContainer";
                var buttonElement = document.createElement("div");
                buttonElement.className = "button";
                buttonElement.textContent = "view more";
                buttonElement.addEventListener("click", CBPageListView2.fetchPages.bind(undefined, state));
                state.buttonContainerElement.appendChild(buttonElement);

                state.element.appendChild(state.buttonContainerElement);
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

document.addEventListener("DOMContentLoaded", function () {
    var elements = document.getElementsByClassName("CBPageListView2");

    for (var i = 0; i < elements.length; i++) {
        CBPageListView2.fetchPages({
            element: elements[i],
            renderStyleIsRecent: elements[i].classList.contains("recent"),
        });
    }
});
