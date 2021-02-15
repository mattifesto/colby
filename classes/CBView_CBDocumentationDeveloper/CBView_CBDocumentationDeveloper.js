/* jshint esversion: 8 */
/* globals
    CBAjax,
    CBUI,
    CBUIPanel,
    Colby,
*/


(function () {
    "use strict";

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "CBView_CBDocumentationDeveloper"
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
        let elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        let buttonRootElement = elements[0];
        let buttonElement = elements[1];

        element.appendChild(
            buttonRootElement
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
            async function () {
                if (isCurrentlyActive) {
                    return;
                }

                isCurrentlyActive = true;

                try {
                    buttonElement.classList.add(
                        "CBUI_button1_disabled"
                    );

                    await CBAjax.call(
                        "CBView_CBDocumentationDeveloper",
                        "createDocumentationFile",
                        {
                            targetClassName,
                        }
                    );

                    element.removeChild(
                        buttonRootElement
                    );
                } catch (error) {
                    CBUIPanel.displayAndReportError(
                        error
                    );

                    buttonElement.classList.remove(
                        "CBUI_button1_disabled"
                    );
                } finally {
                    isCurrentlyActive = false;
                }
            }
        );

    }
    /* initializeElement() */

})();
