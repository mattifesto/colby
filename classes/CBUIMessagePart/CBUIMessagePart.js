"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIMessagePart */
/* global
    CBMessageMarkup */

var CBUIMessagePart = {

    /**
     * @return object
     *
     *      {
     *          element: Element (readonly)
     *          message: string (get, set)
     *      }
     */
    create: function () {
        let message = '';
        let element = document.createElement("div");
        element.className = "CBUIMessagePart CBContentStyleSheet";

        return {
            get element() {
                return element;
            },
            get message() {
                return message;
            },
            set message(value) {
                message = String(value);
                element.innerHTML = CBMessageMarkup.markupToHTML(message);
            },
        };
    },
};
