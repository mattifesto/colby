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
        return ['CBUI', 'CBUIImageChooser', 'CBUISelector',
                'CBUISpecPropertyEditor', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            cbsysurl() . '/classes/CBViewPageInformationEditor/CBPageURIControl.js',
            cbsysurl() . '/classes/CBViewPageInformationEditor/CBPublicationControl.js',
            Colby::flexpath(__CLASS__, 'js', cbsysurl()),
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBCurrentUserID', ColbyUser::currentUserId()],
            ['CBPageClassNamesForKinds', CBPagesPreferences::classNamesForKinds()],
            ['CBPageClassNamesForLayouts', CBPagesPreferences::classNamesForLayouts()],
            ['CBPageClassNamesForSettings', CBPagesPreferences::classNamesForSettings()],
            ['CBUsersWhoAreAdministrators', CBViewPageInformationEditor::usersWhoAreAdministrators()],
            ['CBViewPageInformationEditor_mainMenuItemOptions', CBViewPageInformationEditor::fetchMainMenuItemOptions()],
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
