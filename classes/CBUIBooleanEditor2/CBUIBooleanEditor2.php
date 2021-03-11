<?php

/**
 * @see documentation
 */
final class
CBUIBooleanEditor2 {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.14.js',
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
            'CBUIBooleanSwitchPart'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
