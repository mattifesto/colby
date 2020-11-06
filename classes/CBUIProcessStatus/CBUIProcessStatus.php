<?php

final class CBUIProcessStatus {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v363.css', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v660.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAjax',
            'CBConvert',
            'CBLog',
            'CBUIExpander',
            'CBUIPanel',
            'Colby',
        ];
    }
}
