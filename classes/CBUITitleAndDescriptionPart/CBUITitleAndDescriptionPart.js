"use strict";
/* jshint strict: global */
/* exported CBUITitleAndDescriptionPart */

/**
 * @deprecated use CBUIStringsPart
 */
var CBUITitleAndDescriptionPart = {

    /**
     * @return object
     *
     *      {
     *          description: string
     *          element: Element (readonly)
     *          title: string
     *      }
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBUITitleAndDescriptionPart";

        var titleElement = document.createElement("div");
        titleElement.className = "title";

        var descriptionElement = document.createElement("div");
        descriptionElement.className = "description";

        element.appendChild(titleElement);
        element.appendChild(descriptionElement);

        return {
            get description() {
                return descriptionElement.textContent;
            },
            set description(value) {
                descriptionElement.textContent = value;
            },
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
