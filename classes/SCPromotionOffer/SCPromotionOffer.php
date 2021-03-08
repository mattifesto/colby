<?php

final class
SCPromotionOffer {

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
            'SCPromotionOffer_cbmessage'
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
        $offerSpec->SCPromotionOffer_cbmessage = $cbmessage;
    }
    /* setCBMessage() */

}
