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
            result.pages.forEach(function (page) {
                var element = document.createElement("article");
                var artworkElement = CBArtworkElement.create({
                    image: page.image,
                    src: page.thumbnailURL,
                });
                var dateElement = document.createElement("div");
                dateElement.className = "published";
                dateElement.appendChild(Colby.unixTimestampToElement(page.publicationTimeStamp));
                var titleElement = document.createElement("a");
                titleElement.className = "title";
                titleElement.textContent = page.title;
                titleElement.href = "/" + page.URI + "/";
                var descriptionElement = document.createElement("div");
                descriptionElement.className = "description";
                descriptionElement.textContent = page.description;

                element.addEventListener("click", CBPageListView2.navigate.bind(undefined, "/" + page.URI + "/"));

                element.appendChild(dateElement);
                element.appendChild(artworkElement);
                element.appendChild(titleElement);
                element.appendChild(descriptionElement);

                state.element.insertBefore(element, state.moreButton);
                state.published = page.publicationTimeStamp;
            });

            Colby.updateTimes();

            if (state.moreButton === undefined) {
                state.moreButton = document.createElement("div");
                state.moreButton.className = "more";
                state.moreButton.textContent = "view more";
                state.moreButton.addEventListener("click", CBPageListView2.fetchPages.bind(undefined, state));

                state.element.appendChild(state.moreButton);
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
    var state;

    for (var i = 0; i < elements.length; i++) {
        state = {element:elements[i]};
        CBPageListView2.fetchPages(state);
    }
});
