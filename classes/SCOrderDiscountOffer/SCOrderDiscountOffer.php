<?php

final class
SCOrderDiscountOffer {

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

        SCOrderDiscountOffer::setCBMessage(
            $offerModel,
            SCOrderDiscountOffer::getCBMessage(
                $offerSpec
            )
        );

        $discountInCents = SCOrderDiscountOffer::getDiscountInCents(
            $offerSpec
        );

        if ($discountInCents < 0) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The discount in cents on this
                    SCOrderDiscountOffer spec is not valid.

                EOT),
                $offerSpec,
                'f3199575edec8109094a4ec890e48c4e2400850c'
            );
        }

        SCOrderDiscountOffer::setDiscountInCents(
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
     * @return string
     */
    static function
    getCBMessage(
        stdClass $offerModel
    ): string {
        return CBModel::valueToString(
            $offerModel,
            'SCOrderDiscountOffer_cbmessage'
        );
    }
    /* getCBMessage() */



    /**
     * @param object $offerSpec
     * @param string $cbmessage
     *
     * @return void
     */
    static function
    setCBMessage(
        stdClass $offerSpec,
        string $cbmessage
    ): void {
        $offerSpec->SCOrderDiscountOffer_cbmessage = $cbmessage;
    }
    /* setCBMessage() */



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
            'SCOrderDiscountOffer_discountInCents'
        ) ?? 0;

        return max(
            $discountInCents,
            0
        );
    }
    /* getDiscountInCents() */



    /**
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
        $offerSpec->SCOrderDiscountOffer_discountInCents = (
            $discountInCents
        );
    }
    /* getDiscountInCents() */

}
