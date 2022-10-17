<?php

final class
CBViewPageEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_17_1666013780',
                'js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



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
                'CB_UI_StringEditor',
                'CBAjax',
                'CBConvert',
                'CBErrorHandler',
                'CBImage',
                'CBModel',
                'CBUI',
                'CBUIButton',
                'CBUIPanel',
                'CBUISpecArrayEditor',
                'CBViewPageInformationEditor',
            ]
        );
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBViewPageEditor */
