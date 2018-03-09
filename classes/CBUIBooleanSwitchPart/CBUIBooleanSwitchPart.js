"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIBooleanSwitchPart */

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
        let element = document.createElement("div");
        element.className = "CBUIBooleanSwitchPart";
        let sliderElement = document.createElement("div");
        sliderElement.className = "slider";
        let buttonElement = document.createElement("div");
        buttonElement.className = "button";

        sliderElement.appendChild(buttonElement);
        element.appendChild(sliderElement);

        element.addEventListener("click", function () {
            api.value = !value;
        });

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
};
