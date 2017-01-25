<?php

final class CBHideByUserGroupViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIBooleanEditor', 'CBUISelector'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBHideByUserGroupViewEditorAddableViews', CBPagesPreferences::classNamesForAddableViews()],
            ['CBHideByUserGroupViewEditorGroupNames', ColbyUser::fetchGroupNames()]
        ];
    }
}
