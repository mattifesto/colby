<?php

final class CBViewPageEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        $classNamesForEditableViewsAndLayouts = array_merge(
            CBPagesPreferences::classNamesForEditableViews(),
            CBPagesPreferences::classNamesForLayouts()
        );

        $classNamesForEditors = array_map(function ($className) {
            return "{$className}Editor";
        }, $classNamesForEditableViewsAndLayouts);

        return array_merge(
            $classNamesForEditors,
            [
                'CBUI',
                'CBUINavigationArrowPart',
                'CBUINavigationView',
                'CBUISectionItem4',
                'CBUISpecArrayEditor',
                'CBUISpecEditor',
                'CBUISpecSaver',
                'CBUITitleAndDescriptionPart',
                'CBViewPageInformationEditor',
            ]
        );
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v450.js', cbsysurl()),
        ];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBViewPageEditor_addableClassNames', CBPagesPreferences::classNamesForAddableViews()],
        ];
    }
}
