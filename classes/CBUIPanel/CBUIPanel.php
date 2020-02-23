<?php

final class CBUIPanel {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v525.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v578.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBException',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'Colby',

            'CBContentStyleSheet',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBUIPanel */
