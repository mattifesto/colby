<?php

final class CBArtworkCollectionEditor {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v632.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBArtworkEditor',
            'CBModel',
            'CBUI',
            'CBUISpecArrayEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
