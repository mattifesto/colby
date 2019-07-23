<?php

final class CBHideByUserGroupViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIBooleanEditor',
            'CBUISelector',
            'CBUISpecArrayEditor',
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v488.js', cbsysurl()),
        ];
    }


    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBHideByUserGroupViewEditor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews(),
            ],
            [
                'CBHideByUserGroupViewEditor_groupNames',
                ColbyUser::fetchGroupNames(),
            ],
        ];
    }
}
