"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCartItemTests */
/* exported SCCartItemTests_MaximumQuantityCartItem */
/* exported SCCartItemTests_OriginalSubtotalCartItem1 */
/* exported SCCartItemTests_OriginalUnitPriceCartItem1 */
/* exported SCCartItemTests_QuantityCartItem1 */
/* exported SCCartItemTests_SubtotalCartItem1 */
/* exported SCCartItemTests_UnitPriceCartItem1 */
/* global
    CBException,
    CBModel,
    CBTest,
    Colby,
    SCCartItem,

    SCCartItemTests_getMaximumQuantityTestCases,
    SCCartItemTests_getOriginalSubtotalInCentsTestCases,
    SCCartItemTests_getOriginalUnitPriceInCentsTestCases,
    SCCartItemTests_getQuantityTestCases,
    SCCartItemTests_getSubtotalInCentsTestCases,
    SCCartItemTests_getUnitPriceInCentsTestCases,
*/

var SCCartItemTests = {

    /* -- tests -- -- -- -- -- */



    /**
     * @return object|Promise
     */
    CBTest_cleanAndConsolidateCartItems: function () {
        let originalCartItemSpecs = [
            {
                className: "SCProductCartItem",
                productCode: "TEST_00",
            },
            5,
            {
                className: "SCProductCartItem",
                productCode: "TEST_00",
                quantity: 0,
            },
            undefined,
            {
                className: "SCProductCartItem",
                productCode: "TEST_01",
                quantity: 1,
            },
            {
                className: "SCProductCartItem",
                productCode: "TEST_02",
                quantity: 4,
            },
            "fred",
            {
                className: "SCProductCartItem",
                productCode: "TEST_01",
                quantity: 2,
            },
            {
                foo: "bar",
            },
            undefined,
            {
                className: "SCProductCartItem",
                productCode: "TEST_01",
                quantity: 3,
            },
        ];

        let expectedResult = [
            {
                className: "SCProductCartItem",
                productCode: "TEST_01",
                quantity: 6,
            },
            {
                className: "SCProductCartItem",
                productCode: "TEST_02",
                quantity: 4,
            },
        ];

        let actualResult = SCCartItem.cleanAndConsolidateCartItems(
            originalCartItemSpecs
        );

        if (!CBModel.equals(actualResult, expectedResult)) {
            return CBTest.resultMismatchFailure(
                "test 1",
                actualResult,
                expectedResult
            );
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_cleanAndConsolidateCartItems() */



    /**
     * @return object|Promise
     */
    CBTest_fetchUpdatedCartItemSpec: function () {
        let originalCartItemSpec = {
            className: "SCProductCartItem",
            productCode: "SCProductTest_1",
        };

        let expectedResult = {
            className: "SCProductCartItem",
            productCode: "SCProductTest_1",
            image: null,
            message: "",
            priceInCents: 0,
            quantity: 0,
            title: "Test Product 1",
            unitPriceInCents: 1000,
        };

        return Colby.callAjaxFunction(
            "SCProductTests",
            "installTestProducts"
        ).then(
            function () {
                return SCCartItem.fetchUpdatedCartItemSpec(
                    originalCartItemSpec
                );
            }
        ).then(
            function (actualResult) {
                return Colby.callAjaxFunction(
                    "SCProductTests",
                    "uninstallTestProducts"
                ).then(
                    function () {
                        if (CBModel.equals(actualResult, expectedResult)) {
                            return {
                                succeeded: true,
                            };
                        } else {
                            return CBTest.resultMismatchFailure(
                                'Test 1',
                                actualResult,
                                expectedResult
                            );
                        }
                    }
                );
            }
        );
    },
    /* CBTest_fetchUpdatedCartItemSpec() */



    /**
     * @return object|Promise
     */
    CBTest_getMaximumQuantity: function (
    ) {
        let testCaseCount = SCCartItemTests_getMaximumQuantityTestCases.length;

        for (let index = 0; index < testCaseCount; index += 1) {
            let testCase = SCCartItemTests_getMaximumQuantityTestCases[index];

            let actualResult = SCCartItem.getMaximumQuantity(
                testCase.cartItemModel);

            let expectedResult = testCase.expectedMaximumQuantity;

            if (expectedResult === null) {
                expectedResult = undefined;
            }

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test case index ${index}`,
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_getMaximumQuantity() */



    /**
     * @return object
     */
    CBTest_getOriginalSubtotalInCents(
    ) {
        for (
            let index = 0;
            index < SCCartItemTests_getOriginalSubtotalInCentsTestCases.length;
            index += 1
        ) {
            let testCase = (
                SCCartItemTests_getOriginalSubtotalInCentsTestCases[index]
            );

            let actualResult = SCCartItem.getOriginalSubtotalInCents(
                testCase.cartItemModel
            );

            let expectedResult = testCase.expectedOriginalSubtotalInCents;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    JSON.stringify(
                        testCase.cartItemModel
                    ),
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_getOriginalSubtotalInCents() */



    /**
     * @return object|Promise
     */
    CBTest_getOriginalUnitPriceInCents(
    ) {
        let testCaseCount = (
            SCCartItemTests_getOriginalUnitPriceInCentsTestCases.length
        );

        for (
            let index = 0;
            index < testCaseCount;
            index += 1
        ) {
            let actualResult;

            let testCase = (
                SCCartItemTests_getOriginalUnitPriceInCentsTestCases[index]
            );

            try {
                actualResult = SCCartItem.getOriginalUnitPriceInCents(
                    testCase.cartItemModel
                );
            } catch(error) {
                actualResult = CBException.errorToSourceCBID(
                    error
                );
            }

            let expectedResult = testCase.expectedResult;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test case index ${index}`,
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_getOriginalUnitPriceInCents() */



    /**
     * @return object|Promise
     */
    CBTest_getQuantity(
    ) {
        let testCaseCount = SCCartItemTests_getQuantityTestCases.length;

        for (
            let index = 0;
            index < testCaseCount;
            index += 1
        ) {
            let actualResult;
            let testCase = SCCartItemTests_getQuantityTestCases[index];

            try {
                actualResult = SCCartItem.getQuantity(
                    testCase.originalValue
                );
            } catch(error) {
                actualResult = CBException.errorToSourceCBID(
                    error
                );
            }

            let expectedResult = testCase.expectedResult;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test case index ${index}`,
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_getQuantity() */



    /**
     * @return object
     */
    CBTest_getSubtotalInCents(
    ) {
        for (
            let index = 0;
            index < SCCartItemTests_getSubtotalInCentsTestCases.length;
            index += 1
        ) {
            let testCase = SCCartItemTests_getSubtotalInCentsTestCases[index];

            let actualResult = SCCartItem.getSubtotalInCents(
                testCase.cartItemModel
            );

            let expectedResult = testCase.expectedSubtotalInCents;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    JSON.stringify(
                        testCase.cartItemModel
                    ),
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_getSubtotalInCents() */



    /**
     * @return object|Promise
     */
    CBTest_getUnitPriceInCents(
    ) {
        let testCaseCount = SCCartItemTests_getUnitPriceInCentsTestCases.length;

        for (
            let index = 0;
            index < testCaseCount;
            index += 1
        ) {
            let actualResult;
            let testCase = SCCartItemTests_getUnitPriceInCentsTestCases[index];

            try {
                actualResult = SCCartItem.getUnitPriceInCents(
                    testCase.cartItemModel
                );
            } catch(error) {
                actualResult = CBException.errorToSourceCBID(
                    error
                );
            }

            let expectedResult = testCase.expectedResult;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test case index ${index}`,
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_getUnitPriceInCents() */



    /**
     * @return Promise -> object
     */
    CBTest_updateSpecs(
    ) {
        let originalCartItemSpecs = [
            {
                className: "SCProductCartItem",
                productCode: "SCProductTest_1",
                quantity: 1,
            },
            {
                className: "SCProductCartItem",
                productCode: "SCProductTest_1",
                quantity: 5,
            },
            {
                className: "SCProductCartItem",
                productCode: "SCProductTest_2",
                quantity: 3,
            },
        ];

        return Colby.callAjaxFunction(
            "SCProductTests",
            "installTestProducts"
        ).then(
            function () {
                return Colby.callAjaxFunction(
                    "SCCartItem",
                    "updateSpecs",
                    {
                        originalCartItemSpecs: originalCartItemSpecs,
                    }
                );
            }
        ).then(
            function (updatedCartItemSpecs) {
                return Colby.callAjaxFunction(
                    "SCProductTests",
                    "uninstallTestProducts"
                ).then(
                    function () {
                        {
                            let actualResult = CBModel.valueAsInt(
                                updatedCartItemSpecs[0],
                                "quantity"
                            );

                            let expectedResult = 1;

                            if (actualResult !== expectedResult) {
                                return CBTest.resultMismatchFailure(
                                    "Test 1",
                                    actualResult,
                                    expectedResult
                                );
                            }
                        }

                        {
                            let actualResult = CBModel.valueAsInt(
                                updatedCartItemSpecs[1],
                                "quantity"
                            );

                            let expectedResult = 5;

                            if (actualResult !== expectedResult) {
                                return CBTest.resultMismatchFailure(
                                    "Test 2",
                                    actualResult,
                                    expectedResult
                                );
                            }
                        }

                        {
                            let actualResult = CBModel.valueAsInt(
                                updatedCartItemSpecs[2],
                                "quantity"
                            );

                            let expectedResult = 3;

                            if (actualResult !== expectedResult) {
                                return CBTest.resultMismatchFailure(
                                    "Test 2",
                                    actualResult,
                                    expectedResult
                                );
                            }
                        }

                        return {
                            succeeded: true,
                        };
                    }
                );
            }
        );
    },
    /* CBTest_updateSpecs() */

};
/* SCCartItemTests */



/**
 *
 */
var SCCartItemTests_MaximumQuantityCartItem = {

    SCCartItem_getMaximumQuantity: function(
        /* cartItemModel */
    ) {
        return 5;
    },

};
/* SCCartItemTests_MaximumQuantityCartItem */



/**
 *
 */
var SCCartItemTests_OriginalSubtotalCartItem1 = {

    /**
     * @return int
     */
    SCCartItem_getOriginalSubtotalInCents(
        /* cartItemModel */
    ) {
        return 2000;
    },

    /**
     * @return int
     */
    SCCartItem_getSubtotalInCents(
        /* cartItemModel */
    ) {
        return 1800;
    },

};
/* SCCartItemTests_OriginalSubtotalCartItem1 */



/**
 *
 */
var SCCartItemTests_OriginalUnitPriceCartItem1 = {

    /**
     * @return int
     */
    SCCartItem_getOriginalUnitPriceInCents(
        cartItemModel
    ) {
        let hasNegativeUnitPrice = CBModel.valueToBool(
            cartItemModel,
            'hasNegativeUnitPrice'
        );

        if (hasNegativeUnitPrice) {
            return -5252;
        } else {
            return 5252;
        }
    }

};
/* SCCartItemTests_OriginalUnitPriceCartItem1 */



/**
 *
 */
var SCCartItemTests_QuantityCartItem1 = {

    /**
     * @return float
     */
    SCCartItem_getQuantity(
        /* cartItemModel */
    ) {
        return -4.2;
    }

};
/* SCCartItemTests_QuantityCartItem1 */



/**
 *
 */
var SCCartItemTests_SubtotalCartItem1 = {

    /**
     * @return int
     */
    SCCartItem_getSubtotalInCents(
        /* cartItemModel */
    ) {
        return 1000;
    },

};
/* SCCartItemTests_SubtotalCartItem1 */



/**
 *
 */
var SCCartItemTests_UnitPriceCartItem1 = {

    /**
     * @return int
     */
    SCCartItem_getUnitPriceInCents(
        cartItemModel
    ) {
        let hasNegativeUnitPrice = CBModel.valueToBool(
            cartItemModel,
            'hasNegativeUnitPrice'
        );

        if (hasNegativeUnitPrice) {
            return -4242;
        } else {
            return 4242;
        }
    }

};
/* SCCartItemTests_UnitPriceCartItem1 */
