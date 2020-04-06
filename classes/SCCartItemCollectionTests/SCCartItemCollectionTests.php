<?php

final class SCCartItemCollectionTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v111.js', scliburl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBModel',
            'CBTest',
            'SCCartItem',
            'SCCartItemCollection',

            /**
             * This class is not used directly, but the test cart items are
             * SCProductCartItem models.
             */
            'SCProductCartItem',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_JavaScriptTests(): array {
        return [
            ['SCCartItemCollection', 'fetchCartItem'],
            ['SCCartItemCollection', 'replaceCartItems'],
        ];
    }
    /* CBTest_JavaScriptTests() */
}
