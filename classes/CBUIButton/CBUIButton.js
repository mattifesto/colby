"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIButton */

var CBUIButton = {

    /**
     * @return object
     *
     *      {
     *          disabled: bool (get, set)
     *          element: Element (get)
     *          textContent: string (get, set)
     *
     *          addClickListener()
     *          removeClickListener()
     *      }
     */
    create: function () {
        let clickListeners = [];
        let element = document.createElement("div");
        element.className = "CBUIButton";

        let buttonElement = document.createElement("div");
        buttonElement.className = "CBUIButton_button";

        buttonElement.addEventListener("click", handleClick);

        element.appendChild(buttonElement);

        let contentElement = document.createElement("div");
        contentElement.className = "CBUIButton_content";

        buttonElement.appendChild(contentElement);

        let api = {

            /**
             * @param function value
             *
             * @return undefined
             */
            addClickListener: addClickListener,

            /**
             * @param function value
             *
             * @return undefined
             */
            removeClickListener: removeClickListener,

            /**
             * @return bool
             */
            get disabled() {
                return element.classList.contains("CBUIButton_disabled");
            },

            /**
             * @param bool value
             */
            set disabled(value) {
                if (value) {
                    element.classList.add("CBUIButton_disabled");
                } else {
                    element.classList.remove("CBUIButton_disabled");
                }
            },

            /**
             * @return Element
             */
            get element() {
                return element;
            },

            /**
             * @return string
             */
            get textContent() {
                return contentElement.textContent;
            },

            /**
             * @param string value
             */
            set textContent(value) {
                contentElement.textContent = value;
            },
        };

        return api;

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
