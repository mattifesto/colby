"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBModel,
    CBUI,
    CBUIMessagePart,
    CBUISectionItem4,
    Colby,
*/



(function () {

    let mainElement;



    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );


    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        mainElement = document.getElementsByTagName("main")[0];

        Colby.callAjaxFunction(
            "CBStatusAdminPage",
            "fetchMessages"
        ).then(
            function (messages) {
                renderMessages(messages);
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
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

        if (issueCBMessages.length > 0) {
            {
                let issuesTitleElement = CBUI.createElement(
                    "CBUI_title1"
                );

                issuesTitleElement.textContent = "Issues";

                mainElement.appendChild(
                    issuesTitleElement
                );
            }

            let sectionElement = CBUI.createSection();

            issueCBMessages.forEach(
                function (issue) {
                    let sectionItem = CBUISectionItem4.create();
                    let messagePart = CBUIMessagePart.create();

                    if (Array.isArray(issue)) {
                        messagePart.message = `

                            ${issue[0]}

                            ${issue[1]}

                        `;
                    } else {
                        messagePart.message = issue;
                    }

                    sectionItem.appendPart(messagePart);
                    sectionElement.appendChild(sectionItem.element);
                }
            );

            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }

        let duplicateURICBMessages = CBModel.valueToArray(
            messages,
            "duplicateURICBMessages"
        );

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
