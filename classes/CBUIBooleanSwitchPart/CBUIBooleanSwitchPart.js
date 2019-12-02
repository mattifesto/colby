"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIBooleanSwitchPart */
/* global
    CBUI,
*/



var CBUIBooleanSwitchPart = {

    /**
     * @return object
     *
     *      {
     *          changed: function (get, set)
     *          element: Element (readonly)
     *          value: bool (get, set)
     *      }
     */
    create: function () {
        let changed;
        let value = false;


        /* element */

        let element = CBUI.createElement(
            "CBUIBooleanSwitchPart CBUI_userSelectNone"
        );


        /* slider */

        let sliderElement = CBUI.createElement("slider");

        element.appendChild(sliderElement);


        /* button */

        let buttonElement = CBUI.createElement("button");

        sliderElement.appendChild(buttonElement);

        element.addEventListener(
            "click",
            function () {
                api.value = !value;
            }
        );

        let api = {
            get changed() {
                return changed;
            },
            set changed(value) {
                if (typeof value === "function") {
                    changed = value;
                } else {
                    changed = undefined;
                }
            },
            get element() {
                return element;
            },
            get value() {
                return value;
            },
            set value(newValue) {
                newValue = !!newValue;

                if (value == newValue) {
                    return;
                }

                value = newValue;

                if (value === true) {
                    element.classList.add("true");
                } else {
                    element.classList.remove("true");
                }

                if (typeof changed === "function") {
                    changed();
                }
            },
        };

        return api;
    },
    /* create() */

};
