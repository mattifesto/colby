<?php

final class
CBViewPageEditor {

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
                'v675.38.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBViewPageEditor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews()
            ],
            [
                'CBViewPageEditor_currentFrontPageID',
                CBSitePreferences::frontPageID(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        $classNamesForEditableViewsAndLayouts = array_merge(
            CBPagesPreferences::classNamesForEditableViews(),
            CBPagesPreferences::classNamesForLayouts()
        );

        $classNamesForEditors = array_map(
            function ($className) {
                return "{$className}Editor";
            },
            $classNamesForEditableViewsAndLayouts
        );

        return array_merge(
            $classNamesForEditors,
            [
                'CB_Brick_Button',
                'CBAjax',
                'CBConvert',
                'CBErrorHandler',
                'CBImage',
                'CBModel',
                'CBUI',
                'CBUIPanel',
                'CBUISpecArrayEditor',
                'CBViewPageInformationEditor',
            ]
        );
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBViewPageEditor */
