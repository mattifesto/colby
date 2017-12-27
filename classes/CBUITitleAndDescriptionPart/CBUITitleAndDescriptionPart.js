"use strict";
/* jshint strict: global */
/* exported CBUITitleAndDescriptionPart */

var CBUITitleAndDescriptionPart = {

    /**
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          title: string
     *      }
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBUITitleAndDescriptionPart";

        var titleElement = document.createElement("div");
        titleElement.className = "title";

        element.appendChild(titleElement);

        return {
            get element() {
                return element;
            },
            get title() {
                return titleElement.textContent;
            },
            set title(value) {
                titleElement.textContent = value;
            },
        };
    },
};
