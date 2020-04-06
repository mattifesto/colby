"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBConvert,
    CBUI,
    CBUIStringEditor,
    Colby,
    SCShoppingCart,
*/




(function () {



    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return void
     */
    function afterDOMContentLoaded() {
        let viewElements = document.getElementsByClassName(
            "SCFreeFormBuyView"
        );

        for (let index = 0; index < viewElements.length; index += 1) {
            let viewElement = viewElements[index];

            viewElement.appendChild(
                createViewContentElement()
            );
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createViewContentElement() {
        let currentAmountInCents;

        let elements = CBUI.createElementTree(
            "CBUI_viewContent",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        /* amount */

        let amountEditor = CBUIStringEditor.create();
        amountEditor.title = "Amount";

        sectionElement.appendChild(
            amountEditor.element
        );

        amountEditor.changed = function () {
            let valueWithoutLeadingDollarSign = amountEditor.value.replace(
                /\s*\$/,
                ""
            );

            currentAmountInCents = CBConvert.dollarsAsCents(
                valueWithoutLeadingDollarSign
            );

            if (currentAmountInCents === undefined) {
                buttonElement.classList.add("CBUI_button1_disabled");

                if (valueWithoutLeadingDollarSign.trim() === "") {
                    amountEditor.title = "Amount";
                } else {
                    amountEditor.title = "Amount (invalid)";
                }
            } else {
                buttonElement.classList.remove("CBUI_button1_disabled");

                let amountInDollars = CBConvert.centsToDollars(
                    currentAmountInCents
                );

                amountEditor.title = `Amount ($${amountInDollars})`;
            }
        };


        /* descriptionAsText */

        let descriptionEditor = CBUIStringEditor.create();
        descriptionEditor.title = "Description";

        sectionElement.appendChild(
            descriptionEditor.element
        );


        /* button */

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1 CBUI_button1_disabled"
        );

        element.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];

        buttonElement.textContent = "Add Payment to Cart";

        buttonElement.addEventListener(
            "click",
            function () {
                if (currentAmountInCents === undefined) {
                    return;
                }

                SCShoppingCart.adjustMainCartItemQuantity(
                    {
                        className: "SCFreeFormCartItem",

                        descriptionAsText: descriptionEditor.value,

                        unitPriceInCents: currentAmountInCents,

                        productCBID: Colby.random160(),

                        title: "Payment",
                    },
                    1
                ).then(
                    function () {
                        window.location.href = "/view-cart/";
                    }
                );
            }
        );


        /* view cart */

        element.appendChild(
            createViewCartElement()
        );

        return element;
    }
    /* createContentElement() */



    /**
     * @return Element
     */
    function createViewCartElement() {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section",
            [
                "CBUI_action",
                "a",
            ]
        );

        let anchorElement = elements[2];
        anchorElement.textContent = "View Cart >";
        anchorElement.href = "/view-cart/";

        return elements[0];
    }
    /* createViewCartElement() */

})();
