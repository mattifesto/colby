/* global
    CBConvert,
    CBModel,
    CBUI,
    CBUIStringEditor2,
*/


(function () {
    "use strict";

    window.SCPromotionExecutor_CBOrderDiscountEditor = {
        CBUISpecEditor_createEditorElement2,
    };



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        spec,
        specChangedCallback
    ) {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[1];

        sectionElement.appendChild(
            createPercentDiscountEditorElement(
                spec,
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createDollarsInCentsPropertyEditorElement(
                spec,
                "SCPromotionExecutor_CBOrderDiscount_minimumSubtotalInCents",
                "Minimum Subtotal",
                specChangedCallback
            )
        );

        return element;
    }
    /* CBUISpecEditor_createEditorElement2() */



    /**
     * @param object promotionExecutorSpec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    createPercentDiscountEditorElement(
        promotionExecutorSpec,
        specChangedCallback
    ) {
        let listener = function () {
            let stringValue = editor.CBUIStringEditor2_getValue().trim();
            let valueIsInvalid = false;

            let numericValue = CBConvert.valueAsNumber(
                stringValue
            );

            if (stringValue !== "") {
                if (
                    numericValue === undefined ||
                    numericValue < 0 ||
                    numericValue > 100
                ) {
                    numericValue = undefined;
                    valueIsInvalid = true;
                }
            }

            promotionExecutorSpec
            .SCPromotionExecutor_CBOrderDiscount_percentDiscount = (
                numericValue
            );

            if (valueIsInvalid) {
                editor.CBUIStringEditor2_getElement().classList.add(
                    "CBUIStringEditor2_error"
                );
            } else {
                editor.CBUIStringEditor2_getElement().classList.remove(
                    "CBUIStringEditor2_error"
                );
            }

            specChangedCallback();
        };

        let editor = CBUIStringEditor2.create();

        editor.CBUIStringEditor2_setChangedEventListener(
            listener
        );

        editor.CBUIStringEditor2_setTitle(
            "Percent Discount"
        );

        editor.CBUIStringEditor2_setValue(
            CBConvert.valueToString(
                CBModel.valueAsNumber(
                    promotionExecutorSpec,
                    "SCPromotionExecutor_CBOrderDiscount_percentDiscount"
                )
            )
        );

        return editor.CBUIStringEditor2_getElement();
    }
    /* createPercentDiscountEditorElement() */

})();
