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
            ['CBArrayEditor', 'CBUI', 'CBUINavigationView', 'CBUISpecEditor',
             'CBUISpecSaver', 'CBViewPageInformationEditor']
        );
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBPageEditorAvailableViewClassNames', CBPagesPreferences::classNamesForAddableViews()],
            ['CBViewPageEditor_specID', cb_query_string_value('data-store-id')],
            ['CBViewPageEditor_specIDToCopy', cb_query_string_value('id-to-copy')],
        ];
    }
}
