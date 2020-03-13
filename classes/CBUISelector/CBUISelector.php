<?php

final class CBUISelector {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v590.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUINavigationView',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
