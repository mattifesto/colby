<?php

final class CBYouTubeViewEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v614.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',
            'CBUI',
            'CBUISelector',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
