<?php

final class SCCartItemCartView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_07_21_1658420444',
                'css',
                cbsysurl()
            ),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_07_21_1658420445',
                'js',
                cbsysurl()
            ),
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
