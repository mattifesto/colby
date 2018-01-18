<?php

final class CBPHPAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'php'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {

    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v368.js', cbsysurl())];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBPHPAdmin_iniValues', ini_get_all(null, false)],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUISectionItem4', 'CBUITitleAndDescriptionPart'];
    }
}
