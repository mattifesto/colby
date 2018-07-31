"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISection */

var CBUISection = {

    /**
     * @return object
     *
     *      {
     *          appendItem(sectionItem)
     *
     *          element: Element (readonly)
     *      }
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBUISection";

        return {
            appendItem: appendItem,

            get element() {
                return element;
            },
        };

        /**
         * @param object sectionItem
         *
         * @return undefined
         */
        function appendItem(sectionItem) {
            element.appendChild(sectionItem.element);
        }
    },
};
