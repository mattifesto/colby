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
            'SCPromotionExecutor_apply'
        );

        if ($callable === null) {
            $callable = CBModel::getClassFunction(
                $promotionExecutorModel,
                'CBPromotion_apply' /* @deprecated */
            );
        }

        if ($callable === null) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The class of this promotion executor model has not
                    implemented SCPromotionExecutor_apply().

                EOT),
                $promotionExecutorModel,
                'd50f0aac4115a42fb43589d045a7a44e79ff0773'
            );
        }

        $orderSpec = call_user_func(
            $callable,
            $promotionExecutorModel,
            $orderSpec
        );

        return $orderSpec;
    }
    /* apply() */

}
