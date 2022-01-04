<?php

final class
SCPromotion_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests  -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    general(
    ): stdClass {
        $promotionCBID = 'd918a66dec8e39311ae5bf496e2c8d74ba8f9353';


        /* initialize test */

        CBDB::transaction(
            function () use (
                $promotionCBID
            ) {
                CBModels::deleteByID(
                    $promotionCBID
                );
            }
        );

        SCPromotionsTable::deletePromotionByCBID(
            $promotionCBID
        );

        $activePromotionCBIDs = SCPromotionsTable::fetchActivePromotionCBIDs();

        $isActive = in_array(
            $promotionCBID,
            $activePromotionCBIDs
        );

        if ($isActive !== false) {
            return CBTest::valueIssueFailure(
                'initialize test',
                $activePromotionCBIDs,
                <<< EOT

                    The array of active promotions should not contain the test
                    promotion CBID.

                EOT
            );
        }

        /* bare promotion */

        $promotionSpec = (object)[
            'className' => 'SCPromotion',
            'ID' => $promotionCBID,
        ];

        CBDB::transaction(
            function () use (
                $promotionSpec
            ) {
                CBModels::save(
                    $promotionSpec
                );
            }
        );

        CBDB::transaction(
            function () use (
                $promotionCBID
            ) {
                CBModels::deleteByID(
                    $promotionCBID
                );
            }
        );

        $activePromotionCBIDs = SCPromotionsTable::fetchActivePromotionCBIDs();

        $isActive = in_array(
            $promotionCBID,
            $activePromotionCBIDs
        );

        if (
            $isActive !== false
        ) {
            return CBTest::valueIssueFailure(
                'bare promotion',
                $activePromotionCBIDs,
                <<< EOT

                    The array of active promotions should not contain the test
                    promotion CBID.

                EOT
            );
        }


        /* active promotion */

        $promotionSpec = (object)[
            'className' => 'SCPromotion',
            'ID' => $promotionCBID,
            'beginTimestamp' => time() - 600,
            'endTimestamp' => time() + 600,
        ];

        CBDB::transaction(
            function () use (
                $promotionSpec
            ) {
                CBModels::save(
                    $promotionSpec
                );
            }
        );

        $activePromotionCBIDs = SCPromotionsTable::fetchActivePromotionCBIDs();

        $isActive = in_array(
            $promotionCBID,
            $activePromotionCBIDs
        );

        if ($isActive !== true) {
            return CBTest::valueIssueFailure(
                'active promotion',
                $activePromotionCBIDs,
                <<< EOT

                    The array of active promotions should contain the test
                    promotion CBID.

                EOT
            );
        }


        /* make active promotion inactive */

        $promotionSpec->version = 1;
        $promotionSpec->endTimestamp = 0;

        CBDB::transaction(
            function () use (
                $promotionSpec
            ) {
                CBModels::save(
                    $promotionSpec
                );
            }
        );

        $activePromotionCBIDs = SCPromotionsTable::fetchActivePromotionCBIDs();

        $isActive = in_array(
            $promotionCBID,
            $activePromotionCBIDs
        );

        if (
            $isActive !== false
        ) {
            return CBTest::valueIssueFailure(
                'make active promotion inactive',
                $activePromotionCBIDs,
                <<< EOT

                    The array of active promotions should not contain the test
                    promotion CBID.

                EOT
            );
        }


        /* delete promotion */

        CBDB::transaction(
            function () use (
                $promotionCBID
            ) {
                CBModels::deleteByID(
                    $promotionCBID
                );
            }
        );

        $activePromotionCBIDs = SCPromotionsTable::fetchActivePromotionCBIDs();

        $isActive = in_array(
            $promotionCBID,
            $activePromotionCBIDs
        );

        if (
            $isActive !== false
        ) {
            return CBTest::valueIssueFailure(
                'delete promotion',
                $activePromotionCBIDs,
                <<< EOT

                    The array of active promotions should not contain the test
                    promotion CBID.

                EOT
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* general() */

}
