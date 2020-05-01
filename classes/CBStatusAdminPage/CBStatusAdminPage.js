"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBModel,
    CBUI,
    CBUIMessagePart,
    CBUIPanel,
    Colby,
*/



(function () {

    let mainElement;
    let statusTextElement;



    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );


    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        {
            let mainElements = document.getElementsByClassName(
                "CBStatusAdminPage"
            );

            if (mainElements.length < 1) {
                return;
            }

            mainElement = mainElements.item(0);

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_text1"
            );

            mainElement.appendChild(
                elements[0]
            );

            statusTextElement = elements[2];

            statusTextElement.textContent = "fetching issues...";
        }

        Colby.callAjaxFunction(
            "CBStatusAdminPage",
            "fetchMessages"
        ).then(
            function (messages) {
                renderMessages(messages);
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
            }
        );
    }
    /* afterDOMContentLoaded() */



    /**
     * @param object messages
     *
     *      {
     *          issueCBMessages: [string]
     *          duplicateURICBMessages: [string]
     *      }
     *
     * @return undefined
     */
    function renderMessages(
        messages
    ) {
        let issueCBMessages = CBModel.valueToArray(
            messages,
            "issueCBMessages"
        );

        let duplicateURICBMessages = CBModel.valueToArray(
            messages,
            "duplicateURICBMessages"
        );

        if (
            issueCBMessages.length === 0 &&
            duplicateURICBMessages.length === 0
        ) {
            statusTextElement.textContent = "no issues were found";
        } else {
            statusTextElement.textContent = "issues below";
        }

        if (issueCBMessages.length > 0) {
            {
                let issuesTitleElement = CBUI.createElement(
                    "CBUI_title1"
                );

                mainElement.appendChild(
                    issuesTitleElement
                );

                issuesTitleElement.textContent = "Issues";
            }

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            mainElement.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            issueCBMessages.forEach(
                function (issue) {
                    let sectionItemElement = CBUI.createElement(
                        "CBUI_sectionItem"
                    );

                    sectionElement.appendChild(
                        sectionItemElement
                    );

                    let messagePart = CBUIMessagePart.create();

                    sectionItemElement.appendChild(
                        messagePart.element
                    );

                    if (Array.isArray(issue)) {
                        messagePart.message = `

                            ${issue[0]}

                            ${issue[1]}

                        `;
                    } else {
                        messagePart.message = issue;
                    }
                }
            );
        }

        if (duplicateURICBMessages.length > 0) {
            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            mainElement.appendChild(
                titleElement
            );

            titleElement.textContent = "Duplicate URIs";

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            mainElement.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            duplicateURICBMessages.forEach(
                function (message) {
                    let sectionItemElement = CBUI.createElement(
                        "CBUI_sectionItem"
                    );

                    sectionElement.appendChild(
                        sectionItemElement
                    );

                    let messagePart = CBUIMessagePart.create();

                    sectionItemElement.appendChild(
                        messagePart.element
                    );

                    messagePart.message = message;
                }
            );
        }
    }
    /* renderMessages() */

})();
