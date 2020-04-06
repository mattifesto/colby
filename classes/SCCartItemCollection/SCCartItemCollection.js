"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItemCollection */
/* global
    CBActiveObject,
    CBConvert,
    CBEvent,
    CBException,
    CBModel,
    SCCartItem,
*/

var SCCartItemCollection = {

    /**
     * @return object
     *
     *      {
     *          addEventListener(type, callback)
     *          fetchCartItem(originalCartItem)
     *          getCartItems() -> [object]
     *          replaceCartItems([object])
     *      }
     */
    create: function () {
        let cartItems = [];

        let anItemWasAddedEvent = CBEvent.create();
        let somethingChangedEvent = CBEvent.create();

        let pod = {
            addEventListener: create_addEventListener,
            fetchCartItem: create_fetchCartItem,
            getCartItems: function() {
                return cartItems.slice();
            },
            replaceCartItems: create_replaceCartItems,
        };

        return pod;


        /* -- closures -- -- -- -- -- */

        /**
         * @param string type
         * @param function callback
         *
         * @return undefined
         */
        function create_addEventListener(type, callback) {
            switch (type) {
                case "anItemWasAdded":
                    anItemWasAddedEvent.addListener(callback);
                    break;

                case "somethingChanged":
                    somethingChangedEvent.addListener(callback);
                    break;

                default:
                    throw CBException.withError(
                        Error(
                            "The event type \"" +
                            type +
                            "\" is not a valid SCCartItemCollection event type."
                        ),
                        "",
                        "baf266926a2fbfb4c1341293f366f8400f048be4"
                    );
            }
        }
        /* create_addEventListener() */


        /**
         * @param object cartItem
         *
         * @return undefined
         */
        function create_addCartItem(cartItem) {
            CBActiveObject.activate(cartItem);

            cartItems.push(cartItem);

            cartItem.CBActiveObject.addEventListener(
                "theObjectDataHasChanged",
                function () {
                    somethingChangedEvent.dispatch();
                }
            );

            cartItem.CBActiveObject.addEventListener(
                "theObjectHasBeenReplaced",
                function (replacementCartItem) {
                    let index = cartItems.indexOf(cartItem);
                    cartItems[index] = replacementCartItem;
                    cartItem = replacementCartItem;

                    somethingChangedEvent.dispatch();
                }
            );

            cartItem.CBActiveObject.addEventListener(
                "theObjectHasBeenDeactivated",
                function () {
                    let index = cartItems.indexOf(cartItem);

                    cartItems.splice(index, 1);

                    somethingChangedEvent.dispatch();
                }
            );

            anItemWasAddedEvent.dispatch(cartItem);
            somethingChangedEvent.dispatch();
        }
        /* create_createCartItem() */


        /**
         * @param object originalCartItem
         *
         * @return object
         */
        function create_fetchCartItem(originalCartItem) {
            if (CBConvert.valueAsModel(originalCartItem) === undefined) {
                throw CBException.withError(
                    TypeError("The originalCartItem parameter must be a spec."),
                    "",
                    "4f83f042ac02f83c0165d6f1520312ae34083e60"
                );
            }

            let foundCartItem = SCCartItem.findCartItemSpec(
                cartItems,
                originalCartItem
            );

            if (foundCartItem !== undefined) {
                return foundCartItem;
            }

            let addedCartItem = CBModel.clone(originalCartItem);

            SCCartItem.setQuantity(addedCartItem, 0);

            create_addCartItem(addedCartItem);

            return addedCartItem;
        }
        /* create_fetchCartItem() */


        /**
         * @param [object] replacementCartItems
         *
         *      The cart items in this array will be the actual cart items used
         *      by the collection so they must not be active yet and must be
         *      available for use.
         *
         *      If you specify more than one cart item in this array that
         *      represent the same product then each cart item will replace the
         *      one before it so the last cart item in the array for the product
         *      will be the cart item used.
         *
         *      Callers should clean and consolidate the array of replacement
         *      cart items before calling this function. All of the cart items
         *      must be models or an error will be thrown and the ajustment may
         *      be only partially applied.
         *
         * @return undefined
         */
        function create_replaceCartItems(replacementCartItems) {
            let originalCartItems = cartItems.slice();

            replacementCartItems.forEach(
                function (currentReplacentCartItem) {
                    let foundCartItem = SCCartItem.findCartItemSpec(
                        cartItems,
                        currentReplacentCartItem
                    );

                    if (foundCartItem) {
                        foundCartItem.CBActiveObject.replace(
                            currentReplacentCartItem
                        );
                    } else {
                        create_addCartItem(currentReplacentCartItem);
                    }
                }
            );
            /* replacementCartItems.forEach */

            originalCartItems.forEach(
                function (currentOriginalCartItem) {
                    let index = cartItems.indexOf(currentOriginalCartItem);

                    /**
                     * If the original cart item is still in the array of cart
                     * items then it was not replaced, which means it was
                     * effectively removed, so its quantity should be set to 0.
                     */
                    if (index >= 0) {
                        SCCartItem.setQuantity(currentOriginalCartItem, 0);

                        currentOriginalCartItem
                        .CBActiveObject
                        .tellListenersThatTheObjectDataHasChanged();
                    }
                }
            );
            /* originalCartItems.forEach */
        }
        /* create_replaceCartItems */
    },
    /* create() */
};
