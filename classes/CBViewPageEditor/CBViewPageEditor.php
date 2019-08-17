<?php

final class CBViewPageEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v483.js', cbsysurl()),
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
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
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
                'CBImage',
                'CBModel',
                'CBUI',
                'CBUISpecArrayEditor',
                'CBViewPageInformationEditor',
                'Colby',
            ]
        );
    }
    /* CBHTMLOutput_requiredClassNames() */
}
/* CBViewPageEditor */
