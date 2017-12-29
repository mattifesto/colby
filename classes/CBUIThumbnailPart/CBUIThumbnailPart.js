"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIThumbnailPart */

var CBUIThumbnailPart = {

    /**
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          src: string
     *      }
     */
    create: function () {
        var src;
        var element = document.createElement("div");
        element.className = "CBUIThumbnailPart";

        var img = document.createElement("img");

        element.appendChild(img);

        return {
            get element() {
                return element;
            },
            get src() {
                return src;
            },
            set src(value) {
                if (typeof value === "string" && value.trim() !== "") {
                    src = value;
                } else {
                    src = undefined;
                }

                if (src === undefined) {
                    element.classList.remove("show");
                    img.src = "";
                } else {
                    element.classList.add("show");
                    img.src = src;
                }
            },
        };
    },
};
