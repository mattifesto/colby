"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCProductBuyViewEditor */
/* globals
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIStringEditor,
*/



var SCProductBuyViewEditor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



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
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let elements;
        let spec = args.spec;
        let specChanged = args.specChangedCallback;

        elements = CBUI.createElementTree(
            "SCProductBuyViewEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];


        /* productCode */
        {
            let productCodeEditor = CBUIStringEditor.create();
            productCodeEditor.title = "Product Code";
            productCodeEditor.value = spec.productCode;

            productCodeEditor.changed = function () {
                spec.productCode = productCodeEditor.value;
                specChanged();
            };

            sectionElement.appendChild(
                productCodeEditor.element
            );
        }
        /* productCode */


        /* hide image */
        {
            let sectionItemElement = CBUI.createElement(
                "CBUI_sectionItem"
            );

            sectionElement.appendChild(sectionItemElement);

            let textContainerElement = CBUI.createElement(
                "CBUI_container_topAndBottom CBUI_flexGrow"
            );

            sectionItemElement.appendChild(textContainerElement);

            let textElement = CBUI.createElement();

            textElement.textContent = "Hide Image";

            textContainerElement.appendChild(textElement);

            let switchPart = CBUIBooleanSwitchPart.create();

            sectionItemElement.appendChild(switchPart.element);

            switchPart.value = CBModel.valueToBool(
                spec,
                'hideImage'
            );

            switchPart.changed = function () {
                spec.hideImage = switchPart.value;

                specChanged();
            };
        }
        /* hide image */


        /* show product page link */
        {
            let sectionItemElement = CBUI.createElement(
                "CBUI_sectionItem"
            );

            sectionElement.appendChild(sectionItemElement);

            let textContainerElement = CBUI.createElement(
                "CBUI_container_topAndBottom CBUI_flexGrow"
            );

            sectionItemElement.appendChild(textContainerElement);

            let textElement = CBUI.createElement();

            textElement.textContent = "Show Product Page Link";

            textContainerElement.appendChild(textElement);

            let switchPart = CBUIBooleanSwitchPart.create();

            sectionItemElement.appendChild(switchPart.element);

            switchPart.value = CBModel.valueToBool(
                spec,
                'showProductPageLink'
            );

            switchPart.changed = function () {
                spec.showProductPageLink = switchPart.value;

                specChanged();
            };
        }
        /* show product page link */

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /* -- CBUISpec interfaces -- -- -- -- -- */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let productCode = CBModel.valueToString(
            spec,
            "productCode"
        ).trim();

        if (productCode === "") {
            return undefined;
        } else {
            return productCode;
        }
    },
    /* CBUISpec_toDescription() */

};
/* SCProductBuyViewEditor */
