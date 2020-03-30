"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartMenuItem */
/* global
    Colby,
    SCCartItem,
    SCShoppingCart,
*/

var SCCartMenuItem = {

    /**
     * @param Element element
     *
     * @return undefined
     */
    activateElement: function (element) {
        let anchorElement = document.createElement("a");

        element.appendChild(anchorElement);

        anchorElement.href = "/view-cart/";

        SCShoppingCart.mainCartItemSpecs.addEventListener(
            "somethingChanged",
            function activateElement_mainCartItemSpecsChanged() {
                activateElement_updateQuantity();
            }
        );

        activateElement_updateQuantity();

        return;

        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function activateElement_updateQuantity() {
            let quantityOfItemsInCart = SCCartItem.itemsToQuantity(
                SCShoppingCart.mainCartItemSpecs.getCartItems()
            );

            if (quantityOfItemsInCart > 0) {
                anchorElement.textContent = (
                    "Cart (" +
                    quantityOfItemsInCart +
                    ")"
                );
            } else {
                anchorElement.textContent = "Cart";
            }
        }
        /* activateElement_updateQuantity() */
    },
    /* activateElement() */
};
/* SCCartMenuItem */


Colby.afterDOMContentLoaded(
    function SCCartMenuItem_afterDOMContentLoaded() {
        let elements = document.getElementsByClassName("SCCartMenuItem");

        for (let index = 0; index < elements.length; index += 1) {
            SCCartMenuItem.activateElement(
                elements.item(index)
            );
        }
    }
);
