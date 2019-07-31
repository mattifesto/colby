<?php

final class CBYouTubeViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUISelector',
            'CBUIStringEditor'
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v494.js', cbsysurl()),
        ];
    }
}
