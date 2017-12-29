"use strict";
/* jshint strict: global */
/* exported CBUINavigationArrowPart */

var CBUINavigationArrowPart = {

    /**
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *      }
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBUINavigationArrowPart";

        var o = {

            /**
             * @return Element
             */
            get element() {
                return element;
            },
        };

        return o;
    },
};
