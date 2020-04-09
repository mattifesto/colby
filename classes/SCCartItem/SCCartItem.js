"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItem */
/* global
    CBConvert,
    CBException,
    CBModel,
    CBView,
    Colby,
*/



var SCCartItem = {

    /* -- functions -- -- -- -- -- */



    /**
     * This function will clean and consolidate an array of cart item models.
     *
     * @param [mixed] cartItems
     *
     *      The cart items will be modified and discarded without notification.
     *      The caller of this function should ensure that the cart items passed
     *      are free to be modified.
     *
     * @return [object]
     */
    cleanAndConsolidateCartItems(
        cartItems
    ) {
        let cleanedCartItems = [];

        cartItems.forEach(
            function (currentCartItem) {
                /**
                 * Don't add cart items that are not models to the array of
                 * cleaned cart items.
                 */

                currentCartItem = CBConvert.valueAsModel(
                    currentCartItem
                );

                if (currentCartItem === undefined) {
                    return;
                }


                /**
                 * Don't add cart items that are not available to the array of
                 * cleaned cart items.
                 */

                let isNotAvailable = SCCartItem.getIsNotAvailable(
                    currentCartItem
                );

                if (isNotAvailable) {
                    return;
                }


                /**
                 * Don't add cart itmes with a quantity of zero to the array of
                 * cleaned cart items.
                 */

                let currentCartItemQuantity = SCCartItem.getQuantity(
                    currentCartItem
                );

                if (currentCartItemQuantity === 0) {
                    return;
                }


                /**
                 * Check to see if there is already a cart item spec that
                 * matches this cart item spec in the array of cleaned cart
                 * items.
                 */

                let cleanedCartItem = SCCartItem.findCartItemSpec(
                    cleanedCartItems,
                    currentCartItem
                );

                if (cleanedCartItem === undefined) {
                    /**
                     * Add this cart item to the array of cleaned cart items.
                     */

                    cleanedCartItems.push(
                        currentCartItem
                    );
                } else {
                    /**
                     * Increase the quantity of the mathing cart item already in
                     * the array of clean cart items by the quantity of this
                     * cart item.
                     */

                    let cleanedCartItemQuantity = SCCartItem.getQuantity(
                        cleanedCartItem
                    );

                    let mergedCartItemQuantity = (
                        cleanedCartItemQuantity +
                        currentCartItemQuantity
                    );

                    SCCartItem.setQuantity(
                        cleanedCartItem,
                        mergedCartItemQuantity
                    );
                }
            }
        );
        /* cartItems.forEach */

        return cleanedCartItems;
    },
    /* cleanAndConsolidateCartItems() */



    /**
     * @param object cartItemModel
     *
     * @return Element
     */
    createCheckoutViewElement: function (cartItemModel) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_createCheckoutViewElement"
        );

        if (callable !== undefined) {
            return callable(cartItemModel);
        } else {
            return CBView.create(
                {
                    className: "SCCartItemOrderView",
                    cartItemModel: cartItemModel,
                }
            ).element;
        }
    },



    /**
     * @param object cartItemModel
     *
     * @return Element
     */
    createOrderViewElement: function (cartItemModel) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_createOrderViewElement"
        );

        if (callable !== undefined) {
            return callable(cartItemModel);
        } else {
            return CBView.create(
                {
                    className: "SCCartItemOrderView",
                    cartItemModel: cartItemModel,
                }
            ).element;
        }
    },



    /**
     * This function will return an updated cart item spec. If no updated cart
     * item spec is returned from the server, an Error will be thrown.
     *
     * @param object originalCartItemSpec
     *
     * @return Promise -> object
     */
    fetchUpdatedCartItemSpec: function (originalCartItemSpec) {
        return Colby.callAjaxFunction(
            "SCCartItem",
            "updateSpecs",
            {
                originalCartItemSpecs: [
                    originalCartItemSpec
                ],
            }
        ).then(
            function (updatedCartItemSpecs) {
                return updatedCartItemSpecs[0];
            }
        );
    },
    /* fetchUpdatedCartItemSpec() */



    /**
     * @param [object] cartItemSpecs
     *
     * @param object cartItemSpec
     *
     * @return object|undefined
     */
    findCartItemSpec: function (cartItemSpecs, cartItemSpec) {
        return cartItemSpecs.find(
            function (currentCartItemSpec) {
                return SCCartItem.specsRepresentTheSameProduct(
                    currentCartItemSpec,
                    cartItemSpec
                );
            }
        );
    },
    /* findCartItemSpec() */



    /**
     * @param object cartItemModel
     *
     * @return bool
     */
    getIsNotAvailable(
        cartItemModel
    ) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_getIsNotAvailable"
        );

        if (callable !== undefined) {
            return callable(
                cartItemModel
            );
        } else {
            return CBModel.valueToBool(
                cartItemModel,
                'isNotAvailable'
            );
        }
    },
    /* getIsNotAvailable() */



    /**
     * @param object cartItemModel
     *
     * @return int|undefined
     */
    getMaximumQuantity: function (cartItemModel) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_getMaximumQuantity"
        );

        if (callable !== undefined) {
            return callable(cartItemModel);
        } else {
            return CBModel.valueAsInt(
                cartItemModel,
                "maximumQuantity"
            );
        }
    },
    /* getMaximumQuantity() */



    /**
     * @param object cartItemModel
     *
     * @return int
     */
    getPriceInCents: function (cartItemModel) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_getPriceInCents"
        );

        if (callable !== undefined) {
            return callable(cartItemModel);
        } else {
            return CBModel.valueAsInt(
                cartItemModel,
                "priceInCents"
            ) || 0;
        }
    },
    /* getPriceInCents() */



    /**
     * @param object cartItemModel
     *
     * @return number
     *
     *      The default implementation assumes that the quantity is an integer
     *      in the "quantity" property. If the "quantity" property is undefined
     *      or is not an integer, this function returns 1.
     */
    getQuantity: function (cartItemModel) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_getQuantity"
        );

        if (callable !== undefined) {
            return callable(cartItemModel);
        } else {
            let quantity = CBModel.valueAsInt(cartItemModel, "quantity");

            if (quantity === undefined || quantity < 0) {
                return 0;
            } else {
                return quantity;
            }
        }
    },



    /**
     * The source URL is a root-relative URL that links to the page that most
     * represents the cart item. Some cart items will not have a source URL.
     *
     * @param object cartItemModel
     *
     * @return string
     *
     *      If a source URL is available it will be returned. If a source URL is
     *      not available an empty string will be returned.
     */
    getSourceURL: function (cartItemModel) {
        let callable = CBModel.classFunction(
            cartItemModel,
            "SCCartItem_getSourceURL"
        );

        if (callable !== undefined) {
            return callable(cartItemModel);
        } else {
            return CBModel.valueToString(cartItemModel, "sourceURL");
        }
    },



    /**
     * @param object cartItemSpec
     *
     * @return string
     *
     *      If the cart item class has not implemented SCCartItem_getTitle()
     *      this function will return the string value of the "title" property.
     */
    getTitle: function (cartItemSpec) {
        let callable = CBModel.classFunction(
            cartItemSpec,
            "SCCartItem_getTitle"
        );

        if (callable !== undefined) {
            return callable(cartItemSpec);
        } else {
            return CBModel.valueToString(cartItemSpec, "title");
        }
    },



    /**
     * Calculate the total quantity of items for an array of cart item models.
     *
     * @param array cartItemModels
     *
     *      This parameter can be an array of cart item specs or models.
     *
     * @return number
     */
    itemsToQuantity: function (cartItemModels) {
        return cartItemModels.reduce(
            function (accumulatedQuantity, cartItemModel) {
                return (
                    accumulatedQuantity +
                    SCCartItem.getQuantity(cartItemModel)
                );
            },
            0
        );
    },



    /**
     * @param object cartItemSpec
     * @param number quantity
     *
     * @return undefined
     */
    setQuantity: function (cartItemSpec, quantity) {
        let callable = CBModel.classFunction(
            cartItemSpec,
            "SCCartItem_setQuantity"
        );

        if (callable !== undefined) {
            return callable(cartItemSpec, quantity);
        } else {
            SCCartItem.setQuantity_defaultImplementation(
                cartItemSpec,
                quantity
            );
        }
    },
    /* setQuantity() */



    /**
     * This function is used as the default implementation of the
     * SCCartItem_setQuantity() interface by SCCartItem.setQuantity() for cart
     * items whose class doesn't have the interface implemented. It should not
     * be used generally, but it may be used by a cart item class that has the
     * interface implemented to set the quantity in the default way (by calling
     * this function) and then does additional things.
     *
     * @param object cartItemSpec
     * @param number quantity
     *
     * @return int
     *
     *      Returns the quantity set as the value for the "quantity" property.
     */
    setQuantity_defaultImplementation: function (
        cartItemSpec,
        requestedQuantity
    ) {
        let quantity = CBConvert.valueAsInt(
            requestedQuantity
        );

        if (quantity === undefined) {
            throw new TypeError(
                "The quantity parameter must be an integer for the " +
                "default implementation."
            );
        }

        if (quantity < 0) {
            quantity = 0;
        } else {
            let maximumQuantity = SCCartItem.getMaximumQuantity(
                cartItemSpec
            );

            if (
                maximumQuantity !== undefined &&
                quantity > maximumQuantity
            ) {
                quantity = maximumQuantity;
            }
        }

        cartItemSpec.quantity = quantity;

        return quantity;
    },
    /* setQuantity_defaultImplementation() */



    /**
     * All cart item classes are required to implement the
     * SCCartItem_specsRepresentTheSameProduct() interface. Even a singular
     * unique item should report that it represents the same product as itself.
     * This could be done using a random CBID as a product code or another
     * similar method.
     *
     * Singular unique items can still have their quantity switched between 0
     * and 1 while they are in a customer's shopping cart.
     *
     * @TODO
     *
     *      Rename this function, it works for models too.
     *
     * @param object cartItemModelA
     * @param object cartItemModelB
     *
     * @return bool
     *
     *      Returns true if the cart items are representing the same exact
     *      product.
     */
    specsRepresentTheSameProduct(
        cartItemModelA,
        cartItemModelB
    ) {
        let callable = CBModel.classFunction(
            cartItemModelA,
            "SCCartItem_specsRepresentTheSameProduct"
        );

        /**
         * All cart item classes are reu
         */
        if (callable === undefined) {
            throw CBException.withValueRelatedError(
                Error(
                    "This cart item class has not implemented the " +
                    "SCCartItem_specsRepresentTheSameProduct() interface."
                ),
                cartItemModelA,
                "28eebc9d294790541820acc62534a01a2711946f"
            );
        }

        let classNameA = CBModel.valueToString(
            cartItemModelA,
            "className"
        );

        let classNameB = CBModel.valueToString(
            cartItemModelB,
            "className"
        );

        if (classNameA === classNameB) {
            return callable(
                cartItemModelA,
                cartItemModelB
            );
        } else {
            return false;
        }
    },
    /* specsRepresentTheSameProduct() */

};
/* SCCartItem */