<?php

final class CBImageLinkViewEditor {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(
                __CLASS__,
                'v656.js',
                cbsysurl()
            )
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAjax',
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
/* CBImageLinkViewEditor */
