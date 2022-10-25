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
        // anchor

        let anchorElement =
        document.createElement(
            "a"
        );

        element.appendChild(
            anchorElement
        );



        // cart symbol

        let cartSymbolElement =
        document.createElement(
            "span"
        );

        cartSymbolElement.className =
        "CB_MaterialSymbols_characters";

        cartSymbolElement.textContent =
        "shopping_bag";

        anchorElement.append(
            cartSymbolElement
        );



        // quantity

        let quantityElement =
        document.createElement(
            "span"
        );

        quantityElement.style.fontSize =
        "80%";

        quantityElement.style.position =
        "relative";

        quantityElement.style.bottom =
        "3px";

        anchorElement.append(
            quantityElement
        );

        anchorElement.href =
        "/view-cart/";



        SCShoppingCart.mainCartItemSpecs.addEventListener(
            "somethingChanged",
            function
            activateElement_mainCartItemSpecsChanged(
            ) // -> undefined
            {
                activateElement_updateQuantity();
            }
        );

        activateElement_updateQuantity();




        /**
         * @return undefined
         */
        function
        activateElement_updateQuantity(
        ) // -> undefined
        {
            let quantityOfItemsInCart =
            SCCartItem.itemsToQuantity(
                SCShoppingCart.mainCartItemSpecs.getCartItems()
            );

            if (
                quantityOfItemsInCart >
                0
            ) {
                quantityElement.textContent =
                ` (${quantityOfItemsInCart})`;
            }

            else
            {
                quantityElement.textContent =
                "";
            }
        }
        /* activateElement_updateQuantity() */

    }
    /* SCCartMenuItem_activateElement() */


    Colby.afterDOMContentLoaded(
        function
        SCCartMenuItem_afterDOMContentLoaded(
        ) // -> undefined
        {
            let elements =
            document.getElementsByClassName(
                "SCCartMenuItem"
            );

            for (
                let index = 0;
                index < elements.length;
                index += 1
            ) {
                SCCartMenuItem_activateElement(
                    elements.item(
                        index
                    )
                );
            }
        }
    );

})();
