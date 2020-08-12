"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBAjax,
    CBUI,
    CBUIPanel,
    Colby,
*/


(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "CBDocumentation_DeveloperView"
        );

        for (let index = 0; index < elements.length; index += 1) {
            let element = elements.item(index);

            initializeElement(
                element
            );
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @param Element element
     *
     * @return undefined
     */
    function initializeElement(
        element
    ) {
        let buttonElement = CBUI.createElement(
            "CBUI_button1"
        );

        element.appendChild(
            buttonElement
        );

        let targetClassName = element.dataset.targetClassName;

        buttonElement.textContent = (
            "Create " +
            targetClassName +
            " Documentation File"
        );

        let isCurrentlyActive = false;

        buttonElement.addEventListener(
            "click",
            function () {
                if (isCurrentlyActive) {
                    return;
                }

                buttonElement.classList.add(
                    "CBUI_button1_disabled"
                );

                isCurrentlyActive = true;

                CBAjax.call(
                    "CBDocumentation",
                    "createDocumentationFile",
                    {
                        targetClassName,
                    }
                ).then(
                    function () {
                        element.removeChild(
                            buttonElement
                        );
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayAndReportError(
                            error
                        );

                        buttonElement.classList.remove(
                            "CBUI_button1_disabled"
                        );
                    }
                ).finally(
                    function () {
                        isCurrentlyActive = false;
                    }
                );
            }
        );

    }
    /* initializeElement() */

})();
