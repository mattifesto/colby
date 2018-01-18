"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIActionPart */

var CBUIActionPart = {

    /**
     * @return object
     *
     *      {
     *          disabled: bool (get, set)
     *
     *              A CBUIActionPart does not have a function so the disabled
     *              property, if set to true, just gives the part a disabled
     *              appearance.
     *
     *          element: Element (readonly)
     *          title: string (get, set)
     *      }
     */
    create: function () {
        let disabled = false;
        let element = document.createElement("div");
        element.className = "CBUIActionPart";
        let titleElement = document.createElement("div");
        titleElement.className = "title";

        element.appendChild(titleElement);

        return {
            get disabled() {
                return disabled;
            },
            set disabled(value) {
                disabled = !!value;

                if (disabled) {
                    element.classList.add("disabled");
                } else {
                    element.classList.remove("disabled");
                }
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
