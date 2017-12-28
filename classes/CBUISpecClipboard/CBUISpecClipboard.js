"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpecClipboard */

var CBUISpecClipboard = {

    /**
     * @return [object]
     */
    get specs() {
        var clipboardAsJSON = localStorage.getItem("CBUISpecClipboard");

        if (clipboardAsJSON === null) {
            return [];
        }

        return JSON.parse(clipboardAsJSON);
    },

    /**
     * @param [object] value
     */
    set specs(value) {
        if (!Array.isArray(value)) {
            value = [];
        }

        for (let i = 0; i < value.length; i++) {
            let spec = value[i];

            if (typeof spec !== "object") {
                value = [];
                break;
            }

            let className = spec.className;

            if (typeof className !== "string" || className.trim() === "") {
                value = [];
                break;
            }
        }

        localStorage.setItem("CBUISpecClipboard", JSON.stringify(value));
    },
};
