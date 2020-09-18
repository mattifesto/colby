<?php

final class CBArtworkEditor {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v640.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBImage',
            'CBUI',
            'CBUIImageChooser',
            'CBUIPanel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
