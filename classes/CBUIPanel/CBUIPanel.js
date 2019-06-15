"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIPanel */
/* global
    CBConvert,
    CBMessageMarkup,
    CBModel,
    CBUI,
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

Colby.afterDOMContentLoaded(
    function init() {
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
         *                      [button element]
         */

        let viewportElement = document.createElement("div");
        viewportElement.className = "CBUIPanel";

        let backgroundElement = document.createElement("div");
        backgroundElement.className = "CBUIPanel_background";

        viewportElement.appendChild(backgroundElement);

        let surfaceElement = document.createElement("div");
        surfaceElement.className = "CBUIPanel_surface";

        backgroundElement.appendChild(surfaceElement);

        let messageElement = document.createElement("div");
        messageElement.className = "CBUIPanel_message";

        surfaceElement.appendChild(messageElement);

        let contentElement = document.createElement("div");
        contentElement.className = "CBUIPanel_content CBContentStyleSheet";

        messageElement.appendChild(contentElement);

        let interfaceElement = CBUI.createElement("CBUIPanel_interface");

        surfaceElement.appendChild(interfaceElement);

        document.body.appendChild(viewportElement);

        let api = {

            get buttons() {
                return buttons;
            },

            set buttons(value) {
                init_setButtons(value);
            },

            get isShowing() {
                return isShowing;
            },

            set isShowing(value) {
                init_setIsShowing(value);
            },

            get message() {
                return message;
            },

            set message(value) {
                init_setMessage(value);
            },

            reset: init_reset,
        };

        init_reset();

        Object.defineProperty(window, 'CBUIPanel', {
            value: api,
        });

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @param object args
         *
         *      {
         *          callback: function
         *          title: string
         *      }
         *
         * @return Element
         */
        function init_createButtonElement(args) {
            let containerElement = CBUI.createElement("CBUI_container1");
            let buttonElement = CBUI.createElement("CBUI_button1");

            containerElement.appendChild(buttonElement);

            buttonElement.textContent = CBModel.valueToString(
                args,
                "title"
            );

            let callback = CBModel.valueAsFunction(
                args,
                "callback"
            );

            if (callback === undefined) {
                callback = init_reset;
            }

            buttonElement.addEventListener(
                "click",
                callback
            );

            return containerElement;
        }
        /* init_createButtonElement() */


        /**
         * @return undefined
         */
        function init_reset() {
            init_setIsShowing(false);

            init_setMessage("");

            init_setButtons(
                [
                    {
                        title: "OK",
                    }
                ]
            );
        }


        /**
         * @param [object] buttonArgsArray
         *
         *      {
         *          callback: function
         *          title: string,
         *      }
         *
         * @return undefined
         */
        function init_setButtons(buttonArgsArray) {
            interfaceElement.textContent = "";

            if (
                Array.isArray(buttonArgsArray) &&
                buttonArgsArray.length > 0
            ) {
                buttonArgsArray.forEach(
                    function (buttonArgs) {
                        let buttonElement = init_createButtonElement(
                            buttonArgs
                        );

                        interfaceElement.appendChild(
                            buttonElement
                        );
                    }
                );
            }
        }
        /* init_setButtons() */


        /**
         * @param bool value
         *
         * @return undefined
         */
        function init_setIsShowing(value) {
            isShowing = !!value;

            if (isShowing) {
                viewportElement.classList.add("showing");
            } else {
                viewportElement.scrollTop = 0;
                viewportElement.classList.remove("showing");
            }
        }
        /* init_setIsShowing() */


        /**
         * @param string message
         *
         * @return undefined
         */
        function init_setMessage(value) {
            message = CBConvert.valueToString(value);

            contentElement.innerHTML = CBMessageMarkup.markupToHTML(message);
        }
        /* init_setMessage() */
    }
    /* init() */
);
