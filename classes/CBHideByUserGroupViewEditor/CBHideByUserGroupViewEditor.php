<?php

final class CBHideByUserGroupViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIBooleanEditor', 'CBUISelector'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBHideByUserGroupViewEditorAddableViews', CBPagesPreferences::classNamesForAddableViews()],
            ['CBHideByUserGroupViewEditorGroupNames', ColbyUser::fetchGroupNames()]
        ];
    }
}
