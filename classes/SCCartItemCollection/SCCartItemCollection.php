<?php

final class SCCartItemCollection {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v93.js', scliburl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBActiveObject',
            'CBConvert',
            'CBEvent',
            'CBException',
            'CBModel',
            'SCCartItem',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
