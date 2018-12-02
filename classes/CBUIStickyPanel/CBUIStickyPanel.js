"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIPanel */

/**
 * This file creates the CBUIStickyPanel global variable which holds an object
 * exposing the API to work with the sticky panel.
 */

{
    let element = document.createElement("div");
    element.className = "CBUIStickyPanel";

    document.body.appendChild(element);

    let containerElement = document.createElement("div");
    containerElement.className = "CBUIStickyPanel_container";

    element.appendChild(containerElement);

    let placeholderElement = document.createElement("div");
    placeholderElement.className = "CBUIStickyPanel_placeholder";

    document.body.appendChild(placeholderElement);

    let api = {

        /**
         * @return bool
         */
        get isShowing() {
            return element.classList.contains("CBUIStickyPanel_showing");
        },

        /**
         * @param bool value
         */
        set isShowing(value) {
            if (!!value) {
                element.classList.add("CBUIStickyPanel_showing");
                placeholderElement.classList.add("CBUIStickyPanel_showing");
            } else {
                element.classList.remove("CBUIStickyPanel_showing");
                placeholderElement.classList.remove("CBUIStickyPanel_showing");
            }
        },

        /**
         * @return Element
         */
        get containerElement() {
            return containerElement;
        },
    };

    Object.defineProperty(
        window,
        "CBUIStickyPanel",
        {
            value: api,
        }
    );
}
