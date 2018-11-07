<?php

final class CBUISelector {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUINavigationArrowPart',
            'CBUINavigationView',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v466.js', cbsysurl()),
        ];
    }
}
