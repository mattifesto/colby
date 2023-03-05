/* global
    CBAjaxResponse,
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
    window.CBUIPanel =
    {
        confirmText:
        CBUIPanel_confirmText,

        displayAjaxResponse2:
        CBUIPanel_displayAjaxResponse2,

        displayAndReportError:
        CBUIPanel_displayAndReportError,

        displayBusyText:
        CBUIPanel_displayBusyText,

        displayCBMessage:
        CBUIPanel_displayCBMessage,

        displayElement:
        CBUIPanel_displayElement,

        displayError2:
        CBUIPanel_displayError2,

        displayText2:
        CBUIPanel_displayText2,

        hidePanelWithContentElement:
        CBUIPanel_hidePanelWithContentElement,

        /* deprecated */
        displayAjaxResponse:
        CBUIPanel_displayAjaxResponse,

        /* deprecated */
        displayError:
        CBUIPanel_displayError,

        /* deprecated */
        displayText:
        CBUIPanel_displayText,
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
    ) // -> undefined
    {
        if (
            typeof contentElement.CBUIPanel_hidePanel === "function"
        ) {
            contentElement.CBUIPanel_hidePanel();
        }
    }
    /* CBUIPanel_hidePanelWithContentElement() */



    /**
     * @param string textContent
     *
     * @return Promise -> bool
     */
    function
    CBUIPanel_confirmText(
        textContent
    ) // -> Promise -> bool
    {
        let cancel, confirm;

        let promise =
        new Promise(
            CBUIPanel_confirmText_execute
        );

        promise.CBUIPanel =
        {
            cancel: cancel,
            confirm: confirm,
        };

        return promise;



        /* -- closures -- */



        /**
         * @param function resolve
         * @param function reject
         *
         * @return undefined
         */
        function
        CBUIPanel_confirmText_execute(
            resolve,
            reject
        ) // -> undefined
        {
            try
            {
                cancel =
                function () {
                    try
                    {
                        CBUIPanel_hidePanelWithContentElement(
                            element
                        );

                        promise.CBUIPanel =
                        undefined;

                        resolve(
                            false
                        );
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
                    CBUIPanel_createTextElement(textContent)
                );

                element.appendChild(
                    CBUIPanel_createButtonElement(
                        {
                            callback: confirm,
                            title: "OK",
                        }
                    )
                );

                element.appendChild(
                    CBUIPanel_createButtonElement(
                        {
                            callback: cancel,
                            title: "Cancel",
                        }
                    )
                );

                CBUIPanel_displayElement(element);
            } catch (error) {
                reject(error);
            }
        }
        /* CBUIPanel_confirmText_execute() */
    }
    /* CBUIPanel_confirmText() */



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
    function
    CBUIPanel_createButtonElement(
        args
    ) // -> Element
    {
        let containerElement =
        CBUI.createElement(
            "CBUI_container1"
        );

        let buttonElement =
        CBUI.createElement(
            "CBUI_button1"
        );

        containerElement.appendChild(
            buttonElement
        );

        buttonElement.textContent =
        CBModel.valueToString(
            args,
            "title"
        );

        let callback =
        CBModel.valueAsFunction(
            args,
            "callback"
        );

        if (
            callback === undefined
        ) {
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
    /* CBUIPanel_createButtonElement() */



    /**
     * Creates a tree of elements used for displaying cbmessage content
     * without a border.
     *
     * @param string cbmessage
     *
     * @return Element
     */
    function
    CBUIPanel_createCBMessageElement(
        cbmessage
    ) // -> Element
    {
        let sectionContainerElement =
        CBUI.createElement(
            "CBUI_sectionContainer CBUI_padding_half"
        );

        let sectionElement =
        CBUI.createElement(
            "CBUI_section CBUI_section_noborder"
        );

        sectionContainerElement.appendChild(
            sectionElement
        );

        let contentElement =
        CBUI.createElement(
            "CBUI_content CBContentStyleSheet"
        );

        sectionElement.appendChild(
            contentElement
        );

        contentElement.innerHTML =
        CBMessageMarkup.messageToHTML(
            cbmessage
        );

        return sectionContainerElement;
    }
    /* CBUIPanel_createCBMessageElement() */



    /**
     * @param Element contentElement
     *
     * @return Element
     */
    function
    CBUIPanel_createPanelElement(
        contentElement
    ) // -> Element
    {
        let panelElement =
        document.createElement(
            "div"
        );

        panelElement.className =
        "CBUIPanel CBUIPanel_showing";

        let backgroundElement =
        document.createElement(
            "div"
        );

        backgroundElement.className =
        "CBUIPanel_background";

        panelElement.appendChild(
            backgroundElement
        );

        let surfaceElement =
        document.createElement(
            "div"
        );

        surfaceElement.className =
        "CBUIPanel_surface2";

        backgroundElement.appendChild(
            surfaceElement
        );

        surfaceElement.appendChild(
            contentElement
        );

        return panelElement;
    }
    /* CBUIPanel_createPanelElement() */



    /**
     * Creates a tree of elements used for displaying text content without a
     * border.
     *
     * @param string textContent
     *
     * @return Element
     */
    function
    CBUIPanel_createTextElement(
        textContent
    ) // -> Element
    {
        let sectionContainerElement =
        CBUI.createElement(
            "CBUI_sectionContainer CBUI_padding_half"
        );

        let sectionElement =
        CBUI.createElement(
            "CBUI_section CBUI_section_noborder"
        );

        sectionContainerElement.appendChild(
            sectionElement
        );

        let textContainerElement =
        CBUI.createElement(
            "CBUI_container_topAndBottom"
        );

        sectionElement.appendChild(
            textContainerElement
        );

        let textElement =
        CBUI.createElement();

        textElement.textContent =
        textContent;

        textContainerElement.appendChild(
            textElement
        );

        return sectionContainerElement;
    }
    /* CBUIPanel_createTextElement() */



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
    ) // -> Promise -> undefined
    {
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
    ) // -> object
    {
        let resolve;

        let promise =
        new Promise(
            function (
                resolveCallback
            ) {
                resolve = resolveCallback;
            }
        );

        let element =
        CBUI.createElement();


        /* message */
        {
            let textContent =
            CBModel.valueToString(
                ajaxResponse,
                "message"
            );

            element.appendChild(
                CBUIPanel_createTextElement(
                    textContent
                )
            );
        }
        /* message */


        /* cbmessage */
        {
            let cbmessage =
            CBAjaxResponse.getCBMessage(
                ajaxResponse
            );

            if (
                cbmessage !== ""
            ) {
                let cbmessageElement =
                CBUIPanel_createCBMessageElement(
                    cbmessage
                );

                element.append(
                    cbmessageElement
                );
            }
        }
        /* cbmessage */


        /* button */
        {
            let buttonElement;

            let versionMismatchSourceCBID =
            "a567dc90ccb59fb918ced4ae7f82e6d1b556f932";

            if (
                ajaxResponse.sourceCBID === versionMismatchSourceCBID ||
                ajaxResponse.userMustLogIn
            ) {
                buttonElement =
                CBUIPanel_createButtonElement(
                    {
                        callback:
                        function () {
                            location.reload();
                        },

                        title:
                        "Reload",
                    }
                );
            } else {
                buttonElement =
                CBUIPanel_createButtonElement(
                    {
                        callback:
                        CBUIPanel_close,

                        title:
                        "OK",
                    }
                );
            }

            element.appendChild(
                buttonElement
            );
        }
        /* button */


        if (
            ajaxResponse.stackTrace
        ) {
            let titleElement =
            CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent =
            "Stack Trace";

            element.appendChild(
                titleElement
            );

            let sectionContainerElement =
            CBUI.createElement(
                "CBUI_sectionContainer CBUI_padding_half"
            );

            element.appendChild(
                sectionContainerElement
            );

            let sectionElement =
            CBUI.createElement(
                "CBUI_section CBUI_section_inner"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            let textContainerElement =
            CBUI.createElement(
                "CBUI_container_topAndBottom"
            );

            sectionElement.appendChild(
                textContainerElement
            );

            let textElement =
            CBUI.createElement(
                "CBUI_whiteSpace_preWrap"
            );

            textElement.textContent =
            CBModel.valueToString(
                ajaxResponse,
                "stackTrace"
            );

            textContainerElement.appendChild(
                textElement
            );
        }
        /* stack trace */


        CBUIPanel_displayElement(
            element
        );

        return {
            CBUIPanel_close,

            CBUIPanel_getClosePromise:
            function () {
                return promise;
            },
        };



        /**
         * @return undefined
         */
        function
        CBUIPanel_close(
        ) // -> undefined
        {
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
    function
    CBUIPanel_displayBusyText(
        textContent
    ) // -> object
    {
        let element =
        CBUI.createElement();

        element.appendChild(
            CBUIPanel_createTextElement(
                textContent
            )
        );

        CBUIPanel_displayElement(
            element
        );

        return element.CBUIPanel;
    }
    /* CBUIPanel_displayBusyText() */



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
    async function
    CBUIPanel_displayCBMessage(
        cbmessage,
        buttonTextContent
    ) // -> Promise -> undefined
    {
        let resolve;

        let promise =
        new Promise(
            function (
                resolveCallback
            ) {
                resolve =
                resolveCallback;
            }
        );

        let element =
        CBUI.createElement();

        element.appendChild(
            CBUIPanel_createCBMessageElement(
                cbmessage
            )
        );

        buttonTextContent =
        CBConvert.valueToString(
            buttonTextContent
        ).trim();

        if (
            buttonTextContent === ""
        ) {
            buttonTextContent =
            "OK";
        }

        element.appendChild(
            CBUIPanel_createButtonElement(
                {
                    callback:
                    function () {
                        CBUIPanel_hidePanelWithContentElement(
                            element
                        );

                        resolve();
                    },

                    title:
                    buttonTextContent,
                }
            )
        );

        CBUIPanel_displayElement(
            element
        );

        return promise;
    }
    /* CBUIPanel_displayCBMessage() */



    /**
     * @param Element contentElement
     *
     * @return undefined
     */
    function
    CBUIPanel_displayElement(
        contentElement
    ) // -> undefined
    {
        if (
            contentElement.CBUIPanel_hidePanel !== undefined
        ) {
            let message =
            CBConvert.stringToCleanLine(`

                The contentElement argument is already being displayed.

            `);

            throw CBException.withError(
                Error(message),
                "",
                "44a2f3c7d22095385de52b49193dea07b004fee3"
            );
        }

        let panelElement =
        CBUIPanel_createPanelElement(
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
                configurable:
                true,

                enumerable:
                false,

                value:
                {
                    hide: CBUIPanel_hidePanel,
                },

                writable: false,
            }
        );


        Object.defineProperty(
            contentElement,
            "CBUIPanel_hidePanel",
            {
                configurable:
                true,

                enumerable:
                false,

                value:
                CBUIPanel_hidePanel,

                writable:
                false,
            }
        );



        /**
         * @return undefined
         */
        function
        CBUIPanel_hidePanel(
        )  // -> undefined
        {
            document.body.removeChild(
                panelElement
            );

            contentElement.parentElement.removeChild(
                contentElement
            );

            delete
            contentElement.CBUIPanel;

            delete
            contentElement.CBUIPanel_hidePanel;
        }
        /* CBUIPanel_hidePanel() */

    }
    /* CBUIPanel_displayElement() */



    /**
     * @param Error error
     *
     * @return Promise -> undefined
     */
    async function
    CBUIPanel_displayError(
        error
    ) // -> undefined
    {
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
    ) // -> object
    {
        if (
            !CBErrorHandler.getCurrentBrowserIsSupported()
        ) {
            return;
        }

        let ajaxResponse =
        CBAjaxResponse.fromError(
            error
        );

        if (
            ajaxResponse !== undefined
        ) {
            return CBUIPanel_displayAjaxResponse2(
                ajaxResponse
            );
        }

        else {
            let oneLineErrorReport =
            CBException.errorToOneLineErrorReport(
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
    ) // Promise -> undefined
    {
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
    ) // -> Promise -> undefined
    {
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
    ) // -> object
    {
        let resolve;

        let promise =
        new Promise(
            function (
                resolveCallback
            ) // -> undefined
            {
                resolve =
                resolveCallback;
            }
        );

        let element =
        CBUI.createElement();

        element.appendChild(
            CBUIPanel_createTextElement(
                textContent
            )
        );

        buttonTextContent =
        CBConvert.valueToString(
            buttonTextContent
        ).trim();

        if (
            buttonTextContent === ""
        ) {
            buttonTextContent =
            "OK";
        }

        element.appendChild(
            CBUIPanel_createButtonElement(
                {
                    callback:
                    CBUIPanel_close,

                    title:
                    buttonTextContent,
                }
            )
        );

        CBUIPanel_displayElement(
            element
        );

        return {
            CBUIPanel_close,

            CBUIPanel_getClosePromise:
            function (
            ) // -> Promise
            {
                return promise;
            },
        };



        /**
         * @return undefined
         */
        function
        CBUIPanel_close(
        ) // -> undefined
        {
            CBUIPanel_hidePanelWithContentElement(
                element
            );

            resolve();
        }
        /* CBUIPanel_close() */

    }
    /* CBUIPanel_displayText2() */

})();
