"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIPanel */
/* global
    CBMessageMarkup,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/

Colby.afterDOMContentLoaded(function () {
    let buttons;
    let isShowing = false;
    let message = "";

    /**
     * Stucture:
     *
     *      element
     *          surfaceElement
     *              messageElement
     *                  contentElement
     *              interfaceElement
     *                  sectionElement
     */

    let element = document.createElement("div");
    element.className = "CBUIPanel";
    let surfaceElement = document.createElement("div");
    surfaceElement.className = "surface";

    element.appendChild(surfaceElement);

    let messageElement = document.createElement("div");
    messageElement.className = "message";
    let contentElement = document.createElement("div");
    contentElement.className = "content";

    messageElement.appendChild(contentElement);
    surfaceElement.appendChild(messageElement);

    let interfaceElement = document.createElement("div");
    interfaceElement.className = "interface";
    let sectionElement = CBUI.createSection();

    interfaceElement.appendChild(sectionElement);
    surfaceElement.appendChild(interfaceElement);

    document.body.appendChild(element);

    let api = {

        get buttons() {
            return buttons;
        },

        /**
         * @param [object] descriptors
         *
         *      {
         *          title: string
         *          callback: function?
         *
         *              If no callback is specified, a callback will be used
         *              that resets (and hides) the panel.
         *      }
         */
        set buttons(value) {
            setButtons(value);
        },

        get isShowing() {
            return isShowing;
        },

        set isShowing(value) {
            setIsShowing(value);
        },

        get message() {
            return message;
        },

        set message(value) {
            setMessage(value);
        },

        reset: reset,
    };

    reset();

    Object.defineProperty(window, 'CBUIPanel', {
        value: api,
    });

    function createButtonSectionItem(descriptor) {
        let sectionItem = CBUISectionItem4.create();

        if (typeof descriptor.callback === "function") {
            sectionItem.callback = descriptor.callback;
        } else {
            sectionItem.callback = function () {
                reset();
            };
        }

        let stringsPart = CBUIStringsPart.create();
        stringsPart.string1 = descriptor.title;

        stringsPart.element.classList.add("action");

        sectionItem.appendPart(stringsPart);

        return sectionItem;
    }

    function reset() {
        setIsShowing(false);
        setMessage("");
        setButtons([
            {
                title: "OK",
            }
        ]);
    }

    function setButtons(value) {
        buttons = value;
        sectionElement.textContent = "";

        if (Array.isArray(buttons) && buttons.length > 0) {
            buttons.forEach(function (button) {
                sectionElement.appendChild(
                    createButtonSectionItem(button).element
                );
            });

            interfaceElement.classList.remove("empty");
        } else {
            interfaceElement.classList.add("empty");
        }
    }

    function setIsShowing(value) {
        isShowing = !!value;

        if (isShowing) {
            element.classList.add("showing");
        } else {
            element.classList.remove("showing");
        }
    }

    function setMessage(value) {
        message = String(value);

        contentElement.innerHTML = CBMessageMarkup.markupToHTML(message);
    }
});
