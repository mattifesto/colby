<?php

final class SCShoppingCartView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v147.css', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v663.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::ID()
        );

        $cartItemClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemClassNames'
        );

        $cartItemCartViewClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemCartViewClassNames'
        );

        return array_merge(
            [
                'CBAjax',
                'CBConvert',
                'CBErrorHandler',
                'CBMessageMarkup',
                'CBUI',
                'CBUIPanel',
                'Colby',
                'SCCartItem',
                'SCCartItemCartView',
                'SCShoppingCart',
            ],
            $cartItemClassNames,
            $cartItemCartViewClassNames
        );
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[];
    }



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBView_render(): void {
        ?>

        <div class="SCShoppingCartView CBUIRoot">
            <?php

            CBView::renderSpec(
                (object)[
                    'className' => 'CBPageTitleAndDescriptionView',
                    'hideDescription' => true,
                ]
            );

            ?>
        </div>

        <?php
    }
    /* CBView_render() */

}
