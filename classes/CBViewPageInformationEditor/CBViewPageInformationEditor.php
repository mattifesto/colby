<?php

final class CBViewPageInformationEditor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v514.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBPageClassNamesForLayouts',
                CBPagesPreferences::classNamesForLayouts()
            ],
            [
                'CBUsersWhoAreAdministrators',
                CBViewPageInformationEditor::usersWhoAreAdministrators()
            ],
            [
                'CBViewPageInformationEditor_currentUserNumericID',
                ColbyUser::currentUserId()
            ],
            [
                'CBViewPageInformationEditor_frameClassNames',
                CBPageFrameCatalog::fetchClassNames()
            ],
            [
                'CBViewPageInformationEditor_kindClassNames',
                CBPageKindCatalog::fetchClassNames()
            ],
            [
                'CBViewPageInformationEditor_pagesAdminURL',
                CBAdmin::getAdminPageURL('CBAdminPageForPagesFind'),
            ],
            [
                'CBViewPageInformationEditor_settingsClassNames',
                CBPageSettingsCatalog::fetchClassNames()
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
            'CBModel',
            'CBUI',
            'CBUIActionLink',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUISpecPropertyEditor',
            'CBUIStringEditor',
            'CBUIStringsPart',
            'CBUIUnixTimestampEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames */


    /* -- functions -- -- -- -- -- */

    /**
     * @return [{stdClass}]
     */
    private static function usersWhoAreAdministrators() {
        $SQL = <<<EOT

            SELECT `user`.`ID`, `user`.`facebookName` as `name`
            FROM `ColbyUsers` AS `user`
            JOIN `ColbyUsersWhoAreAdministrators` AS `administrator`
            ON `user`.`ID` = `administrator`.`userID`

EOT;

        return CBDB::SQLToObjects($SQL);
    }
}
