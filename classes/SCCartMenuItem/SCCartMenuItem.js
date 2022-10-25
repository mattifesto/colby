/* global
    Colby,
    SCCartItem,
    SCShoppingCart,
*/

(function()
{
    "use strict";


    /**
     * @param Element element
     *
     * @return undefined
     */
    function
    SCCartMenuItem_activateElement(
        element
    ) // -> undefined
    {
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
    }
    /* SCCartMenuItem_activateElement() */


    Colby.afterDOMContentLoaded(
        function SCCartMenuItem_afterDOMContentLoaded() {
            let elements = document.getElementsByClassName("SCCartMenuItem");

            for (let index = 0; index < elements.length; index += 1) {
                SCCartMenuItem_activateElement(
                    elements.item(index)
                );
            }
        }
    );

})();
