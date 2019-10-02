<?php

final class CBArtworkViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v530.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBImage',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'CBUIImageChooser',
            'CBUISelector',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
/* CBArtworkViewEditor */
