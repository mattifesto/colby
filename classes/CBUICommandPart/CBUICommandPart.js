"use strict";
/* jshint strict: global */
/* exported CBUICommandPart */

var CBUICommandPart = {

    /**
     * @return object
     *
     *      {
     *          callback: function
     *          disabled: bool
     *          element: Element (readonly)
     *          title: string
     *      }
     */
    create: function () {
        var callback;
        var disabled = false;
        var element = document.createElement("div");
        element.className = "CBUICommandPart";

        element.addEventListener("click", function () {
            if (callback && !disabled) {
                callback();
            }
        });

        var o = {
            get callback() {
                return callback;
            },
            set callback(value) {
                if (typeof value === "function") {
                    callback = value;
                } else {
                    callback = undefined;
                }
            },
            get disabled() {
                return disabled;
            },
            set disabled(value) {
                if (value) {
                    element.classList.add("disabled");
                    disabled = true;
                } else {
                    element.classList.remove("disabled");
                    disabled = false;
                }
            },
            get element() {
                return element;
            },
            get title() {
                return element.textContent;
            },
            set title(value) {
                element.textContent = value;
            },
        };

        element.CBUICommandPart = o;

        return o;
    }
};
