<?php

final class CBPageTitleAndDescriptionViewEditor {

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
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
