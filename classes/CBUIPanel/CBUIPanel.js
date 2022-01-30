/* global
    CBConvert,
    CBErrorHandler,
    CBException,
    CBMessageMarkup,
    CBModel,
    CBUI,
*/


(function () {
    "use strict";

    /**
     * @NOTE 2022_01_29
     *
     *      This global variable was previously defined only after the DOM
     *      content had loaded, which is odd. But it is true that these APIs
     *      modify the DOM and should not be called until after the DOM content
     *      has loaded.
     */
    window.CBUIPanel = {
        confirmText: init_confirmText,
        displayAjaxResponse: CBUIPanel_displayAjaxResponse, /* deprecated */
        displayAjaxResponse2: CBUIPanel_displayAjaxResponse2,
        displayAndReportError: CBUIPanel_displayAndReportError,
        displayBusyText: init_displayBusyText,
        displayCBMessage: init_displayCBMessage,
        displayElement: init_displayElement,
        displayError: CBUIPanel_displayError, /* deprecated */
        displayError2: CBUIPanel_displayError2,
        displayText: CBUIPanel_displayText, /* deprecated */
        displayText2: CBUIPanel_displayText2,
        hidePanelWithContentElement: CBUIPanel_hidePanelWithContentElement,
    };



    /**
     * This function will hide a panel containing the contentElement parameter.
     * If there is no panel containing the contentElement, this function will do
     * nothing.
     *
     * @param Element contentElement
     *
     * @return undefined
     */
    function
    CBUIPanel_hidePanelWithContentElement(
        contentElement
    ) {
        if (typeof contentElement.CBUIPanel_hidePanel === "function") {
            contentElement.CBUIPanel_hidePanel();
        }
    }
    /* CBUIPanel_hidePanelWithContentElement() */



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
                        CBUIPanel_hidePanelWithContentElement(
                            element
                        );

                        promise.CBUIPanel = undefined;

                        resolve(false);
                    } catch (error) {
                        reject(error);
                    }
                };

                confirm = function () {
                    try {
                        CBUIPanel_hidePanelWithContentElement(
                            element
                        );

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
            throw Error(
                "Buttons must have a callback."
            );
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
     *      A CBAjaxResponse model.
     *
     * @return Promise -> undefined
     */
    async function
    CBUIPanel_displayAjaxResponse(
        ajaxResponse
    ) {
        return CBUIPanel_displayAjaxResponse2(
            ajaxResponse
        ).CBUIPanel_getClosePromise();
    }
    /* CBUIPanel_displayAjaxResponse() */



    /**
     * @param object ajaxResponse
     *
     *      A CBAjaxResponse model.
     *
     * @return object
     *
     *      {
     *          CBUIPanel_getClosePromise: function -> Promise -> undefined
     *          CBUIPanel_close: function -> undefined
     *      }
     */
    function
    CBUIPanel_displayAjaxResponse2(
        ajaxResponse
    ) {
        let resolve;

        let promise = new Promise(
            function (resolveCallback) {
                resolve = resolveCallback;
            }
        );

        let element = CBUI.createElement();


        /* message */
        {
            let textContent = CBModel.valueToString(
                ajaxResponse,
                "message"
            );

            element.appendChild(
                init_createTextElement(
                    textContent
                )
            );
        }
        /* message */


        /* button */
        {
            let buttonElement;
            let versionMismatchSourceCBID = (
                "a567dc90ccb59fb918ced4ae7f82e6d1b556f932"
            );

            if (
                ajaxResponse.sourceCBID === versionMismatchSourceCBID ||
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
                        callback: CBUIPanel_close,
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


        init_displayElement(
            element
        );

        return {
            CBUIPanel_close,

            CBUIPanel_getClosePromise: function () {
                return promise;
            },
        };



        /**
         * @return undefined
         */
        function CBUIPanel_close() {
            CBUIPanel_hidePanelWithContentElement(
                element
            );

            resolve();
        }
        /* CBUIPanel_close() */

    }
    /* CBUIPanel_displayAjaxResponse2() */



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
     * @return Promise -> undefined
     *
     *      The promise resolves when the user clicks the button.
     */
    async function init_displayCBMessage(
        cbmessage,
        buttonTextContent
    ) {
        let resolve;

        let promise = new Promise(
            function (resolveCallback) {
                resolve = resolveCallback;
            }
        );

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
                        CBUIPanel_hidePanelWithContentElement(
                            element
                        );

                        resolve();
                    },
                    title: buttonTextContent,
                }
            )
        );

        init_displayElement(
            element
        );

        return promise;
    }
    /* init_displayCBMessage() */



    /**
     * @param Element contentElement
     *
     * @return undefined
     */
    function
    init_displayElement(
        contentElement
    ) {
        if (contentElement.CBUIPanel_hidePanel !== undefined) {
            let message = CBConvert.stringToCleanLine(`

                The contentElement argument is already being displayed.

            `);

            throw CBException.withError(
                Error(message),
                "",
                "44a2f3c7d22095385de52b49193dea07b004fee3"
            );
        }

        let panelElement = init_createPanelElement(
            contentElement
        );

        document.body.appendChild(
            panelElement
        );

        /**
         * @deprecated 2020_12_02
         *
         *      THe CBUIPanel property on content elements has been deprecated
         *      and replaced by the global function
         *      CBUIPanel_hidePanelWithContentElement().
         */
        Object.defineProperty(
            contentElement,
            "CBUIPanel",
            {
                configurable: true,
                enumerable: false,
                value: {
                    hide: CBUIPanel_hidePanel,
                },
                writable: false,
            }
        );


        Object.defineProperty(
            contentElement,
            "CBUIPanel_hidePanel",
            {
                configurable: true,
                enumerable: false,
                value: CBUIPanel_hidePanel,
                writable: false,
            }
        );



        /**
         * @return undefined
         */
        function CBUIPanel_hidePanel() {
            document.body.removeChild(
                panelElement
            );

            contentElement.parentElement.removeChild(
                contentElement
            );

            delete contentElement.CBUIPanel;
            delete contentElement.CBUIPanel_hidePanel;
        }
        /* CBUIPanel_hidePanel() */

    }
    /* init_displayElement() */



    /**
     * @param Error error
     *
     * @return Promise -> undefined
     */
    async function
    CBUIPanel_displayError(
        error
    ) {
        return CBUIPanel_displayError2(
            error
        ).CBUIPanel_getClosePromise();
    }
    /* displayError() */



    /**
     * @param Error error
     *
     * @return object
     *
     *      {
     *          CBUIPanel_getClosePromise: function -> Promise -> undefined
     *          CBUIPanel_close: function -> undefined
     *      }
     */
    function
    CBUIPanel_displayError2(
        error
    ) {
        if (
            !CBErrorHandler.getCurrentBrowserIsSupported()
        ) {
            return;
        }

        if (
            error.ajaxResponse
        ) {
            return CBUIPanel_displayAjaxResponse2(
                error.ajaxResponse
            );
        }

        else {
            let oneLineErrorReport = CBException.errorToOneLineErrorReport(
                error
            );

            return CBUIPanel_displayText2(
                oneLineErrorReport
            );
        }
    }
    /* CBUIPanel_displayError2() */



    /**
     * @param Error error
     *
     * @return Promise -> undefined
     */
    function
    CBUIPanel_displayAndReportError(
        error
    ) {
        CBErrorHandler.report(
            error
        );

        return CBUIPanel_displayError(
            error
        );
    }
    /* displayAndReportError() */



    /**
     * @param string textContent
     * @param string buttonTextContent
     *
     *      The default value for this parameter is "OK".
     *
     * @return Promise -> undefined
     *
     *      The promise resolves when the user clicks the OK button.
     */
    async function
    CBUIPanel_displayText(
        textContent,
        buttonTextContent
    ) {
        return CBUIPanel_displayText2(
            textContent,
            buttonTextContent
        ).CBUIPanel_getClosePromise();
    }
    /* displayText() */



    /**
     * @param string textContent
     * @param string buttonTextContent
     *
     *      The default value for this parameter is "OK".
     *
     * @return object
     *
     *      {
     *          CBUIPanel_getClosePromise: function -> Promise -> undefined
     *          CBUIPanel_close: function -> undefined
     *      }
     */
    function
    CBUIPanel_displayText2(
        textContent,
        buttonTextContent
    ) {
        let resolve;

        let promise = new Promise(
            function (resolveCallback) {
                resolve = resolveCallback;
            }
        );

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
                    callback: CBUIPanel_close,
                    title: buttonTextContent,
                }
            )
        );

        init_displayElement(
            element
        );

        return {
            CBUIPanel_close,

            CBUIPanel_getClosePromise: function () {
                return promise;
            },
        };



        /**
         * @return undefined
         */
        function CBUIPanel_close() {
            CBUIPanel_hidePanelWithContentElement(
                element
            );

            resolve();
        }
        /* CBUIPanel_close() */

    }
    /* CBUIPanel_displayText2() */

})();
