<?php

final class CBHideByUserGroupViewEditor {

    /* CBHTMLOutput intefaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v592.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $userGroupClassNames = array_map(
            function ($userGroupModel) {
                return $userGroupModel->userGroupClassName;
            },
            CBUserGroup::fetchCBUserGroupModels()
        );

        return [
            [
                'CBHideByUserGroupViewEditor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews(),
            ],
            [
                'CBHideByUserGroupViewEditor_userGroupClassNames',
                $userGroupClassNames,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBModel',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUISelector',
            'CBUISpecArrayEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
