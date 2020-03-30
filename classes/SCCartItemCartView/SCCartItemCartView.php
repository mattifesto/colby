<?php

final class SCCartItemCartView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v140.css', scliburl()),
        ];
    }



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
            'CBArtworkElement',
            'CBConvert',
            'CBImage',
            'CBMessageMarkup',
            'CBModel',
            'CBReleasable',
            'CBUI',
            'SCCartItem',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
