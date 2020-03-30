"use strict";
/* jshint strict: global */
/* jshint esversion: 9 */
/* exported SCShoppingCartTests */
/* global
    CBActiveObject,
    CBModel,
    CBModels,
    CBTest,
    SCCartItem,
    SCShoppingCart,
*/




var SCShoppingCartTests = {

    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    CBTest_addJunkItemsToMainCart: function () {
        let mainShoppingCartID = "a2adb1ceff0339fc399e5816ff163e5b5ca2d627";
        let mainShoppingCartSpec;
        let mainShoppingCartVersion;

        let record = CBModels.fetch(
            mainShoppingCartID,
            localStorage
        );

        if (record === undefined) {
            mainShoppingCartSpec = {};
            mainShoppingCartVersion = 0;
        } else {
            mainShoppingCartSpec = record.spec;
            mainShoppingCartVersion = record.meta.version;
        }

        CBModel.merge(
            mainShoppingCartSpec,
            {
                className: "SCShoppingCart",
            }
        );

        mainShoppingCartSpec.cartItems = CBModel.valueToArray(
            mainShoppingCartSpec,
            "cartItems"
        ).concat(
            [
                5,
                "wrong",
                {
                    className: "bad_item_quantity_0",
                    data: 42,
                },
                {
                    className: "bad_item_quantity_1",
                    data: 43,
                    quantity: 1,
                },
                undefined,
                undefined,
                null,
                Error("This is an error!"),
                true,
            ]
        );

        CBModels.save(
            mainShoppingCartID,
            mainShoppingCartSpec,
            mainShoppingCartVersion,
            localStorage
        );

        return {
            succeeded: true,
        };
    },
    /* CBTest_addJunkItemsToMainCart() */



    /**
     * @return Promise -> object
     */
    async CBTest_mainCartItemQuantity() {
        let cartItem = {
            className: "SCProductCartItem",
            productCode: "SCShoppingCartTests_adjustMainCartItemQuantity",
        };

        let tests = [
            {
                quantity: 0,
                action: "set",
                expectedQuantity: 0
            },
            {
                quantity: 1,
                action: "set",
                expectedQuantity: 1
            },
            {
                quantity: 2,
                action: "adjust",
                expectedQuantity: 3,
            },
            {
                quantity: -1,
                action: "adjust",
                expectedQuantity: 2,
            },
            {
                quantity: Number.MIN_SAFE_INTEGER,
                action: "adjust",
                expectedQuantity: 0,
            },
        ];

        for (
            let testIndex = 0;
            testIndex < tests.length;
            testIndex += 1
        ) {
            let resolution = await runTestAtIndex(testIndex);

            if (resolution) {
                return resolution;
            }
        }

        return {
            succeeded: true,
        };



        /* -- closures -- -- -- -- -- */



        /**
         * @param int testIndex
         *
         * @return Promise|object
         */
        function runTestAtIndex(testIndex) {
            let promise;
            let test = tests[testIndex];

            if (test.action === "set") {
                promise = SCShoppingCart.setMainCartItemQuantity(
                    cartItem,
                    test.quantity
                );
            } else if (test.action === "adjust") {
                promise = SCShoppingCart.adjustMainCartItemQuantity(
                    cartItem,
                    test.quantity
                );
            } else {
                return CBTest.valueIssueFailure(
                    `test index ${testIndex}`,
                    test,
                    "The test action is not supported"
                );
            }

            return promise.then(
                function () {
                    let actualQuantity =
                    SCShoppingCart.getMainCartItemQuantity(
                        cartItem
                    );

                    if (actualQuantity !== test.expectedQuantity) {
                        return CBTest.resultMismatchFailure(
                            `test index ${testIndex}`,
                            actualQuantity,
                            test.expectedQuantity
                        );
                    }
                }
            );
        }
        /* runTestAtIndex() */

    },
    /* CBTest_mainCartItemQuantity() */



    /**
     * @return object|Promise
     */
    CBTest_empty: function () {
        let cartItems = [
            {
                className: "SCProductCartItem",
                productCode: "SCShoppingCartTests_0001",
                quantity: 1,
            },
            {
                className: "SCProductCartItem",
                productCode: "SCShoppingCartTests_0002",
                quantity: 2,
            },
            {
                className: "SCProductCartItem",
                productCode: "SCShoppingCartTests_0003",
                quantity: -3.3,
            },
        ];

        cartItems.forEach(
            function (currentCartItem) {
                CBActiveObject.activate(currentCartItem);
            }
        );

        SCShoppingCart.empty(cartItems);


        /* array length */
        {
            let actualLength = cartItems.length;
            let expectedLength = 3;

            if (actualLength !== expectedLength) {
                return CBTest.resultMismatchFailure(
                    "array length",
                    actualLength,
                    expectedLength
                );
            }
        }
        /* array length */


        /* quantity */
        {
            for (let index = 0; index < cartItems.length; index += 1) {
                let currentCartItem = cartItems[index];

                let actualQuantity = SCCartItem.getQuantity(
                    currentCartItem
                );

                let expectedQuantity = 0;

                if (actualQuantity !== expectedQuantity) {
                    return CBTest.resultMismatchFailure(
                        `quantity of cart item at index ${index}`,
                        actualQuantity,
                        expectedQuantity
                    );
                }
            }
            /* for */
        }
        /* quantity */


        return {
            succeeded: true,
        };
    },
    /* CBTest_empty() */



    /**
     * @return object|Promise
     */
    CBTest_mainCartItemSpecs: function () {
        return new Promise(
            CBTest_mainCartItemSpecs_run
        );

        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function CBTest_mainCartItemSpecs_run(resolve, reject) {
            let cartItem = {
                className: "SCProductCartItem",
                productCode: "SCShoppingCartTests_unknown",
                quantity: 1,
            };

            /**
             * The following timeout exists so that the test will fail if it
             * takes more than 10 seconds to run.
             */

            let timeoutID = setTimeout(
                function () {
                    let error = Error("The test took to long to run.");

                    reject(error);
                },
                10000
            );

            let activeCartItem =
            SCShoppingCart
            .mainCartItemSpecs
            .fetchCartItem(cartItem);

            SCCartItem.setQuantity(activeCartItem, 5);

            activeCartItem
            .CBActiveObject
            .tellListenersThatTheObjectDataHasChanged();

            /**
             * Once the cart has saved the test is done.
             */
            SCShoppingCart.mainCartSavePromise.then(
                function () {
                    if (timeoutID) {
                        clearTimeout(timeoutID);
                    }

                    let activeCartItem =
                    SCShoppingCart
                    .mainCartItemSpecs
                    .fetchCartItem(cartItem);

                    let actualQuantity = SCCartItem.getQuantity(
                        activeCartItem
                    );

                    let expectedQuantity = 5;

                    let testResult;

                    if (actualQuantity === expectedQuantity) {
                        testResult = {
                            succeeded: true,
                        };

                        SCShoppingCart.adjustMainCartItemQuantity(
                            cartItem,
                            Number.MIN_SAFE_INTEGER
                        ).then(
                            function () {
                                resolve(testResult);
                            }
                        );
                    } else {
                        testResult = CBTest.resultMismatchFailure(
                            'quantity',
                            actualQuantity,
                            expectedQuantity
                        );

                        resolve(testResult);
                    }
                }
            );
        }
        /* CBTest_mainCartItemSpecs_run() */

    }
    /* CBTest_mainCartItemSpecs() */

};
/* SCShoppingCartTests */
