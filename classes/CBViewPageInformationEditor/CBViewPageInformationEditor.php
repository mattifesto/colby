<?php

final class CBViewPageInformationEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBImageEditor', 'CBUI', 'CBUISelector', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBViewPageInformationEditor::URL('CBViewPageInformationEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBPageURIControl.js',
            CBSystemURL . '/javascript/CBPublicationControl.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBViewPageInformationEditor::URL('CBViewPageInformationEditor.js'),
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBCurrentUserID', ColbyUser::currentUserId()],
            ['CBPageClassNamesForKinds', CBPagesPreferences::classNamesForKinds()],
            ['CBPageClassNamesForLayouts', CBPagesPreferences::classNamesForLayouts()],
            ['CBPageClassNamesForSettings', CBPagesPreferences::classNamesForSettings()],
            ['CBUsersWhoAreAdministrators', CBViewPageInformationEditor::usersWhoAreAdministrators()],
        ];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
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
