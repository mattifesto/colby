"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIPanel */
/* global
    CBConvert,
    CBException,
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
 *          function confirmText(textContent) -> Promise -> bool
 *
 *          function displayAjaxResponse(ajaxResponse)
 *
 *          function displayBusyText(textContent) -> object
 *
 *          function displayCBMessage(cbmessage, buttonTextContent)
 *          -> Promise -> undefined
 *
 *          function displayElement(contentElement)
 *
 *          function displayError(error)
 *
 *          function displayText(textContent, buttonTextContent)
 *          -> Promise -> undefined
 *
 *
 *          -- everything below is deprecated -- -- -- -- --
 *
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

            confirmText: init_confirmText,

            displayAjaxResponse: init_displayAjaxResponse,

            displayBusyText: init_displayBusyText,

            displayCBMessage: init_displayCBMessage,

            displayElement: init_displayElement,

            displayError: init_displayError,

            displayText: init_displayText,


            /**
             * @deprecated 2019_09_19
             */
            get buttons() {
                return buttons;
            },

            /**
             * @deprecated 2019_09_19
             */
            set buttons(value) {
                init_setButtons(value);
            },

            /**
             * @deprecated 2019_09_19
             */
            get isShowing() {
                return isShowing;
            },

            /**
             * @deprecated 2019_09_19
             */
            set isShowing(value) {
                init_setIsShowing(value);
            },

            /**
             * @deprecated 2019_09_19
             */
            get message() {
                return message;
            },

            /**
             * @deprecated 2019_09_19
             */
            set message(value) {
                init_setMessage(value);
            },

            /**
             * @deprecated 2019_09_19
             */
            reset: init_reset,
        };

        init_reset();

        Object.defineProperty(
            window,
            'CBUIPanel',
            {
                value: api,
            }
        );

        return;



        /* -- closures -- -- -- -- -- */


        /**
         * @param string textContent
         *
         * @return Promise -> bool
         */
        function init_confirmText(textContent) {
            let cancel, confirm;

            let promise = new Promise(
                init_confirmText_execute
            );

            promise.CBUIPanel = {
                cancel: cancel,
                confirm: confirm,
            };

            return promise;


            /* -- closures -- -- -- -- -- */


            /**
             * @param function resolve
             * @param function reject
             *
             * @return undefined
             */
            function init_confirmText_execute(resolve, reject) {
                try {
                    cancel = function () {
                        try {
                            element.CBUIPanel.hide();
                            promise.CBUIPanel = undefined;
                            resolve(false);
                        } catch (error) {
                            reject(error);
                        }
                    };

                    confirm = function () {
                        try {
                            element.CBUIPanel.hide();
                            promise.CBUIPanel = undefined;
                            resolve(true);
                        } catch (error) {
                            reject(error);
                        }
                    };

                    let element = CBUI.createElement();

                    element.appendChild(
                        init_createTextElement(textContent)
                    );

                    element.appendChild(
                        init_createButtonElement(
                            {
                                callback: confirm,
                                title: "OK",
                            }
                        )
                    );

                    element.appendChild(
                        init_createButtonElement(
                            {
                                callback: cancel,
                                title: "Cancel",
                            }
                        )
                    );

                    init_displayElement(element);
                } catch (error) {
                    reject(error);
                }
            }
            /* init_confirmText_execute() */
        }
        /* init_confirmText() */



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
         * Creates a tree of elements used for displaying cbmessage content
         * without a border.
         *
         * @param string cbmessage
         *
         * @return Element
         */
        function init_createCBMessageElement(cbmessage) {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer CBUI_padding_half"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section CBUI_section_noborder"
            );

            sectionContainerElement.appendChild(sectionElement);

            let contentElement = CBUI.createElement(
                "CBUI_content CBContentStyleSheet"
            );

            sectionElement.appendChild(contentElement);

            contentElement.innerHTML = CBMessageMarkup.messageToHTML(cbmessage);

            return sectionContainerElement;
        }
        /* init_createCBMessageElement() */



        /**
         * @param Element contentElement
         *
         * @return Element
         */
        function init_createPanelElement(contentElement) {
            let panelElement = document.createElement("div");
            panelElement.className = "CBUIPanel CBUIPanel_showing";

            let backgroundElement = document.createElement("div");
            backgroundElement.className = "CBUIPanel_background";

            panelElement.appendChild(backgroundElement);

            let surfaceElement = document.createElement("div");
            surfaceElement.className = "CBUIPanel_surface2";

            backgroundElement.appendChild(surfaceElement);

            surfaceElement.appendChild(contentElement);

            return panelElement;
        }
        /* init_createPanelElement() */



        /**
         * Creates a tree of elements used for displaying text content without a
         * border.
         *
         * @param string textContent
         *
         * @return Element
         */
        function init_createTextElement(textContent) {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer CBUI_padding_half"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section CBUI_section_noborder"
            );

            sectionContainerElement.appendChild(sectionElement);

            let textContainerElement = CBUI.createElement(
                "CBUI_container_topAndBottom"
            );

            sectionElement.appendChild(textContainerElement);

            let textElement = CBUI.createElement();

            textElement.textContent = textContent;

            textContainerElement.appendChild(textElement);

            return sectionContainerElement;
        }
        /* init_createTextElement() */



        /**
         * @param object ajaxResponse
         *
         * @return undefined
         */
        function init_displayAjaxResponse(ajaxResponse) {
            let element = CBUI.createElement();

            /* message */
            {
                let textContent = CBModel.valueToString(
                    ajaxResponse,
                    "message"
                );

                element.appendChild(
                    init_createTextElement(textContent)
                );
            }
            /* message */

            /* button */
            {
                let buttonElement;

                if (
                    ajaxResponse.classNameForException ===
                    "CBModelVersionMismatchException" ||
                    ajaxResponse.userMustLogIn
                ) {
                    buttonElement = init_createButtonElement(
                        {
                            callback: function () {
                                location.reload();
                            },
                            title: "Reload",
                        }
                    );
                } else {
                    buttonElement = init_createButtonElement(
                        {
                            callback: function () {
                                element.CBUIPanel.hide();
                            },
                            title: "OK",
                        }
                    );
                }

                element.appendChild(
                    buttonElement
                );
            }
            /* button */

            if (ajaxResponse.stackTrace) {
                let titleElement = CBUI.createElement(
                    "CBUI_title1"
                );

                titleElement.textContent = "Stack Trace";

                element.appendChild(titleElement);

                let sectionContainerElement = CBUI.createElement(
                    "CBUI_sectionContainer CBUI_padding_half"
                );

                element.appendChild(sectionContainerElement);

                let sectionElement = CBUI.createElement(
                    "CBUI_section CBUI_section_inner"
                );

                sectionContainerElement.appendChild(sectionElement);

                let textContainerElement = CBUI.createElement(
                    "CBUI_container_topAndBottom"
                );

                sectionElement.appendChild(textContainerElement);

                let textElement = CBUI.createElement(
                    "CBUI_whiteSpace_preWrap"
                );

                textElement.textContent = CBModel.valueToString(
                    ajaxResponse,
                    "stackTrace"
                );

                textContainerElement.appendChild(textElement);
            }
            /* stack trace */

            init_displayElement(element);
        }
        /* init_displayAjaxResponse() */



        /**
         * @param string text
         *
         * @return object
         *
         *      {
         *          hide: function
         *      }
         */
        function init_displayBusyText(textContent) {
            let element = CBUI.createElement();

            element.appendChild(
                init_createTextElement(textContent)
            );

            init_displayElement(element);

            return element.CBUIPanel;
        }
        /* init_displayBusyText() */



        /**
         * @param string cbmessage
         * @param string buttonTextContent
         *
         *      The default value for this parameter is "OK".
         *
         * @return Promise
         *
         *      The promise resolves when the user clicks the button.
         */
        function init_displayCBMessage(cbmessage, buttonTextContent) {
            return new Promise(
                function (resolve) {
                    let element = CBUI.createElement();

                    element.appendChild(
                        init_createCBMessageElement(cbmessage)
                    );

                    buttonTextContent = CBConvert.valueToString(
                        buttonTextContent
                    ).trim();

                    if (buttonTextContent === "") {
                        buttonTextContent = "OK";
                    }

                    element.appendChild(
                        init_createButtonElement(
                            {
                                callback: function () {
                                    element.CBUIPanel.hide();
                                    resolve();
                                },
                                title: buttonTextContent,
                            }
                        )
                    );

                    init_displayElement(element);
                }
            );
            /* new Promise */
        }
        /* init_displayCBMessage() */



        /**
         * @param Element contentElement
         *
         * @return undefined
         */
        function init_displayElement(contentElement) {
            if (contentElement.CBUIPanel !== undefined) {
                throw CBException.withError(
                    Error(
                        "The contentElement argument is already " +
                        "being displayed."
                    ),
                    "",
                    "44a2f3c7d22095385de52b49193dea07b004fee3"
                );
            }

            let panelElement = init_createPanelElement(contentElement);

            document.body.appendChild(panelElement);

            contentElement.CBUIPanel = {
                hide: function CBUIPanel_hide() {
                    document.body.removeChild(panelElement);

                    contentElement.parentElement.removeChild(
                        contentElement
                    );

                    contentElement.CBUIPanel = undefined;
                }
            };
        }
        /* init_displayElement() */


        /**
         * @param Error error
         *
         * @return undefined
         */
        function init_displayError(error) {
            if (!Colby.browserIsSupported) {
                return;
            }

            if (error.ajaxResponse) {
                init_displayAjaxResponse(
                    error.ajaxResponse
                );
            } else {
                init_displayText(
                    Colby.errorToMessage(error)
                );
            }
        }
        /* init_displayError() */


        /**
         * @param string textContent
         * @param string buttonTextContent
         *
         *      The default value for this parameter is "OK".
         *
         * @return Promise
         *
         *      The promise resolves when the user clicks the OK button.
         */
        function init_displayText(textContent, buttonTextContent) {
            return new Promise(
                function (resolve) {
                    let element = CBUI.createElement();

                    element.appendChild(
                        init_createTextElement(textContent)
                    );

                    buttonTextContent = CBConvert.valueToString(
                        buttonTextContent
                    ).trim();

                    if (buttonTextContent === "") {
                        buttonTextContent = "OK";
                    }

                    element.appendChild(
                        init_createButtonElement(
                            {
                                callback: function () {
                                    element.CBUIPanel.hide();
                                    resolve();
                                },
                                title: buttonTextContent,
                            }
                        )
                    );

                    init_displayElement(element);
                }
            );
            /* new Promise */
        }
        /* init_displayText() */


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
        /* init_reset() */


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
                viewportElement.classList.add("CBUIPanel_showing");
            } else {
                viewportElement.scrollTop = 0;
                viewportElement.classList.remove("CBUIPanel_showing");
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
