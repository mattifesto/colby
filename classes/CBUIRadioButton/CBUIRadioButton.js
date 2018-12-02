"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIRadioButton */

var CBUIRadioButton = {

    /**
     * @return object
     *
     *      {
     *          element: Element
     *
     *          addClickListener()
     *          appendPart()
     *          removeClickListener()
     *      }
     */
    create: function () {
        let clickListeners = [];
        let element = document.createElement("label");
        element.className = "CBUIRadioButton";
        let buttonElement = document.createElement("div");
        buttonElement.className = "CBUIRadioButton_button";

        buttonElement.addEventListener("click", handleClick);

        element.appendChild(buttonElement);

        return {
            addClickListener: addClickListener,
            appendPart: appendPart,
            removeClickListener: removeClickListener,

            /**
             * @return Element
             */
            get element() {
                return element;
            },

            /**
             * @return bool
             */
            get selected() {
                return element.classList.contains("CBUIRadioButton_selected");
            },

            /**
             * @param bool value
             */
            set selected(value) {
                if (value) {
                    element.classList.add("CBUIRadioButton_selected");
                } else {
                    element.classList.remove("CBUIRadioButton_selected");
                }
            },
        };

        /**
         * closure in create()
         *
         * @param function value
         *
         * @return undefined
         */
        function addClickListener(value) {
            if (typeof value !== "function") {
                throw new TypeError();
            }

            if (clickListeners.includes(value)) {
                return;
            }

            clickListeners.push(value);
        }

        /**
         * closure in create()
         *
         * @param object part
         *
         *      {
         *          element: Element
         *      }
         *
         * @return undefined
         */
        function appendPart(part) {
            buttonElement.appendChild(part.element);
        }

        /**
         * closure in create()
         *
         * @return undefined
         */
        function handleClick() {
            if (element.classList.contains("CBUIButton_disabled")) {
                return;
            }

            clickListeners.forEach(
                function (callback) {
                    callback.call();
                }
            );
        }

        /**
         * closure in create()
         *
         * @param function value
         *
         * @return undefined
         */
        function removeClickListener(value) {
            if (typeof value !== "function") {
                throw new TypeError();
            }

            clickListeners = clickListeners.filter(
                function (callback) {
                    return callback !== value;
                }
            );
        }
    },
};
