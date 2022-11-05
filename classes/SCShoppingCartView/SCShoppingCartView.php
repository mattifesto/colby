<?php

final class
SCShoppingCartView
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            Colby::flexpath(__CLASS__, 'v147.css', scliburl()),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            Colby::flexpath(__CLASS__, 'v663.js', scliburl()),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
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

        $arrayOfRequiredClassNames =
        array_merge(
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

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



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
