<?php

final class CBThemeEditor {

    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.7.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
     static function
     CBHTMLOutput_requiredClassNames(
     ): array {
        return [
            'CBModel',
            'CBUI',
            'CBUIStringEditor2'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
