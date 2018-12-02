"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIRadioButton */

var CBUIRadioButton = {

    /**
     * @param object args
     *
     *      {
     *          mutator: object
     *
     *              {
     *                  addChangeListener: function
     *                  value: mixed (get, set)
     *              }
     *
     *          value: mixed
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *
     *          appendPart()
     *      }
     */
    create: function (args) {
        let mutator = args.mutator;
        let value = args.value;
        let element = document.createElement("label");
        element.className = "CBUIRadioButton";
        let buttonElement = document.createElement("div");
        buttonElement.className = "CBUIRadioButton_button";

        buttonElement.addEventListener("click", click);

        element.appendChild(buttonElement);

        mutator.addChangeListener(changed);

        return {
            appendPart: appendPart,

            /**
             * @return Element
             */
            get element() {
                return element;
            },
        };

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
         */
        function changed() {
            if (mutator.value === value) {
                element.classList.add("CBUIRadioButton_selected");
            } else {
                element.classList.remove("CBUIRadioButton_selected");
            }
        }

        /**
         * closure in create();
         */
        function click() {
            mutator.value = value;
        }
    },
};
