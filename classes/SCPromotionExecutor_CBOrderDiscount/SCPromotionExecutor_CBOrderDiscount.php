<?php

final class
SCPromotionExecutor_CBOrderDiscount {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_configure(
    ): void {
        $updater = new CBModelUpdater(
            SCPromotionExecutor_CBOrderDiscount::getRegistrationModelCBID()
        );

        CBModel::setClassName(
            $updater->getSpec(),
            'SCPromotionExecutorRegistration'
        );

        SCPromotionExecutorRegistration::setDescriptionCBMessage(
            $updater->getSpec(),
            <<<EOT

                A promotion using this executor will apply a percentage discount
                to the items subtotal of the order.

            EOT
        );

        SCPromotionExecutorRegistration::setExecutorClassName(
            $updater->getSpec(),
            __CLASS__
        );

        SCPromotionExecutorRegistration::setTitle(
            $updater->getSpec(),
            'Percentage Discount'
        );

        CBDB::transaction(
            function () use (
                $updater
            ) {
                $updater->save2();
            }
        );
    }
    /* CBInstall_configure() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $promotionExecutorSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $promotionExecutorSpec
    ): stdClass {
        $promotionExecutorModel = (object)[];

        SCPromotionExecutor_CBOrderDiscount::setIsWholesale(
            $promotionExecutorModel,
            SCPromotionExecutor_CBOrderDiscount::getIsWholesale(
                $promotionExecutorSpec
            )
        );

        SCPromotionExecutor_CBOrderDiscount::setMinimumSubtotalInCents(
            $promotionExecutorModel,
            SCPromotionExecutor_CBOrderDiscount::getMinimumSubtotalInCents(
                $promotionExecutorSpec
            )
        );


        SCPromotionExecutor_CBOrderDiscount::setPercentDiscount(
            $promotionExecutorModel,
            SCPromotionExecutor_CBOrderDiscount::getPercentDiscount(
                $promotionExecutorSpec
            )
        );

        return $promotionExecutorSpec;
    }
    /* CBModel_build() */



    /* -- SCPromotionExecutor interfaces -- */



    /**
     * @param object $promotionExecutorModel
     * @param object $orderSpec
     *
     * @return object
     */
    static function
    SCPromotionExecutor_generateOrderDiscountOffer(
        stdClass $promotionExecutorModel,
        stdClass $orderSpec
    ): ?stdClass {
        $promotionIsForWholesaleOrders = (
            SCPromotionExecutor_CBOrderDiscount::getIsWholesale(
                $promotionExecutorModel
            )
        );

        $orderIsWholesale = SCOrder::getIsWholesale(
            $orderSpec
        );

        if ($promotionIsForWholesaleOrders !== $orderIsWholesale) {
            return null;
        }

        $promotionMinimumSubtotalInCents = (
            SCPromotionExecutor_CBOrderDiscount::getMinimumSubtotalInCents(
                $promotionExecutorModel
            )
        );

        $orderSubtotalInCents = SCOrder::getSubtotalInCents(
            $orderSpec
        );

        if ($orderSubtotalInCents < $promotionMinimumSubtotalInCents) {
            return null;
        }

        $offerSpec = CBModel::createSpec(
            'SCOrderDiscountOffer'
        );

        $percentDiscount = (
            SCPromotionExecutor_CBOrderDiscount::getPercentDiscount(
                $promotionExecutorModel
            )
        );

        $discountInCents = round(
            $orderSubtotalInCents * ($percentDiscount / 100)
        );

        SCOrderDiscountOffer::setDiscountInCents(
            $offerSpec,
            $discountInCents
        );

        return $offerSpec;
    }
    /* SCPromotionExecutor_generateOrderDiscountOffer() */



    /* -- accessors -- */



    /**
     * @param object $promotionExecutorModel
     *
     * @return bool
     */
    static function
    getIsWholesale(
        stdClass $promotionExecutorModel
    ): bool {
        return CBModel::valueToBool(
            $promotionExecutorModel,
            'SCPromotionExecutor_CBOrderDiscount_isWholesale'
        );
    }
    /* getIsWholesale() */



    /**
     * @param object $promotionExecutorSpec
     * @param bool $isWholesale
     *
     * @return void
     */
    static function
    setIsWholesale(
        stdClass $promotionExecutorSpec,
        bool $isWholesale
    ): void {
        $promotionExecutorSpec
        ->SCPromotionExecutor_CBOrderDiscount_isWholesale = (
            $isWholesale
        );
    }
    /* setIsWholesale() */



    /**
     * @param object $promotionExecutorModel
     *
     * @return int
     */
    static function
    getMinimumSubtotalInCents(
        stdClass $promotionExecutorModel
    ): int {
        return CBModel::valueAsInt(
            $promotionExecutorModel,
            'SCPromotionExecutor_CBOrderDiscount_minimumSubtotalInCents'
        ) ?? 0;
    }
    /* getMinimumSubtotalInCents() */



    /**
     * @param object $promotionExecutorSpec
     * @param int $minimumSubtotalInCents
     *
     * @return void
     */
    static function
    setMinimumSubtotalInCents(
        stdClass $promotionExecutorSpec,
        int $minimumSubtotalInCents
    ): void {
        $promotionExecutorSpec
        ->SCPromotionExecutor_CBOrderDiscount_minimumSubtotalInCents = (
            $minimumSubtotalInCents
        );
    }
    /* setMinimumSubtotalInCents() */



    /**
     * @param object $promotionExecutorModel
     *
     * @return float
     */
    static function
    getPercentDiscount(
        stdClass $promotionExecutorModel
    ): float {
        return CBModel::valueAsNumber(
            $promotionExecutorModel,
            'SCPromotionExecutor_CBOrderDiscount_percentDiscount'
        ) ?? 0.0;
    }
    /* getPercentDiscount() */



    /**
     * @param object $promotionExecutorSpec
     * @param float $percentDiscount
     *
     * @return void
     */
    static function
    setPercentDiscount(
        stdClass $promotionExecutorSpec,
        float $percentDiscount
    ): void {
        $promotionExecutorSpec
        ->SCPromotionExecutor_CBOrderDiscount_percentDiscount = (
            $percentDiscount
        );
    }
    /* setPercentDiscount() */



    /* -- functions -- */



    /**
     * @return CBID
     */
    static function
    getRegistrationModelCBID(
    ): string {
        return '1f1e234e294d7985125af5c5a40e1c337382fa68';
    }
    /* getRegistrationModelCBID() */

}
