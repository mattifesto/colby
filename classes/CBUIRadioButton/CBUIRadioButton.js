"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIRadioButton */
/* global
    CBModel,
    CBUIStringsPart,
*/

var CBUIRadioButton = {

    /**
     * @param object args
     *
     *      {
     *          description: string
     *          mutator: object
     *
     *              {
     *                  addChangeListener: function
     *                  value: mixed (get, set)
     *              }
     *
     *          title: string
     *          value: mixed
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
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

        let stringsPart = CBUIStringsPart.create();
        stringsPart.string1 = CBModel.valueToString(args, "title");
        stringsPart.string2 = CBModel.valueToString(args, "description");

        stringsPart.element.classList.add("titledescription");

        buttonElement.appendChild(stringsPart.element);

        mutator.addChangeListener(changed);

        return {
            element: element,
        };

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
