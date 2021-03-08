<?php

final class
SCPromotionOffer_CBOrderDiscount {

    /* -- CBModel interfaces -- */



    /**
     * @param object $offerSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $offerSpec
    ): stdClass {
        $offerModel = (object)[];

        SCPromotionOffer::setCBMessage(
            $offerModel,
            SCPromotionOffer::getCBMessage(
                $offerSpec
            )
        );

        $discountInCents = SCPromotionOffer_CBOrderDiscount::getDiscountInCents(
            $offerSpec
        );

        if ($discountInCents < 0) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The discount in cents on this
                    SCPromotionOffer_CBOrderDiscount spec is not valid.

                EOT),
                $offerSpec,
                'f3199575edec8109094a4ec890e48c4e2400850c'
            );
        }

        SCPromotionOffer_CBOrderDiscount::setDiscountInCents(
            $offerModel,
            $discountInCents
        );

        return $offerModel;
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object $offerModel
     *
     * @return int
     */
    static function
    getDiscountInCents(
        stdClass $offerModel
    ): int {
        $discountInCents = CBModel::valueAsInt(
            $offerModel,
            'SCPromotionOffer_CBOrderDiscount_discountInCents'
        ) ?? 0;

        return max(
            $discountInCents,
            0
        );
    }
    /* getDiscountInCents() */



    /**
     * @see documentation
     *
     * @param object $offerSpec
     * @param int $discountInCents
     *
     * @return void
     */
    static function
    setDiscountInCents(
        stdClass $offerSpec,
        int $discountInCents
    ): void {
        $offerSpec->SCPromotionOffer_CBOrderDiscount_discountInCents = (
            $discountInCents
        );
    }
    /* getDiscountInCents() */

}
