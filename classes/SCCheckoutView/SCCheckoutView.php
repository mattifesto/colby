<?php

final class SCCheckoutView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v84.css', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v654.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::getModelCBID()
        );

        $cartItemClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemClassNames'
        );

        $cartItemCheckoutViewClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemCheckoutViewClassNames'
        );

        return array_merge(
            [
                'CBConvert',
                'CBMessageMarkup',
                'CBModel',
                'CBUI',
                'CBUIPanel',
                'CBUISection',
                'CBUISectionItem4',
                'CBUIStringEditor',
                'CBUIStringsPart',
                'SCCartItem',
                'SCShoppingCart',
                'SCStripe',

                'CBContentStyleSheet',
            ],
            $cartItemClassNames,
            $cartItemCheckoutViewClassNames
        );
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[];
    }



    /* -- CBView  interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        ?>

        <div class="SCCheckoutView CBUIRoot">
            <h1>Checkout</h1>

            <div class="SCCheckoutView_paymentFormsContainer">
            </div>
        </div>

        <?php
    }
    /* CBView_render() */

}
