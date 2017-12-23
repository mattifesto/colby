<?php

final class CBHideByUserGroupViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIBooleanEditor', 'CBUISelector'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBHideByUserGroupViewEditorAddableViews', CBPagesPreferences::classNamesForAddableViews()],
            ['CBHideByUserGroupViewEditorGroupNames', ColbyUser::fetchGroupNames()]
        ];
    }
}
