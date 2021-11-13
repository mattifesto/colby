/* global
    CB_Brick_Padding10,
    CB_Brick_TextContainer,
    CBModel,
    CBUIStringEditor2,
*/

(function () {
    "use strict";

    window.CB_CBView_AdSenseEditor = {
        CBUISpecEditor_createEditorElement,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let element = createMainElement();

        element.CB_CBView_AdSenseEditor_mainElementAppend(
            createClientEditorElement(
                spec,
                specChangedCallback
            )
        );

        element.CB_CBView_AdSenseEditor_mainElementAppend(
            createSlotEditorElement(
                spec,
                specChangedCallback
            )
        );

        return element;
    }
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    createClientEditorElement(
        spec,
        specChangedCallback
    ) {
        let clientEditorElement = CBUIStringEditor2.create();

        clientEditorElement.CBUIStringEditor2_setValue(
            CBModel.valueToString(
                spec,
                'CB_CBView_AdSense_client'
            )
        );

        clientEditorElement.CBUIStringEditor2_setTitle(
            "Client"
        );

        clientEditorElement.CBUIStringEditor2_setHasOutline(
            true
        );

        clientEditorElement.CBUIStringEditor2_setChangedEventListener(
            function () {
                spec.CB_CBView_AdSense_client = (
                    clientEditorElement.CBUIStringEditor2_getValue()
                );

                specChangedCallback();
            }
        );

        return clientEditorElement.CBUIStringEditor2_getElement();
    }
    /* createClientEditorElement() */



    /**
     * @return Element
     */
    function
    createMainElement(
    ) {
        let mainElement = document.createElement(
            "div"
        );

        mainElement.className = "CB_CBView_AdSenseEditor CB_UI";

        let padding10 = CB_Brick_Padding10.create();

        mainElement.append(
            padding10.CB_Brick_Padding10_getOuterElement()
        );

        let textContainer = CB_Brick_TextContainer.create();

        padding10.CB_Brick_Padding10_getInnerElement().append(
            textContainer.CB_Brick_TextContainer_getOuterElement()
        );

        mainElement.CB_CBView_AdSenseEditor_mainElementAppend = (
            CB_CBView_AdSenseEditor_mainElementAppend
        );

        function
        CB_CBView_AdSenseEditor_mainElementAppend(
            childElement
        ) {
            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                childElement
            );
        }

        return mainElement;
    }
    /* createMainElement() */



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    createSlotEditorElement(
        spec,
        specChangedCallback
    ) {
        let slotEditorElement = CBUIStringEditor2.create();

        slotEditorElement.CBUIStringEditor2_setValue(
            CBModel.valueToString(
                spec,
                'CB_CBView_AdSense_slot'
            )
        );

        slotEditorElement.CBUIStringEditor2_setTitle(
            "Slot"
        );

        slotEditorElement.CBUIStringEditor2_setHasOutline(
            true
        );

        slotEditorElement.CBUIStringEditor2_setChangedEventListener(
            function () {
                spec.CB_CBView_AdSense_slot = (
                    slotEditorElement.CBUIStringEditor2_getValue()
                );

                specChangedCallback();
            }
        );

        return slotEditorElement.CBUIStringEditor2_getElement();
    }
    /* createSlotEditorElement() */

})();
