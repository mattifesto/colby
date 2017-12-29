<?php

final class CBHideByUserGroupViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIBooleanEditor', 'CBUISelector',
                'CBUISpecArrayEditor'];
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
            ['CBHideByUserGroupViewEditor_addableClassNames', CBPagesPreferences::classNamesForAddableViews()],
            ['CBHideByUserGroupViewEditor_groupNames', ColbyUser::fetchGroupNames()]
        ];
    }
}
