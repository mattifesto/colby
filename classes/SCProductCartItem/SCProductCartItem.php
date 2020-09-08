<?php

final class SCProductCartItem  {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v124.js', scliburl()),
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

        /* price */

        $priceInCents = CBModel::valueAsInt(
            $cartItemSpec,
            'priceInCents'
        );

        if ($priceInCents === null || $priceInCents < 0) {
            $message = CBConvert::stringToCleanLine(<<<EOT

                The "priceInCents" property on an SCProductCartItem spec should
                be an integer >= 0.

            EOT);

            throw new CBExceptionWithValue(
                $message,
                $cartItemSpec,
                '59bba497eaa5480b037627e314f9801f4836edc4'
            );
        }


        /* product code */

        $productCode = CBModel::valueAsName(
            $cartItemSpec,
            'productCode'
        );

        if ($productCode === null) {
            $message = CBConvert::stringToCleanLine(<<<EOT

                The "productCode" property on an SCProductCartItem is not valid.

            EOT);

            throw new CBExceptionWithValue(
                $message,
                $cartItemSpec,
                '9b6926170f7318e5a60324fc79b3e0d57b9338f2'
            );
        }


        /* quantity */

        $quantity = CBModel::valueAsInt(
            $cartItemSpec,
            'quantity'
        );

        if ($quantity === null || $quantity < 1) {
            $message = CBConvert::stringToCleanLine(<<<EOT

                The "quantity" property on an SCProductCartItem spec should be
                set to an integer > 0.

            EOT);

            throw new CBExceptionWithValue(
                $message,
                $cartItemSpec,
                '2396ab7815bf7ca3fca9827a95835b909d5f013b'
            );
        }


        /* done */

        return (object)[
            'image' => CBModel::valueAsModel(
                $cartItemSpec,
                'image'
            ),

            'message' => CBModel::valueToString(
                $cartItemSpec,
                'message'
            ),

            'priceInCents' => $priceInCents,

            'productCode' => $productCode,

            'quantity' => $quantity,

            'title' => CBModel::valueToString(
                $cartItemSpec,
                'title'
            ),
        ];
    }
    /* CBModel_build() */



    /* -- SCCartItem interfaces -- -- -- -- -- */



    /**
     * @param object $specA
     * @param object $specB
     *
     * @return bool
     */
    static function SCCartItem_specsRepresentTheSameProduct(
        stdClass $specA,
        stdClass $specB
    ): bool {
        return (
            CBModel::valueToString($specA, 'productCode') ===
            CBModel::valueToString($specB, 'productCode')
        );
    }
    /* SCCartItem_specsRepresentTheSameProduct() */



    /**
     * @return object
     */
    static function SCCartItem_update(stdClass $originalCartItemSpec): stdClass {
        $productCode = CBModel::valueAsName(
            $originalCartItemSpec,
            'productCode'
        );

        if ($productCode === null) {
            throw CBException::createModelIssueException(
                'The original cart item spec does not have a valid ' .
                'productCode property value.',
                $originalCartItemSpec,
                '52115cbde7c236f1fbf9443040e6c61e5cacac7f'
            );
        }

        $productModel = CBModelCache::fetchModelByID(
            SCProduct::productCodeToProductID($productCode)
        );

        if ($productModel === null) {
            $message = <<<EOT

                This item was removed because its product code is not
                recognized.

            EOT;

            $updatedCartItemSpec = CBModel::clone(
                $originalCartItemSpec
            );

            $updatedCartItemSpec->isNotAvailable = true;
            $updatedCartItemSpec->message = $message;

            $updatedCartItemSpec->sourceID = (
                '4457ab28249588293c2878856e8f014c99472fb6'
            );

            return $updatedCartItemSpec;
        }

        if (CBModel::valueToBool($productModel, 'isNotAvailable') === true) {
            $message = <<<EOT

                This item was removed because it is not available.

            EOT;

            $updatedCartItemSpec = CBModel::clone(
                $originalCartItemSpec
            );

            $updatedCartItemSpec->isNotAvailable = true;
            $updatedCartItemSpec->message = $message;

            $updatedCartItemSpec->sourceID = (
                '07a670344f89516b1cdb9434a2cf1c80edb7d6ea'
            );

            return $updatedCartItemSpec;
        }

        $unitPriceInCents = CBModel::valueAsInt($productModel, 'priceInCents');

        if ($unitPriceInCents === null) {
            throw CBException::createModelIssueException(
                'The product model does not have a valid priceInCents ' .
                'property value.',
                $productModel,
                '5e2b33e807b5cfc111f56c3f0ce47732b4ceb449'
            );
        }

        $associatedImageModel = CBModelAssociations::fetchAssociatedModel(
            $productModel->ID,
            'CBModelToCBImageAssociation'
        );

        $unitPriceInDollars = CBConvert::centsToDollars($unitPriceInCents);
        $quantity = SCCartItem::getQuantity($originalCartItemSpec);
        $message = '';

        if ($quantity > 1) {
            $message = <<<EOT

                --- dl
                    --- dt
                    Unit Price
                    ---
                    \${$unitPriceInDollars}
                ---

            EOT;
        }

        $updatedCartItemSpec = (object)[
            'className' => __CLASS__,
            'image' => $associatedImageModel,
            'message' => $message,
            'priceInCents' => $unitPriceInCents * $quantity,
            'productCode' => $productCode,
            'quantity' => $quantity,
            'title' => CBModel::valueToString($productModel, 'title'),
            'unitPriceInCents' => $unitPriceInCents,
        ];

        return $updatedCartItemSpec;
    }
    /* SCCartItem_update() */

}
/* SCProductCartItem */
