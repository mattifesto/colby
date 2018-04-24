<?php

final class CBViewPageInformationEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUIActionLink',
            'CBUIImageChooser',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUISpecPropertyEditor',
            'CBUIStringEditor',
            'CBUIStringsPart',
            'CBUIUnixTimestampEditor'
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v420.js', cbsysurl())];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBCurrentUserID', ColbyUser::currentUserId()],
            ['CBPageClassNamesForLayouts', CBPagesPreferences::classNamesForLayouts()],
            ['CBUsersWhoAreAdministrators', CBViewPageInformationEditor::usersWhoAreAdministrators()],
            ['CBViewPageInformationEditor_frameClassNames', CBPageFrameCatalog::fetchClassNames()],
            ['CBViewPageInformationEditor_kindClassNames', CBPageKindCatalog::fetchClassNames()],
            ['CBViewPageInformationEditor_settingsClassNames', CBPageSettingsCatalog::fetchClassNames()],
        ];
    }

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
