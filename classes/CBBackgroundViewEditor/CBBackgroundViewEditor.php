<?php

final class CBBackgroundViewEditor {


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v609.js', cbsysurl()),
        ];
    }



    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBBackgroundViewEditor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUISpec',
            'CBUISpecArrayEditor',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBBackgroundViewEditor */
