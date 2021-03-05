<?php

final class SCPromotion {

    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $promotionSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $promotionSpec
    ): stdClass {

        /**
         * The default beginTimestamp value is zero.
         */

        $beginTimestamp = CBModel::valueAsInt(
            $promotionSpec,
            'beginTimestamp'
        ) ?? 0;


        /**
         * The defaul endTimestamp value is the beginTimestamp value, which
         * means by default a promotion has no duration.
         */

        $endTimestamp = CBModel::valueAsInt(
            $promotionSpec,
            'endTimestamp'
        ) ?? $beginTimestamp;


        /* executor */

        $executorSpec = CBModel::valueAsModel(
            $promotionSpec,
            'executor'
        );

        if ($executorSpec === null) {
            $executorModel = null;
        } else {
            $executorModel = CBModel::build(
                $executorSpec
            );
        }


        /* done */

        return (object)[
            'beginTimestamp' => $beginTimestamp,
            'endTimestamp' => $endTimestamp,
            'executor' => $executorModel,
        ];
    }
    /* CBModel_build() */



    /* -- CBModels interfaces -- -- -- -- -- */



    /**
     * @param [CBID] $CBIDs
     *
     * @return void
     */
    static function CBModels_willDelete(
        array $promotionCBIDs
    ): void {
        foreach ($promotionCBIDs as $promotionCBID) {
            SCPromotionsTable::deletePromotionByCBID(
                $promotionCBID
            );
        }
    }
    /* CBModels_willDelete() */



    /**
     * @param [object] $promotionModels
     *
     * @return void
     */
    static function CBModels_willSave(
        array $promotionModels
    ): void {
        foreach ($promotionModels as $promotionModel) {
            SCPromotionsTable::insertPromotion(
                $promotionModel
            );
        }
    }
    /* CBModels_willSave() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param object $promotionModel
     * @param object $orderSpec
     *
     * @return object
     */
    static function
    apply(
        stdClass $promotionModel,
        stdClass $orderSpec
    ): stdClass {
        $orderSpec = CBModel::clone(
            $orderSpec
        );

        $promotionExecutorModel = CBModel::valueAsModel(
            $promotionModel,
            'executor'
        );

        $orderSpec = SCPromotionExecutor::apply(
            $promotionExecutorModel,
            $orderSpec
        );

        return $orderSpec;
    }
    /* apply() */

}
