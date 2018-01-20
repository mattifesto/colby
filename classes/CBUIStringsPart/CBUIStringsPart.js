"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIStringsPart */

var CBUIStringsPart = {

    /**
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          string1: string (get, set)
     *          string2: string (get, set)
     *      }
     */
    create: function () {
        let element = document.createElement("div");
        element.className = "CBUIStringsPart";

        let string1Element = document.createElement("div");
        string1Element.className = "string1";

        let string2Element = document.createElement("div");
        string2Element.className = "string2";

        element.appendChild(string1Element);
        element.appendChild(string2Element);

        return {
            get element() {
                return element;
            },
            get string1() {
                return string1Element.textContent;
            },
            set string1(value) {
                string1Element.textContent = value;
            },
            get string2() {
                return string2Element.textContent;
            },
            set string2(value) {
                string2Element.textContent = value;
            },
        };
    },
};
