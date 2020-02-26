"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,
    CBUIMessagePart,
    CBUISectionItem4,
    Colby,

    CBStatusAdminPage_duplicateURIMessages,
    CBStatusAdminPage_issues,
*/



(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );


    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main")[0];

        if (CBStatusAdminPage_issues.length > 0) {
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

            CBStatusAdminPage_issues.forEach(
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

        if (CBStatusAdminPage_duplicateURIMessages.length > 0) {
            mainElement.appendChild(
                CBUI.createSectionHeader(
                    {
                        text: "Duplicate URIs",
                    }
                )
            );

            let sectionElement = CBUI.createSection();

            CBStatusAdminPage_duplicateURIMessages.forEach(
                function (message) {
                    let sectionItem = CBUISectionItem4.create();
                    let messagePart = CBUIMessagePart.create();
                    messagePart.message = message;

                    sectionItem.appendPart(messagePart);
                    sectionElement.appendChild(sectionItem.element);
                }
            );

            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
    }
    /* afterDOMContentLoaded() */

})();
