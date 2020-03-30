<?php

final class SCFreeFormCartItem {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v140.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',

            /**
             * SCCartItem APIs are not used, but SCCartItem should be required
             * if SCProductCartItem is required.
             */

            'SCCartItem',
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * SCProductCartItem is always installed, but will will not be usable unless
     * SCProduct models are added.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        SCPreferences::installCartItemClass(__CLASS__);
    }



    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'SCPreferences',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $cartItemSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $cartItemSpec
    ): stdClass {
        $descriptionAsText = CBModel::valueToString(
            $cartItemSpec,
            'descriptionAsText'
        );

        $cbmessage = CBModel::valueToString(
            $cartItemSpec,
            'message'
        );

        $priceInCents = CBModel::valueAsInt(
            $cartItemSpec,
            'priceInCents'
        );

        $productCBID = CBModel::valueAsCBID(
            $cartItemSpec,
            'productCBID'
        );

        $quantity = CBModel::valueAsInt(
            $cartItemSpec,
            'quantity'
        );

        $title = CBModel::valueToString(
            $cartItemSpec,
            'title'
        );

        $unitPriceInCents = CBModel::valueAsInt(
            $cartItemSpec,
            'unitPriceInCents'
        );

        return (object)[
            'descriptionAsText' => $descriptionAsText,
            'message' => $cbmessage,
            'priceInCents' => $priceInCents,
            'productCBID' => $productCBID,
            'quantity' => $quantity,
            'title' => $title,
            'unitPriceInCents' => $unitPriceInCents,
        ];
    }
    /* CBModel_build() */



    /* -- SCCartItem interfaces -- -- -- -- -- */



    /**
     * @param object $cartItemModel
     *
     * @return float
     */
    static function SCCartItem_getMaximumQuantity(
        stdClass $cartItemModel
    ): float {
        return 1.0;
    }
    /* SCCartItem_getQuantity() */



    /**
     * @param object $cartItemModelA
     * @param object $cartItemModelB
     *
     * @return bool
     */
    static function SCCartItem_specsRepresentTheSameProduct(
        stdClass $cartItemModelA,
        stdClass $cartItemModelB
    ): bool {
        $productCBIDA = CBModel::valueAsCBID(
            $cartItemModelA,
            'productCBID'
        );

        $productCBIDB = CBModel::valueAsCBID(
            $cartItemModelB,
            'productCBID'
        );

        return $productCBIDA === $productCBIDB;
    }
    /* SCCartItem_specsRepresentTheSameProduct() */



    /**
     * @return object
     */
    static function SCCartItem_update(
        stdClass $originalCartItemSpec
    ): stdClass {
        $productCBID = CBModel::valueAsCBID(
            $originalCartItemSpec,
            'productCBID'
        );

        if ($productCBID === null) {
            throw new CBExceptionWithValue(
                'The "productCBID" on this cart item is not valid.',
                $originalCartItemSpec,
                'deccc393edb4e23e7fff4cf7def0b322684695b5'
            );
        }

        $unitPriceInCents = CBModel::valueAsInt(
            $originalCartItemSpec,
            'unitPriceInCents'
        );

        if (
            $unitPriceInCents === null ||
            $unitPriceInCents < 1
        ) {
            throw new CBExceptionWithValue(
                'The "unitPriceInCents" on this cart item is not valid.',
                $originalCartItemSpec,
                '23844789f9107e6171195f8a5831d452135a2261'
            );
        }

        $quantity = SCCartItem::getQuantity(
            $originalCartItemSpec
        );

        $descriptionAsText = CBModel::valueToString(
            $originalCartItemSpec,
            "descriptionAsText"
        );

        if (empty(trim($descriptionAsText))) {
            $cbmessage = '';
        } else {
            $cbmessage = <<<EOT

                --- pre prewrap\n{$descriptionAsText}
                ---

            EOT;
        }

        return (object)[
            'className' => __CLASS__,

            'descriptionAsText' => $descriptionAsText,

            'message' => $cbmessage,

            'priceInCents' => $unitPriceInCents * $quantity,

            'productCBID' => $productCBID,

            'quantity' => $quantity,

            'title' => CBConvert::stringToCleanLine(
                CBModel::valueToString(
                    $originalCartItemSpec,
                    'title'
                )
            ),

            'unitPriceInCents' => $unitPriceInCents,
        ];
    }
    /* SCCartItem_update() */

}
