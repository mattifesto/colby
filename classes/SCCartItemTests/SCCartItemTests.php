<?php

final class SCCartItemTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v644.js',
                scliburl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<variable name>, <variable value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [

            [
                'SCCartItemTests_getMaximumQuantityTestCases',
                SCCartItemTests::getMaximumQuantityTestCases(),
            ],

            [
                'SCCartItemTests_getOriginalSubtotalInCentsTestCases',
                SCCartItemTests::getOriginalSubtotalInCentsTestCases(),
            ],

            [
                'SCCartItemTests_getOriginalUnitPriceInCentsTestCases',
                SCCartItemTests::getOriginalUnitPriceInCentsTestCases(),
            ],

            [
                'SCCartItemTests_getQuantityTestCases',
                SCCartItemTests::getQuantityTestCases(),
            ],

            [
                'SCCartItemTests_getSubtotalInCentsTestCases',
                SCCartItemTests::getSubtotalInCentsTestCases(),
            ],

            [
                'SCCartItemTests_getUnitPriceInCentsTestCases',
                SCCartItemTests::getUnitPriceInCentsTestCases(),
            ],

        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBException',
            'CBModel',
            'CBTest',
            'Colby',
            'SCCartItem',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'consolidateSpecs',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getMaximumQuantity',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getOriginalSubtotalInCents',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getOriginalUnitPriceInCents',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getQuantity',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getSubtotalInCents',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getUnitPriceInCents',
                'type' => 'server',
            ],
            (object)[
                'name' => 'update_errors',
                'type' => 'server',
            ],

            (object)[
                'name' => 'cleanAndConsolidateCartItems',
            ],
            (object)[
                'name' => 'fetchUpdatedCartItemSpec',
            ],
            (object)[
                'name' => 'getMaximumQuantity',
            ],
            (object)[
                'name' => 'getOriginalSubtotalInCents',
            ],
            (object)[
                'name' => 'getOriginalUnitPriceInCents',
            ],
            (object)[
                'name' => 'getQuantity',
            ],
            (object)[
                'name' => 'getSubtotalInCents',
            ],
            (object)[
                'name' => 'getUnitPriceInCents',
            ],
            (object)[
                'name' => 'updateSpecs',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_consolidateSpecs(): stdClass {
        SCProductTests::installTestProducts();

        try {
            $originalCartItemSpecs = [
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_1',
                ],
                null,
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_2',
                    'quantity' => 4,
                ],
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_1',
                    'quantity' => 2,
                ],
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_2',
                    'quantity' => 1,
                ],
                null,
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_1',
                    'quantity' => 3,
                ],
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_2',
                ],
            ];

            $expectedResult = [
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_1',
                    'quantity' => 5,
                ],
                null,
                (object)[
                    'className' => 'SCProductCartItem',
                    'productCode' => 'SCProductTest_2',
                    'quantity' => 5,
                ],
                null,
                null,
                null,
                null,
                null,
            ];

            $actualResult = SCCartItem::consolidateSpecs(
                $originalCartItemSpecs
            );

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'Test 1',
                    $actualResult,
                    $expectedResult
                );
            }

            return (object)[
                'succeeded' => true,
            ];
        } finally {
            SCProductTests::uninstallTestProducts();
        }
    }
    /* CBTest_consolidateSpecs() */



    /**
     * @return object
     */
    static function CBTest_getMaximumQuantity(): stdClass {
        $testCases = SCCartItemTests::getMaximumQuantityTestCases();

        for ($index = 0; $index < count($testCases); $index += 1) {
            $testCase = $testCases[$index];

            $actualResult = SCCartItem::getMaximumQuantity(
                $testCase->cartItemModel
            );

            $expectedResult = $testCase->expectedMaximumQuantity;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test case index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getMaximumQuantity() */



    /**
     * @return object
     */
    static function CBTest_getOriginalSubtotalInCents(
    ): stdClass {
        $testCases = SCCartItemTests::getOriginalSubtotalInCentsTestCases();

        for ($index = 0; $index < count($testCases); $index += 1) {
            $testCase = $testCases[$index];

            $actualResult = SCCartItem::getOriginalSubtotalInCents(
                $testCase->cartItemModel
            );

            $expectedResult = $testCase->expectedOriginalSubtotalInCents;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test case index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getOriginalSubtotalInCents() */



    /**
     * @return object
     */
    static function CBTest_getOriginalUnitPriceInCents(
    ): stdClass {
        $testCases = SCCartItemTests::getOriginalUnitPriceInCentsTestCases();

        for (
            $index = 0;
            $index < count($testCases);
            $index += 1
        ) {
            $testCase = $testCases[$index];

            try {
                $actualResult = SCCartItem::getOriginalUnitPriceInCents(
                    $testCase->cartItemModel
                );
            } catch (Throwable $throwable) {
                $actualResult = CBException::throwableToSourceCBID(
                    $throwable
                );
            }

            $expectedResult = $testCase->expectedResult;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test case index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getOriginalUnitPriceInCents() */



    /**
     * @return object
     */
    static function CBTest_getQuantity(): stdClass {
        $testCases = SCCartItemTests::getQuantityTestCases();

        for (
            $index = 0;
            $index < count($testCases);
            $index += 1
        ) {
            $testCase = $testCases[$index];

            try {
                $actualResult = SCCartItem::getQuantity(
                    $testCase->originalValue
                );
            } catch (Throwable $throwable) {
                $actualResult = CBException::throwableToSourceCBID(
                    $throwable
                );
            }

            $expectedResult = $testCase->expectedResult;

            /**
             * @NOTE 2019_02_17
             *
             *      The getQuantity() function returns a float even when the
             *      value is an integer. Test cases need to specify the expected
             *      result as a float because === will return false if one value
             *      is a float and the other is an integer.
             */
            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test case index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getQuantity() */



    /**
     * @return object
     */
    static function CBTest_getSubtotalInCents(
    ): stdClass {
        $testCases = SCCartItemTests::getSubtotalInCentsTestCases();

        for ($index = 0; $index < count($testCases); $index += 1) {
            $testCase = $testCases[$index];

            $actualResult = SCCartItem::getSubtotalInCents(
                $testCase->cartItemModel
            );

            $expectedResult = $testCase->expectedSubtotalInCents;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test case index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getSubtotalInCents() */



    /**
     * @return object
     */
    static function CBTest_getUnitPriceInCents(
    ): stdClass {
        $testCases = SCCartItemTests::getUnitPriceInCentsTestCases();

        for ($index = 0; $index < count($testCases); $index += 1) {
            $testCase = $testCases[$index];

            try {
                $actualResult = SCCartItem::getUnitPriceInCents(
                    $testCase->cartItemModel
                );
            } catch (Throwable $throwable) {
                $actualResult = CBException::throwableToSourceCBID(
                    $throwable
                );
            }

            $expectedResult = $testCase->expectedResult;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test case index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_getUnitPriceInCents() */



    /**
     * @return object
     */
    static function CBTest_update_errors(): stdClass {
        SCCartItem::$reportUpdateExceptions = false;

        try {
            $originalCartItemSpec = (object)[
                'className' => 'SCCartItemTests_ErrorCartItem1',
            ];

            $updatedCartItemSpec = SCCartItem::update($originalCartItemSpec);

            /* test: updated cart item spec */

            $actualResult = CBModel::valueAsID(
                $updatedCartItemSpec,
                'sourceID'
            );

            $expectedResult = '7ded2849d2311f76d4e4ea1404a9581372140816';

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'updated cart item spec',
                    $actualResult,
                    $expectedResult
                );
            }

            /* test: 5 */

            $originalCartItemSpec = 5;

            $actualResult = SCCartItem::update($originalCartItemSpec);
            $expectedResult = null;

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'original cart item spec is 5',
                    $actualResult,
                    $expectedResult
                );
            }

            /* test: no class name */

            $originalCartItemSpec = (object)[];

            $actualResult = SCCartItem::update($originalCartItemSpec);
            $expectedResult = null;

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'original cart item spec has no class name',
                    $actualResult,
                    $expectedResult
                );
            }


            /* test: invalid updatedCartItemSpec property */

            $originalCartItemSpec = (object)[
                'className' => 'SCCartItemTests_ErrorCartItem2',
            ];

            $updatedCartItemSpec = SCCartItem::update($originalCartItemSpec);

            $actualResult = CBModel::valueAsID(
                $updatedCartItemSpec,
                'sourceID'
            );

            $expectedResult = '7ded2849d2311f76d4e4ea1404a9581372140816';

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'invalid updatedCartItemSpec property',
                    $actualResult,
                    $expectedResult
                );
            }


            /* test: SCCartItem_update() not implemented */

            $originalCartItemSpec = (object)[
                'className' => 'SCCartItemTests_ErrorCartItem3',
            ];

            $updatedCartItemSpec = SCCartItem::update($originalCartItemSpec);

            $actualResult = CBModel::valueAsID(
                $updatedCartItemSpec,
                'sourceID'
            );

            $expectedResult = '4006918cd574b94b69bc369f0ca4a4490907080a';

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'SCCartItem_update() not implemented',
                    $actualResult,
                    $expectedResult
                );
            }


            /* test: SCCartItem_update() returns deprecated value */

            $originalCartItemSpec = (object)[
                'className' => 'SCCartItemTests_ErrorCartItemDeprecated',
            ];

            $updatedCartItemSpec = SCCartItem::update($originalCartItemSpec);

            $actualResult = CBModel::valueAsID(
                $updatedCartItemSpec,
                'sourceID'
            );

            $expectedResult = '73d1e9a0f7d792d7abdf900df7e07c75330c9400';

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'SCCartItem_update() not implemented',
                    $actualResult,
                    $expectedResult
                );
            }
        } finally {
            SCCartItem::$reportUpdateExceptions = true;
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_update_errors() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function getMaximumQuantityTestCases(): array {
        return [
            (object)[
                'cartItemModel' => (object)[
                ],
                'expectedMaximumQuantity' => null,
            ],
            (object)[
                'cartItemModel' => (object)[
                    'className' => 'DoesNotExist',
                ],
                'expectedMaximumQuantity' => null,
            ],
            (object)[
                'cartItemModel' => (object)[
                    'maximumQuantity' => 1,
                ],
                'expectedMaximumQuantity' => 1.0,
            ],
            (object)[
                'cartItemModel' => (object)[
                    'className' => 'DoesNotExist',
                    'maximumQuantity' => 1,
                ],
                'expectedMaximumQuantity' => 1.0,
            ],
            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_MaximumQuantityCartItem',
                ],
                'expectedMaximumQuantity' => 5.0,
            ],
        ];
    }
    /* getMaximumQuantityTestCases() */



    /**
     * @return [object]
     *
     *      {
     *          cartItemModel: object
     *          expectedOriginalSubtotalInCents: int
     *      }
     */
    static function getOriginalSubtotalInCentsTestCases(
    ): array {
        return [

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_OriginalSubtotalCartItem1',
                ],
                'expectedOriginalSubtotalInCents' => 2000,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_OriginalSubtotalCartItem1',
                    'SCCartItem_originalSubtotalInCents' => 1900,
                ],
                'expectedOriginalSubtotalInCents' => 2000,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_originalSubtotalInCents' => 900,
                ],
                'expectedOriginalSubtotalInCents' => 900,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_subtotalInCents' => 700,
                    'SCCartItem_originalSubtotalInCents' => 800,
                ],
                'expectedOriginalSubtotalInCents' => 800,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_subtotalInCents' => 700,
                    'SCCartItem_originalSubtotalInCents' => 600,
                ],
                'expectedOriginalSubtotalInCents' => 600,
            ],

            /**
             * The following tests were copied from
             * getSubtotalInCentsTestCases() and adjusted for
             * getOriginalSubtotalInCents().
             */

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_SubtotalCartItem1',
                ],
                'expectedOriginalSubtotalInCents' => 1000,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_SubtotalCartItem1',
                ],
                'expectedOriginalSubtotalInCents' => 1000,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_subtotalInCents' => 900,
                ],
                'expectedOriginalSubtotalInCents' => 900,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'priceInCents' => 800,
                ],
                'expectedOriginalSubtotalInCents' => 800,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_subtotalInCents' => 700,
                    'priceInCents' => 600,
                ],
                'expectedOriginalSubtotalInCents' => 700,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_SubtotalCartItem1',
                    'SCCartItem_subtotalInCents' => 500,
                    'priceInCents' => 400,
                ],
                'expectedOriginalSubtotalInCents' => 1000,
            ],

        ];
    }
    /* getOriginalSubtotalInCentsTestCases() */



    /**
     * @return [object]
     */
    static function getOriginalUnitPriceInCentsTestCases(
    ): array {
        return [
            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_OriginalUnitPriceCartItem1',
                ],
                'expectedResult' => 5252,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_OriginalUnitPriceCartItem1',
                    'hasNegativeUnitPrice' => true,
                ],
                'expectedResult' => '363b7914a1a1d9adec962ef043c89f1495d4eb74',
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_originalUnitPriceInCents' => 5252,
                ],
                'expectedResult' => 5252,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_originalUnitPriceInCents' => -5252,
                ],
                'expectedResult' => '363b7914a1a1d9adec962ef043c89f1495d4eb74',
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_unitPriceInCents' => 5252,
                ],
                'expectedResult' => 5252,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'unitPriceInCents' => 5252,
                ],
                'expectedResult' => 5252,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'originalUnitPriceInCents' => 5252,
                ],
                'expectedResult' => 'eb1465850e5a201b8d33006b79bfbe4be54482ce',
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_originalUnitPriceInCents' => 'foo',
                ],
                'expectedResult' => 'eb1465850e5a201b8d33006b79bfbe4be54482ce',
            ],

            (object)[
                'cartItemModel' => (object)[],
                'expectedResult' => 'eb1465850e5a201b8d33006b79bfbe4be54482ce',
            ],

        ];
    }
    /* getOriginalUnitPriceInCentsTestCases() */



    /**
     * @return [object]
     */
    static function getQuantityTestCases(
    ): array {
        return [
            (object)[
                'originalValue' => (object)[
                    'className' => 'TestCartItem',
                ],
                'expectedResult' => 0.0,
            ],

            (object)[
                'originalValue' => (object)[
                    'className' => 'TestCartItem',
                    'quantity' => 1,
                ],
                'expectedResult' => 1.0,
            ],

            (object)[
                'originalValue' => (object)[
                    'className' => 'TestCartItem',
                    'quantity' => 0,
                ],
                'expectedResult' => 0.0,
            ],

            (object)[
                'originalValue' => (object)[
                    'className' => 'TestCartItem',
                    'quantity' => -1,
                ],
                'expectedResult' => 'e18433a4d2739c7c3a707fa04b9a899cd4e70f68',
            ],

            (object)[
                'originalValue' => (object)[
                    'className' => 'TestCartItem',
                    'quantity' => 5,
                ],
                'expectedResult' => 5.0,
            ],

            (object)[
                'originalValue' => (object)[
                    'quantity' => 5,
                ],
                'expectedResult' => 5.0,
            ],

            (object)[
                'originalValue' => (object)[
                    'isEphemeral' => true,
                    'quantity' => 5,
                ],
                'expectedResult' => 5.0,
            ],

            (object)[
                'originalValue' => (object)[
                    'quantity' => -5,
                ],
                'expectedResult' => 'e18433a4d2739c7c3a707fa04b9a899cd4e70f68',
            ],

            (object)[
                'originalValue' => (object)[
                    'className' => 'SCCartItemTests_QuantityCartItem1',
                ],
                'expectedResult' => 'e18433a4d2739c7c3a707fa04b9a899cd4e70f68',
            ],

        ];
    }
    /* getQuantityTestCases() */



    /**
     * @return [object]
     *
     *      {
     *          cartItemModel: object
     *          expectedSubtotalInCents: int
     *      }
     */
    static function getSubtotalInCentsTestCases(
    ): array {
        return [

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_SubtotalCartItem1',
                ],
                'expectedSubtotalInCents' => 1000,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_subtotalInCents' => 900,
                ],
                'expectedSubtotalInCents' => 900,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'priceInCents' => 800,
                ],
                'expectedSubtotalInCents' => 800,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_subtotalInCents' => 700,
                    'priceInCents' => 600,
                ],
                'expectedSubtotalInCents' => 700,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_SubtotalCartItem1',
                    'SCCartItem_subtotalInCents' => 500,
                    'priceInCents' => 400,
                ],
                'expectedSubtotalInCents' => 1000,
            ],

        ];
    }
    /* getSubtotalInCentsTestCases() */



    /**
     * @return [object]
     */
    static function getUnitPriceInCentsTestCases(
    ): array {
        return [

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_UnitPriceCartItem1',
                ],
                'expectedResult' => 4242,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'className' => 'SCCartItemTests_UnitPriceCartItem1',
                    'hasNegativeUnitPrice' => true,
                ],
                'expectedResult' => 'eb1465850e5a201b8d33006b79bfbe4be54482ce',
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_unitPriceInCents' => 4242,
                ],
                'expectedResult' => 4242,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'unitPriceInCents' => 4242,
                ],
                'expectedResult' => 4242,
            ],

            (object)[
                'cartItemModel' => (object)[
                    'SCCartItem_unitPriceInCents' => 'foo',
                ],
                'expectedResult' => 'eb1465850e5a201b8d33006b79bfbe4be54482ce',
            ],

            (object)[
                'cartItemModel' => (object)[],
                'expectedResult' => 'eb1465850e5a201b8d33006b79bfbe4be54482ce',
            ],

        ];
    }
    /* getUnitPriceInCentsTestCases() */

}
/* SCCartItemTests */



