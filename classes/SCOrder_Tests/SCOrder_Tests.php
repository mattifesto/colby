<?php

final class SCOrder_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'build',
                'type' => 'server',
            ],
            (object)[
                'name' => 'prepare',
                'type' => 'server',
            ],
            (object)[
                'name' => 'prepareOrderKind',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgrade_orderArchivedBy',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgrade_orderPaymentCapturedBy',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_build(): stdClass {
        /* -- test -- -- -- -- -- */

        $testTitle = 'Two Valid Notes';
        $actualNoteCount = null;
        $expectedNoteCount = 2;

        $orderModel = CBModel::build(
            (object)[
                'className' => 'SCOrder',
                'notes' => [
                    (object)[
                        'className' => 'CBNote',
                        'text' => 'Note 1',
                        'timestamp' => time(),
                    ],
                    (object)[
                        'className' => 'CBNote',
                        'text' => 'Note 2',
                        'timestamp' => time(),
                    ],
                ]
            ]
        );

        $actualNoteCount = count($orderModel->notes);

        if ($actualNoteCount !== $expectedNoteCount) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actualNoteCount,
                $expectedNoteCount
            );
        }

        /* -- test -- -- -- -- -- */

        $testTitle = 'Invalid Note Class Name';
        $actualSourceID = null;
        $expectedSourceID = '81b72e04330df3bde3ece481082fa049c1129207';

        try {
            CBModel::build(
                (object)[
                    'className' => 'SCOrder',
                    'notes' => [
                        (object)[
                            'className' => 'CBNote_Bad',
                        ]
                    ]
                ]
            );
        } catch (Throwable $throwable) {
            $actualSourceID = CBException::throwableToSourceID($throwable);
        }

        if ($actualSourceID !== $expectedSourceID) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actualSourceID,
                $expectedSourceID
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_build() */



    /**
     * @return object
     */
    static function CBTest_prepare(): stdClass {
        $spec = (object)[
            'className' => 'SCOrder',
            'kindClassName' => 'SCOrder_Tests_OrderKind1',
        ];

        $preparedSpec = SCOrder::prepare($spec);


        /* orderShippingMethod */

        $actualOrderShippingMethod = CBModel::valueToString(
            $preparedSpec,
            'orderShippingMethod'
        );

        $expectedOrderShippingMethod = 'Overnight';

        if ($actualOrderShippingMethod !== $expectedOrderShippingMethod) {
            return CBTest::resultMismatchFailure(
                'orderShippingMethod',
                $actualOrderShippingMethod,
                $expectedOrderShippingMethod
            );
        }


        /* orderShippingChargeInCents */

        $actualOrderShippingChargeInCents = CBModel::valueAsInt(
            $preparedSpec,
            'orderShippingChargeInCents'
        );

        $expectedOrderShippingChargeInCents = 110;

        if (
            $actualOrderShippingChargeInCents !==
            $expectedOrderShippingChargeInCents
        ) {
            return CBTest::resultMismatchFailure(
                'orderShippingChargeInCents',
                $actualOrderShippingChargeInCents,
                $expectedOrderShippingChargeInCents
            );
        }


        /* orderSalesTaxInCents */

        $actualOrderSalesTaxInCents = CBModel::valueAsInt(
            $preparedSpec,
            'orderSalesTaxInCents'
        );

        $expectedOrderSalesTaxInCents = 100;

        if ($actualOrderSalesTaxInCents !== $expectedOrderSalesTaxInCents) {
            return CBTest::resultMismatchFailure(
                'orderSalesTaxInCents',
                $actualOrderSalesTaxInCents,
                $expectedOrderSalesTaxInCents
            );
        }


        /* orderTotalInCents */

        $actualOrderTotalInCents = CBModel::valueAsInt(
            $preparedSpec,
            'orderTotalInCents'
        );

        $expectedOrderTotalInCents = 210;

        if ($actualOrderTotalInCents !== $expectedOrderTotalInCents) {
            return CBTest::resultMismatchFailure(
                'orderTotalInCents',
                $actualOrderTotalInCents,
                $expectedOrderTotalInCents
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_prepare() */



    /**
     * @return object
     */
    static function CBTest_prepareOrderKind(): stdClass {
        $actualSourceID = null;
        $expectedSourceID = '922fc2133e1d4f1a8b58346658cfa58cf4950104';

        SCOrder_Tests::runWithPreferences(
            function () use (&$actualSourceID) {
                $spec = (object)[
                    'className' => 'SCOrder',
                ];

                try {
                    $preparedSpec = SCOrder::prepare($spec);
                } catch (Throwable $throwable) {
                    $actualSourceID = CBException::throwableToSourceID(
                        $throwable
                    );
                }
            },
            (object)[]
        );

        if ($actualSourceID !== $expectedSourceID) {
            return CBTest::resultMismatchFailure(
                'no default order kind',
                $actualSourceID,
                $expectedSourceID
            );
        }


        /* -- -- -- -- -- */

        $actualOrderKindClassName = null;
        $expectedOrderKindClassName = 'SCOrder_Tests_OrderKind1';

        SCOrder_Tests::runWithPreferences(
            function () use (&$actualOrderKindClassName) {
                $spec = (object)[
                    'className' => 'SCOrder',
                ];

                $preparedSpec = SCOrder::prepare($spec);

                $actualOrderKindClassName = CBModel::valueToString(
                    $preparedSpec,
                    'kindClassName'
                );
            },
            (object)[
                'defaultOrderKindClassName' => $expectedOrderKindClassName,
            ]
        );

        if ($actualOrderKindClassName !== $expectedOrderKindClassName) {
            return CBTest::resultMismatchFailure(
                'SCOrder_Tests_OrderKind1',
                $actualOrderKindClassName,
                $expectedOrderKindClassName
            );
        }


        /* -- -- -- -- -- */

        $actualOrderKindClassName = null;
        $expectedOrderKindClassName = 'SCOrder_Tests_OrderKind2';

        $spec = (object)[
            'className' => 'SCOrder',
            'kindClassName' => $expectedOrderKindClassName,
        ];

        $preparedSpec = SCOrder::prepare($spec);

        $actualOrderKindClassName = CBModel::valueToString(
            $preparedSpec,
            'kindClassName'
        );

        if ($actualOrderKindClassName !== $expectedOrderKindClassName) {
            return CBTest::resultMismatchFailure(
                'SCOrder_Tests_OrderKind2',
                $actualOrderKindClassName,
                $expectedOrderKindClassName
            );
        }


        /* -- -- -- -- -- */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_prepareOrderKind() */



    /**
     * @return object
     */
    static function CBTest_upgrade_orderArchivedBy(): stdClass {

        /* test 1 */

        $userCBID = ColbyUser::getCurrentUserCBID();
        $userNumericID = CBUsers::forTesting_userCBIDtoUserNumericID($userCBID);

        $originalSpec = (object)[
            'className' => 'SCOrder',
            'orderArchivedBy' => $userNumericID,
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderArchivedByUserCBID' => $userCBID,
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 2 */

        $originalSpec = (object)[
            'className' => 'SCOrder',
            'orderArchivedBy' => 'abcd',
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 3 */

        $originalSpec = (object)[
            'className' => 'SCOrder',
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 4 */

        $originalSpec = (object)[
            'className' => 'SCOrder',
            'orderArchivedBy' => -1
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 4',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgrade_orderArchivedBy() */



    /**
     * @return object
     */
    static function CBTest_upgrade_orderPaymentCapturedBy(): stdClass {

        /* test 1 */

        $userCBID = ColbyUser::getCurrentUserCBID();
        $userNumericID = CBUsers::forTesting_userCBIDtoUserNumericID($userCBID);

        $originalSpec = (object)[
            'className' => 'SCOrder',
            'orderPaymentCapturedBy' => $userNumericID,
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
            'orderPaymentCapturedByUserCBID' => $userCBID,
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 2 */

        $originalSpec = (object)[
            'className' => 'SCOrder',
            'orderPaymentCapturedBy' => 'abcd',
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 3 */

        $originalSpec = (object)[
            'className' => 'SCOrder',
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 4 */

        $originalSpec = (object)[
            'className' => 'SCOrder',
            'orderPaymentCapturedBy' => -1
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'SCOrder',
            'orderItems' => [],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 4',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgrade_orderPaymentCapturedBy() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param callable $callable
     * @param object $preferencesSpec
     *
     * @return void
     */
    static function runWithPreferences(
        callable $callable,
        stdClass $preferencesSpec
    ): void {
        $testPreferencesID = '68111dc9ce8e29ccba961b96afbdfcd45891b4d0';

        try {
            CBModels::deleteByID($testPreferencesID);

            SCPreferences::$testID = $testPreferencesID;

            $preferencesSpec->className = 'SCPreferences';
            $preferencesSpec->ID = $testPreferencesID;

            CBModels::save($preferencesSpec);

            call_user_func($callable);
        } finally {
            SCPreferences::$testID = null;

            CBModels::deleteByID($testPreferencesID);
        }
    }
    /* runWithPreferences() */
}



final class SCOrder_Tests_OrderKind1 {

    /* -- SCOrderKind interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function SCOrderKind_defaultShippingMethod(): string {
        return 'Overnight';
    }



    /**
     * @param object $orderSpec
     *
     * @return int
     */
    static function SCOrderKind_salesTaxInCents(
        stdClass $orderSpec
    ): int {
        return 100;
    }



    /**
     * @param object $orderSpec
     *
     * @return int
     */
    static function SCOrderKind_shippingChargeInCents(
        stdClass $orderSpec
    ): int {
        return 110;
    }

}
/* SCOrder_Tests_OrderKind1 */



final class SCOrder_Tests_OrderKind2 {

    /* -- SCOrderKind interfaces -- -- -- -- -- */




    /**
     * @return string
     */
    static function SCOrderKind_defaultShippingMethod(): string {
        return 'Second Day Air';
    }



    /**
     * @param object $orderSpec
     *
     * @return int
     */
    static function SCOrderKind_salesTaxInCents(
        stdClass $orderSpec
    ): int {
        return 200;
    }

    /**
     * @param object $orderSpec
     *
     * @return int
     */
    static function SCOrderKind_shippingChargeInCents(
        stdClass $orderSpec
    ): int {
        return 0;
    }

}
/* SCOrder_Tests_OrderKind2 */
