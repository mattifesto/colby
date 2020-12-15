<?php

final class CBContainerView2Editor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.4.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBContainerView2Editor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews(),
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAjax',
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIImageChooser',
            'CBUIPanel',
            'CBUISpec',
            'CBUISpecArrayEditor',
            'CBUIStringEditor2',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBContainerView2Editor */
