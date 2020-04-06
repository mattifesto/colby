"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItemOrderView */
/* global
    CBUI,
    SCCartItemCartView,
*/

var SCCartItemOrderView = {

    /* -- CBView interfaces -- -- -- -- -- */

    /**
     * @param object model
     *
     *      {
     *          cartItemModel: object
     *      }
     *
     * @return object
     *
     *      {
     *          element: Element
     *      }
     */
    CBView_create: function (model) {
        let cartItemModel = model.cartItemModel;
        model = undefined;

        /* view element */

        let element = CBUI.createElement(
            "SCCartItemOrderView CBUI_section_container"
        );

        /* section element */

        let sectionElement = CBUI.createElement("CBUI_section");

        element.appendChild(sectionElement);

        /* quantity, title, and price section item element */

        sectionElement.appendChild(
            SCCartItemCartView.
            cartItemSpecToQuantityTitleAndPriceSectionItemElement(
                cartItemModel
            )
        );

        /* image section item element */

        sectionElement.appendChild(
            SCCartItemCartView.cartItemSpecToImageSectionItemElement(
                cartItemModel
            )
        );

        /* message section item element */

        sectionElement.appendChild(
            SCCartItemCartView.cartItemSpecToMessageSectionItemElement(
                cartItemModel
            )
        );

        return {
            element: element,
        };
    },
};