/**
 *
 */
final class SCCartItemTests_ErrorCartItem1 {

    /**
     * @return array
     *
     *      This is the error. This function should return an object.
     */
    static function SCCartItem_update($originalCartItemSpec): array {
        return [1, 2, 3];
    }
}



/**
 *
 */
final class SCCartItemTests_ErrorCartItem2 {

    /**
     * @return stdClass
     */
    static function SCCartItem_update($originalCartItemSpec): stdClass {
        return (object)[
        ];
    }
}



/**
 *
 */
final class SCCartItemTests_ErrorCartItemDeprecated {

    /**
     * @return stdClass
     */
    static function SCCartItem_update($originalCartItemSpec): stdClass {
        return (object)[
            'className' => 'SCCartItemTests_ErrorCartItemDeprecated',
            'updatedCartItemSpec' => 1,
        ];
    }
}



/**
 *
 */
final class SCCartItemTests_MaximumQuantityCartItem {

    /**
     * @return float|null
     */
    static function SCCartItem_getMaximumQuantity(
        stdClass $cartItemModel
    ): ?float {
        return 5;
    }
}



/**
 *
 */
final class SCCartItemTests_OriginalSubtotalCartItem1 {

    /**
     * @return int
     */
    static function SCCartItem_getOriginalSubtotalInCents(
        stdClass $cartItemModel
    ): int {
        return 2000;
    }

