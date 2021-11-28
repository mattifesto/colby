/* global
    CB_CBView_Moment,
    CBAjax,
    CBErrorHandler,
    CBUIButton,
    CBUIStringEditor2,
    Colby,
*/


(function () {
    "use strict";

    Colby.afterDOMContentLoaded(
        function () {
            let elements = Array.from(
                document.getElementsByClassName(
                    "CB_CBView_MomentCreator"
                )
            );

            elements.forEach(
                function (element) {
                    CB_CBView_MomentCreator_initializeElement(
                        element
                    );
                }
            );
        }
    );



    /**
     * @param Element element
     *
     * @return undefined
     */
    function
    CB_CBView_MomentCreator_initializeElement(
        element
    ) {
        let moment = CB_CBView_Moment.create();

        element.append(
            moment.CB_CBView_Moment_getElement()
        );

        let stringEditor = CBUIStringEditor2.create();

        stringEditor.CBUIStringEditor2_setPlaceholderText(
            "Share a Moment"
        );

        moment.CB_CBView_Moment_append(
            stringEditor.CBUIStringEditor2_getElement()
        );

        let button = CBUIButton.create();

        button.CBUIButton_setTextContent(
            "Share"
        );

        button.CBUIButton_addClickEventListener(
            function () {
                createMoment();
            }
        );

        moment.CB_CBView_Moment_append(
            button.CBUIButton_getElement()
        );



        /**
         * @return undefined
         */
        async function
        createMoment(
        ) {
            try {
                await CBAjax.call(
                    "CB_Moment",
                    "create",
                    {
                        CB_Moment_create_text: (
                            stringEditor.CBUIStringEditor2_getValue()
                        ),
                    }
                );

                stringEditor.CBUIStringEditor2_setValue("");
            } catch (error) {
                CBErrorHandler.report(
                    error
                );
            }
        }
        /* createMoment() */

    }
    /* CB_CBView_MomentCreator_initializeElement() */

})();
