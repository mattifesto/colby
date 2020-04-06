<?php

final class SCShoppingCartTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v124.js', scliburl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBActiveObject',
            'CBModel',
            'CBModels',
            'CBTest',
            'SCCartItem',
            'SCShoppingCart',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'mainCartItemQuantity',
            ],
            (object)[
                'name' => 'empty',
            ],
            (object)[
                'name' => 'mainCartItemSpecs',
            ],
            (object)[
                'type' => 'interactive',
                'name' => 'addJunkItemsToMainCart',
            ],
        ];
    }
    /* CBTest_getTests() */

}
/* SCShoppingCartTests */