    /**
     * @return int
     */
    static function SCCartItem_getSubtotalInCents(
        stdClass $cartItemModel
    ): int {
        return 1800;
    }

}
/* SCCartItemTests_OriginalSubtotalCartItem1 */



/**
 *
 */
final class SCCartItemTests_OriginalUnitPriceCartItem1 {

    /**
     * @return int
     */
    static function SCCartItem_getOriginalUnitPriceInCents(
        stdClass $cartItemModel
    ): int {
        $hasNegativeUnitPrice = CBModel::valueToBool(
            $cartItemModel,
            'hasNegativeUnitPrice'
        );

        if ($hasNegativeUnitPrice) {
            return -5252;
        } else {
            return 5252;
        }
    }

}
/* SCCartItemTests_OriginalUnitPriceCartItem1 */



/**
 *
 */
final class SCCartItemTests_QuantityCartItem1 {

    /**
     * @return float
     */
    static function SCCartItem_getQuantity(
        $cartItemModel
    ): float {
        return -4.2;
    }

}
/* SCCartItemTests_QuantityCartItem1 */



/**
 *
 */
final class SCCartItemTests_SubtotalCartItem1 {

    /**
     * @return int
     */
    static function SCCartItem_getSubtotalInCents(
        stdClass $cartItemModel
    ): int {
        return 1000;
    }

}
/* SCCartItemTests_SubtotalCartItem1 */



/**
 *
 */
final class SCCartItemTests_UnitPriceCartItem1 {

    /**
     * @return int
     */
    static function SCCartItem_getUnitPriceInCents(
        stdClass $cartItemModel
    ): int {
        $hasNegativeUnitPrice = CBModel::valueToBool(
            $cartItemModel,
            'hasNegativeUnitPrice'
        );

        if ($hasNegativeUnitPrice) {
            return -4242;
        } else {
            return 4242;
        }
    }

}
/* SCCartItemTests_UnitPriceCartItem1 */
