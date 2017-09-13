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
    static function requiredClassNames() {
        return ['CBUI', 'CBUIImageChooser', 'CBUISelector', 'CBUISpecPropertyEditor', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBViewPageInformationEditor::URL('CBViewPageInformationEditor.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBPageURIControl.js',
            CBSystemURL . '/javascript/CBPublicationControl.js',
            CBViewPageInformationEditor::URL('CBViewPageInformationEditor.js'),
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
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
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
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
