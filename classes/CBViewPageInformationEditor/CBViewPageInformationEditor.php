<?php

final class CBViewPageInformationEditor {

    /**
     * @return [object]
     */
    static function fetchMainMenuItemOptions() {
        $mainMenu = CBModels::fetchModelByID(CBWellKnownMenuForMain::ID());

        $options = [(object)[
            'title' => 'None',
        ]];

        foreach ($mainMenu->items as $item) {
            $options[] = (object)[
                'title' => $item->text,
                'value' => $item->name,
            ];
        }

        return $options;
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUIImageChooser',
                'CBUISectionItem4', 'CBUISelector', 'CBUISpecPropertyEditor',
                'CBUIStringEditor', 'CBUIStringsPart',
                'CBUIUnixTimestampEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v416.js', cbsysurl())];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBCurrentUserID', ColbyUser::currentUserId()],
            ['CBPageClassNamesForKinds', CBPagesPreferences::classNamesForKinds()],
            ['CBPageClassNamesForLayouts', CBPagesPreferences::classNamesForLayouts()],
            ['CBUsersWhoAreAdministrators', CBViewPageInformationEditor::usersWhoAreAdministrators()],
            ['CBViewPageInformationEditor_frameClassNames', CBPageFrameCatalog::fetchClassNames()],
            ['CBViewPageInformationEditor_mainMenuItemOptions', CBViewPageInformationEditor::fetchMainMenuItemOptions()],
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
