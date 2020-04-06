<?php

final class SCProductBuyView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v113.js', scliburl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBArtworkElement',
            'CBConvert',
            'CBImage',
            'CBModel',
            'CBUI',
            'SCCartItem',
            'SCShoppingCart',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[
            'hideImage' => CBModel::valueToBool(
                $spec,
                'hideImage'
            ),

            'productCode' => CBModel::valueAsName(
                $spec,
                'productCode'
            ),

            'showProductPageLink' => CBModel::valueToBool(
                $spec,
                'showProductPageLink'
            ),
        ];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(
        stdClass $model
    ): void {
        $productCode = CBModel::valueAsName(
            $model,
            'productCode'
        );

        if ($productCode === null) {
            return;
        }

        $updatedCartItemSpec = SCCartItem::update(
            (object)[
                'className' => 'SCProductCartItem',
                'productCode' => $productCode,
                'quantity' => 1,
            ]
        );

        $isNotAvailable = SCCartItem::getIsNotAvailable(
            $updatedCartItemSpec
        );

        if ($isNotAvailable) {
            return;
        }

        $informationAsJSON = json_encode(
            (object)[
                'hideImage' => CBModel::valueToBool(
                    $model,
                    'hideImage'
                ),

                'productPageURL' => SCProduct::productCodeToProductPageURL(
                    $productCode
                ),

                'showProductPageLink' => CBModel::valueToBool(
                    $model,
                    'showProductPageLink'
                ),
            ]
        );

        $updatedCartItemSpecAsJSON = json_encode($updatedCartItemSpec);

        $CSSClassNames = implode(
            ' ',
            [
                'SCProductBuyView',
                'CBUI_view',
                'CBUI_padding_standard',
                'CBUI_alignItems_end',
            ]
        );

        ?>

        <div
            class="<?= $CSSClassNames ?>"
            data-cart-item-spec="<?= cbhtml($updatedCartItemSpecAsJSON) ?>"
            data-information="<?= cbhtml($informationAsJSON) ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */

}
