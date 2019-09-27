<?php

final class CBImageLinkViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl())
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBErrorHandler',
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
/* CBImageLinkViewEditor */
