"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItemCollectionTests */
/* global
    CBConvert,
    CBModel,
    CBTest,
    SCCartItem,
    SCCartItemCollection,
*/

var SCCartItemCollectionTests = {

    /**
     * @return object|Promise
     */
    CBTest_fetchCartItem: function () {
        let collection = SCCartItemCollection.create();

        /* test: empty parameter to fetchCartItem() */
        {
            let actualSourceID = "no error was thrown";
            let expectedSourceID = "4f83f042ac02f83c0165d6f1520312ae34083e60";

            try {
                collection.fetchCartItem();
            } catch (error) {
                actualSourceID = CBModel.valueAsID(
                    error,
                    "CBException.sourceID"
                );
            }

            if (actualSourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "empty parameter to fetchCartItem()",
                    actualSourceID,
                    expectedSourceID
                );
            }
        }
        /* test: empty parameter to fetchCartItem() */


        /* test: item with no class name parameter to fetchCartItem() */
        {
            let actualSourceID = "no error was thrown";
            let expectedSourceID = "4f83f042ac02f83c0165d6f1520312ae34083e60";

            try {
                collection.fetchCartItem(
                    {
                        comment: "this item has no class name",
                    }
                );
            } catch (error) {
                actualSourceID = CBModel.valueAsID(
                    error,
                    "CBException.sourceID"
                );
            }

            if (actualSourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "item with no class name parameter to fetchCartItem()",
                    actualSourceID,
                    expectedSourceID
                );
            }
        }
        /* test: item with no class name parameter to fetchCartItem() */


        /* test: valid fetchCartItem() */
        {
            let originalCartItem = {
                className: "SCProductCartItem",
                productCode: "TEST_1234",
            };

            let item = collection.fetchCartItem(
                originalCartItem
            );

            if (item === originalCartItem) {
                return CBTest.generalFailure(
                    "valid fetchCartItem() 1",
                    `
                        The return value of fetchCartItem() should not be the
                        originalCartItem.
                    `
                );
            }

            if (CBConvert.valueAsModel(item) === undefined) {
                return CBTest.generalFailure(
                    "valid fetchCartItem() 2",
                    `
                        The return value of fetchCartItem() should be a model.
                    `
                );
            }

            {
                let actualResult = CBModel.valueToString(item, "productCode");
                let expectedResult = "TEST_1234";

                if (actualResult !== expectedResult) {
                    return CBTest.resultMismatchFailure(
                        "valid fetchCartItem() 3",
                        actualResult,
                        expectedResult
                    );
                }
            }
        }
        /* test: valid fetchCartItem() */

        return {
            succeeded: true,
        };
    },
    /* CBTest_fetchCartItem() */


    /**
     * @return object|Promise
     */
    CBTest_replaceCartItems: function () {
        let originalCartItems1 = [
            {
                className: "SCProductCartItem",
                productCode: "TEST_1",
                quantity: 1,
            },
            {
                className: "SCProductCartItem",
                productCode: "TEST_2",
                quantity: 2,
            },
            {
                className: "SCProductCartItem",
                productCode: "TEST_3",
                quantity: 3,
            },
        ];

        let originalCartItems2 = [
            {
                className: "SCProductCartItem",
                productCode: "TEST_3",
                quantity: 13,
            },
            {
                className: "SCProductCartItem",
                productCode: "TEST_4",
                quantity: 14,
            },
            {
                className: "SCProductCartItem",
                productCode: "TEST_5",
                quantity: 15,
            },
        ];

        let collection = SCCartItemCollection.create();


        /* test: first replace */
        {
            collection.replaceCartItems(originalCartItems1);

            let currentCartItems = collection.getCartItems();

            {
                let actualResult = currentCartItems.length;
                let expectedResult = 3;

                if (actualResult !== expectedResult) {
                    return CBTest.resultMismatchFailure(
                        "first replace 1",
                        actualResult,
                        expectedResult
                    );
                }
            }

            for (let index = 0; index < 3; index += 1) {
                let actualResult = currentCartItems[index];
                let expectedResult = originalCartItems1[index];

                if (actualResult !== expectedResult) {
                    return CBTest.resultMismatchFailure(
                        "first replace 2",
                        actualResult,
                        expectedResult
                    );
                }
            }
        }
        /* test: first replace */


        /* test: second replace */
        {
            collection.replaceCartItems(originalCartItems2);

            let currentCartItems = collection.getCartItems();

            {
                let actualResult = currentCartItems.length;
                let expectedResult = 5;

                if (actualResult !== expectedResult) {
                    return CBTest.resultMismatchFailure(
                        "second replace 1",
                        actualResult,
                        expectedResult
                    );
                }
            }

            {
                let expectedCartItems = [
                    originalCartItems1[0],
                    originalCartItems1[1],
                    originalCartItems2[0],
                    originalCartItems2[1],
                    originalCartItems2[2],
                ];

                for (let index = 0; index < 5; index += 1) {
                    let actualResult = currentCartItems[index];
                    let expectedResult = expectedCartItems[index];

                    if (actualResult !== expectedResult) {
                        return CBTest.resultMismatchFailure(
                            "second replace 2",
                            actualResult,
                            expectedResult
                        );
                    }
                }
            }

            {
                let expectedQuantities = [
                    0,
                    0,
                    13,
                    14,
                    15,
                ];

                for (let index = 0; index < 5; index += 1) {
                    let actualResult = SCCartItem.getQuantity(
                        currentCartItems[index]
                    );

                    let expectedResult = expectedQuantities[index];

                    if (actualResult !== expectedResult) {
                        return CBTest.resultMismatchFailure(
                            "second replace 3",
                            actualResult,
                            expectedResult
                        );
                    }
                }
            }
        }
        /* test: second replace */


        return {
            succeeded: true,
        };
    },
    /* CBTest_replaceCartItems() */
};
