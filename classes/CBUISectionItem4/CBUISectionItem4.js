"use strict";
/* jshint strict: global */
/* exported CBUISectionItem4 */

var CBUISectionItem4 = {

    /**
     * A CBUISectionItem4 can have an optional callback that will be called when
     * it is clicked. The section item should have no other interactive user
     * interface elements.
     *
     * CBUISectionItem4 elements are composed of parts that are arranged
     * horizontally. No part should have interactive UI elements because the
     * CBUISectionItem4 itself responds to the click event. Parts are added via
     * the appendPart() function on the returned object.
     *
     * @return object
     *
     *      {
     *          appendPart: function
     *          callback: (getter, setter)
     *          element: Element (readonly)
     *      }
     */
    create: function () {
        var callback;
        var element = document.createElement("div");
        element.className = "CBUISectionItem4";

        element.addEventListener("click", function() {
            if (typeof callback === "function") {
                callback.call();
            }
        });

        return {
            appendPart: function (part) {
                element.appendChild(part.element);
            },
            get callback() {
                return callback;
            },
            set callback(value) {
                callback = value;
            },
            get element() {
                return element;
            },
        };
    },
};
