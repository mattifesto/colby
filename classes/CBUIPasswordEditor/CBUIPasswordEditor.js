"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIPasswordEditor */
/* global
    CBUI,
    Colby,
*/



var CBUIPasswordEditor = {

    /**
     * @return object
     *
     *      {
     *          changed: function (get, set)
     *          element: Element (readonly)
     *          title: string (get, set)
     *          value: string (get, set)
     *      }
     */
    create: function () {
        let changed;

        let element = CBUI.createElement(
            "CBUIPasswordEditor CBUIStringEditor"
        );

        var ID = Colby.random160();
        var label = document.createElement("label");
        label.htmlFor = ID;
        var input = document.createElement("input");
        input.id = ID;
        input.type = "password";

        input.addEventListener(
            "input",
            function () {
                resize();

                if (typeof changed === "function") {
                    changed();
                }
            }
        );

        element.appendChild(label);
        element.appendChild(input);

        /**
         * @NOTE 2015_09_24
         *
         *      We have two timeouts because there is a bug in Safari where the
         *      height is not calculated correctly the first time. The first
         *      height is close which is why we keep both calls. Remove the
         *      second timeout once the bug has been fixed.
         */
        window.setTimeout(resize, 0);
        window.setTimeout(resize, 1000);

        let api = {
            get changed() {
                return changed;
            },
            set changed(value) {
                changed = value;
            },
            get element() {
                return element;
            },
            get title() {
                return label.textContent;
            },
            set title(value) {
                label.textContent = value;
            },
            get value() {
                return input.value;
            },
            set value(newValue) {
                input.value = newValue;
                resize();
            },
        };

        return api;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function resize() {
            input.style.height = "0";
            input.style.height = input.scrollHeight + "px";
        }
    },
    /* create() */

};
