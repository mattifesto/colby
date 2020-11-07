"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItem */
/* global
    CBAjax,
    CBConvert,
    CBException,
    CBModel,
    CBView,
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
        let callable = CBModel.getClassFunction(
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
        let callable = CBModel.getClassFunction(
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
        return CBAjax.call(
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
        let callable = CBModel.getClassFunction(
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
                "isNotAvailable"
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
        let callable = CBModel.getClassFunction(
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
    getOriginalSubtotalInCents(
        cartItemModel
    ) {
        let quantity = SCCartItem.getQuantity(
            cartItemModel
        );

        if (quantity === 0) {
            return 0;
        }

        let originalSubtotalInCents;

        let callable = CBModel.getClassFunction(
            cartItemModel,
            "SCCartItem_getOriginalSubtotalInCents"
        );

        if (callable !== undefined) {
            originalSubtotalInCents = CBConvert.valueAsInt(
                callable(
                    cartItemModel
                )
            );
        }

        if (originalSubtotalInCents === undefined) {
            originalSubtotalInCents = CBModel.valueAsInt(
                cartItemModel,
                "SCCartItem_originalSubtotalInCents"
            );
        }

        if (originalSubtotalInCents === undefined) {
            let originalUnitPriceInCents = (
                SCCartItem.getOriginalUnitPriceInCents(
                    cartItemModel
                )
            );

            originalSubtotalInCents = Math.ceil(
                quantity * originalUnitPriceInCents
            );
        }

        if (originalSubtotalInCents < 0) {
            let originalSubtotalInCentsAsJSON = JSON.stringify(
                originalSubtotalInCents
            );

            let message = CBConvert.stringToCleanLine(`

                This cart item model has an invalid original subtotal in cents
                of "${originalSubtotalInCentsAsJSON}"

            `);

            throw CBException.withValueRelatedError(
                Error(message),
                cartItemModel,
                "0ac8ccfb1d04da7ddcdbf89640e588de48917c3b"
            );
        }

        return originalSubtotalInCents;
    },
    /* getOriginalSubtotalInCents() */



    /**
     * @param object cartItemModel
     *
     * @return int
     */
    getOriginalUnitPriceInCents(
        cartItemModel
    ) {
        let originalUnitPriceInCents;

        let callable = CBModel.getClassFunction(
            cartItemModel,
            "SCCartItem_getOriginalUnitPriceInCents"
        );

        if (callable !== undefined) {
            originalUnitPriceInCents = CBConvert.valueAsInt(
                callable(
                    cartItemModel
                )
            );
        } else {
            originalUnitPriceInCents = CBModel.valueAsInt(
                cartItemModel,
                "SCCartItem_originalUnitPriceInCents"
            );
        }

        if (originalUnitPriceInCents === undefined) {
            originalUnitPriceInCents = SCCartItem.getUnitPriceInCents(
                cartItemModel
            );
        }

        if (originalUnitPriceInCents <= 0) {
            let originalUnitPriceInCentsAsJSON = JSON.stringify(
                originalUnitPriceInCents
            );

            let message = CBConvert.stringToCleanLine(`

                This cart item has an invalid original unit price of
                "${originalUnitPriceInCentsAsJSON}"

            `);

            throw CBException.withValueRelatedError(
                Error(message),
                cartItemModel,
                "363b7914a1a1d9adec962ef043c89f1495d4eb74"
            );
        }

        return originalUnitPriceInCents;
    },
    /* getOriginalUnitPriceInCents() */



    /**
     * @deprecated 2020_09_08
     *
     *      Use SCCartItem.getSubtotalInCents()
     */
    getPriceInCents(
        cartItemModel
    ) {
        return SCCartItem.getSubtotalInCents(
            cartItemModel
        );
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
    getQuantity(
        cartItemModel
    ) {
        let quantity;

        let callable = CBModel.getClassFunction(
            cartItemModel,
            "SCCartItem_getQuantity"
        );

        if (callable !== undefined) {
            quantity = CBConvert.valueAsNumber(
                callable(
                    cartItemModel
                )
            );
        }

        if (quantity === undefined) {
            quantity = CBModel.valueAsInt(
                cartItemModel,
                "SCCartItem_quantity"
            );
        }

        if (quantity === undefined) {
            /* quantity */
            quantity = CBModel.valueAsInt(
                cartItemModel,
                "quantity"
            );
        }

        if (quantity === undefined) {
            quantity = 0;
        }

        if (quantity < 0) {
            let quantityAsJSON = JSON.stringify(
                quantity
            );

            let message = CBConvert.stringToCleanLine(`

                This cart item model has an invalid quantity of
                ${quantityAsJSON}

            `);

            throw CBException.withValueRelatedError(
                Error(message),
                cartItemModel,
                "e18433a4d2739c7c3a707fa04b9a899cd4e70f68"
            );
        }

        return quantity;
    },
    /* getQuantity() */



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
        let callable = CBModel.getClassFunction(
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
     * @param object cartItemModel
     *
     * @return int
     */
    getSubtotalInCents(
        cartItemModel
    ) {
        let quantity = SCCartItem.getQuantity(
            cartItemModel
        );

        if (quantity === 0) {
            return 0;
        }

        let subtotalInCents;

        let callable = CBModel.getClassFunction(
            cartItemModel,
            "SCCartItem_getSubtotalInCents"
        );

        if (callable !== undefined) {
            subtotalInCents = CBConvert.valueAsInt(
                callable(
                    cartItemModel
                )
            );
        }

        if (subtotalInCents === undefined) {
            subtotalInCents = CBModel.valueAsInt(
                cartItemModel,
                "SCCartItem_subtotalInCents"
            );
        }

        if (subtotalInCents === undefined) {
            /* deprecated */
            subtotalInCents = CBModel.valueAsInt(
                cartItemModel,
                "priceInCents"
            );
        }

        if (subtotalInCents === undefined) {
            let unitPriceInCents = SCCartItem.getUnitPriceInCents(
                cartItemModel
            );

            subtotalInCents = Math.ceil(
                quantity * unitPriceInCents
            );
        }

        if (subtotalInCents < 0) {
            let subtotalInCentsAsJSON = JSON.stringify(
                subtotalInCents
            );

            let message = CBConvert.stringToCleanLine(`

                This cart item model has an invalid subtotal in cents of
                "${subtotalInCentsAsJSON}"

            `);

            throw CBException.withValueRelatedError(
                Error(message),
                cartItemModel,
                "e26349b3e3092338ffe5d57a7c85e10277036027"
            );
        }

        return subtotalInCents;
    },
    /* getSubtotalInCents() */



    /**
     * @param object cartItemSpec
     *
     * @return string
     *
     *      If the cart item class has not implemented SCCartItem_getTitle()
     *      this function will return the string value of the "title" property.
     */
    getTitle(
        cartItemSpec
    ) {
        let callable = CBModel.getClassFunction(
            cartItemSpec,
            "SCCartItem_getTitle"
        );

        if (callable !== undefined) {
            return callable(cartItemSpec);
        } else {
            return CBModel.valueToString(cartItemSpec, "title");
        }
    },
    /* getTitle() */



    /**
     * @param object cartItemModel
     *
     * @return int
     */
    getUnitPriceInCents(
        cartItemModel
    ) {
        let unitPriceInCents;

        let callable = CBModel.getClassFunction(
            cartItemModel,
            "SCCartItem_getUnitPriceInCents"
        );

        if (callable !== undefined) {
            unitPriceInCents = CBConvert.valueAsInt(
                callable(
                    cartItemModel
                )
            );
        } else {
            unitPriceInCents = CBModel.valueAsInt(
                cartItemModel,
                "SCCartItem_unitPriceInCents"
            );

            if (unitPriceInCents === undefined) {
                /* deprecated */
                unitPriceInCents = CBModel.valueAsInt(
                    cartItemModel,
                    "unitPriceInCents"
                );
            }
        }

        if (
            unitPriceInCents === undefined ||
            unitPriceInCents <= 0
        ) {
            let unitPriceInCentsAsJSON = JSON.stringify(
                unitPriceInCents
            );

            let message = CBConvert.stringToCleanLine(`

                This cart item has an invalid unit price of
                "${unitPriceInCentsAsJSON}"

            `);

            throw CBException.withValueRelatedError(
                Error(message),
                cartItemModel,
                "eb1465850e5a201b8d33006b79bfbe4be54482ce"
            );
        }

        return unitPriceInCents;
    },
    /* getUnitPriceInCents() */



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
        let callable = CBModel.getClassFunction(
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
        let callable = CBModel.getClassFunction(
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
