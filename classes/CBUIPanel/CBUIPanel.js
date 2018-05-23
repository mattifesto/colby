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

/**
 * This file will create a CBUIPanel global variable (a property on the window
 * object) that can be used to display panels with messages and custom buttons.
 *
 * This is the documentation for the API of the CBUIPanel object.
 *
 *      {
 *          buttons: [object] (get, set)
 *
 *              The default button of the panel is an OK button that hides the
 *              panel. Developers can add custom buttons for panels with more
 *              complex interaction.
 *
 *              {
 *                  title: string
 *                  callback: function?
 *
 *                      If no callback is specified, a callback will be used
 *                      that resets (and hides) the panel.
 *              }
 *
 *          isShowing: bool (get, set)
 *
 *              Shows and hides the panel.
 *
 *          message: string (get, set)
 *
 *              The content of the panel in message markup.
 *
 *          reset: function (get)
 *
 *              The reset function hides the panel, clears the message, and
 *              resets the buttons to a single OK button that will hide the
 *              panel.
 *      }
 */

Colby.afterDOMContentLoaded(function () {
    let buttons;
    let isShowing = false;
    let message = "";

    /**
     * Stucture:
     *
     *      viewportElement
     *          backgroundElement
     *              surfaceElement
     *                  messageElement
     *                      contentElement
     *                  interfaceElement
     *                      sectionElement
     */

    let viewportElement = document.createElement("div");
    viewportElement.className = "CBUIPanel";
    let backgroundElement = document.createElement("div");
    backgroundElement.className = "background";
    let surfaceElement = document.createElement("div");
    surfaceElement.className = "surface";

    backgroundElement.appendChild(surfaceElement);
    viewportElement.appendChild(backgroundElement);

    let messageElement = document.createElement("div");
    messageElement.className = "message";
    let contentElement = document.createElement("div");
    contentElement.className = "content CBContentStyleSheet";

    messageElement.appendChild(contentElement);
    surfaceElement.appendChild(messageElement);

    let interfaceElement = document.createElement("div");
    interfaceElement.className = "interface";
    let sectionElement = CBUI.createSection();

    interfaceElement.appendChild(sectionElement);
    surfaceElement.appendChild(interfaceElement);

    document.body.appendChild(viewportElement);

    let api = {

        get buttons() {
            return buttons;
        },

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
            viewportElement.classList.add("showing");
        } else {
            viewportElement.scrollTop = 0;
            viewportElement.classList.remove("showing");
        }
    }

    function setMessage(value) {
        message = String(value);

        contentElement.innerHTML = CBMessageMarkup.markupToHTML(message);
    }
});
