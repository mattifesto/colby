<?php

final class CBUIPanel {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v525.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v531.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBContentStyleSheet',

            'CBConvert',
            'CBException',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
/* CBUIPanel */
