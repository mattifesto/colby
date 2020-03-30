<?php

final class SCCartItemTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v134.js', scliburl()),
        ];
    }



    /**
     * @return [[<variableName>, <variableValue>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'SCCartItemTests_getMaximumQuantityTestCases',
                SCCartItemTests::getMaximumQuantityTestCases(),
            ],
            [
                'SCCartItemTests_getQuantityTestCases',
                SCCartItemTests::getQuantityTestCases(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
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
                'name' => 'getQuantity',
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
                'name' => 'getQuantity',
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
    static function CBTest_getQuantity(): stdClass {
        $testCases = SCCartItemTests::getQuantityTestCases();

        for ($index = 0; $index < count($testCases); $index += 1) {
            $testCase = $testCases[$index];
            $actualResult = SCCartItem::getQuantity($testCase->originalValue);
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
     */
    static function getQuantityTestCases(): array {
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
                'expectedResult' => 0.0,
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
        ];
    }
    /* getQuantityTestCases() */

}



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
     * @return stdClass
     */
    static function SCCartItem_getMaximumQuantity(
        stdClass $cartItemModel
    ): ?float {
        return 5;
    }
}
