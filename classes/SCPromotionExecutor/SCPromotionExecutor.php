<?php

final class
SCPromotionExecutor {

    /* -- functions -- */



    /**
     * @param object $promotionExecutorModel
     * @param object $orderSpec
     *
     * @return object
     *
     *      This function returns an updated order spec.
     *
     * @NOTE 2021_02_22
     *
     *      A promotion executor has a ton of power to make any changes to an
     *      order spec that it wants. In the future, we may find ways to limit
     *      and control its power.
     *
     * @TODO 2021_02_22
     *
     *      For now the interface is allowed to return the same order spec that
     *      it was passed, even it it makes modifications to the order spec. I'm
     *      not sure if this is good or bad. It's probaby fine because since we
     *      pass the order spec in they can already make changes to it.
     */
    static function
    apply(
        stdClass $promotionExecutorModel,
        stdClass $orderSpec
    ): stdClass {
        $callable = CBModel::getClassFunction(
            $promotionExecutorModel,
            'CBPromotion_apply' /* @deprecated 2021_03_02 */
        );

        if ($callable !== null) {
            $orderSpec = call_user_func(
                $callable,
                $promotionExecutorModel,
                $orderSpec
            );
        }

        $orderSpec = SCPromotionExecutor::applyOrderDiscountPromotion(
            $promotionExecutorModel,
            $orderSpec
        );

        return $orderSpec;
    }
    /* apply() */



    /**
     * @param object $promotionExecutorModel
     * @param object $orderSpec
     *
     * @return object
     */
    private static function
    applyOrderDiscountPromotion(
        stdClass $originalPromotionExecutorModel,
        stdClass $originalOrderSpec
    ): stdClass {
        $orderSpec = $originalOrderSpec;

        $callable = CBModel::getClassFunction(
            $originalPromotionExecutorModel,
            'SCPromotionExecutor_generateOrderDiscountOffer'
        );

        if ($callable !== null) {
            $offerSpec = call_user_func(
                $callable,
                CBModel::clone($originalPromotionExecutorModel),
                CBModel::clone($orderSpec)
            );

            if ($offerSpec !== null) {
                $offerModel = CBModel::build(
                    $offerSpec
                );

                $offerDiscountInCents = (
                    SCOrderDiscountOffer::getDiscountInCents(
                        $offerModel
                    )
                );

                $originalOrderDisountInCents = SCOrder::getDiscountInCents(
                    $originalOrderSpec
                );

                if ($offerDiscountInCents > $originalOrderDisountInCents) {
                    SCOrder::setDiscountInCents(
                        $orderSpec,
                        $offerDiscountInCents
                    );
                }
            }
        }

        return $orderSpec;
    }
    /* applyOrderDiscountPromotion() */

}
