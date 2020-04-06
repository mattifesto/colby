<?php

final class CBModelsAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'models',
            'directory',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Model Class List';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v595.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $SQL = <<<EOT

            SELECT  DISTINCT className
            FROM    CBModels

        EOT;

        $modelClassNames = CBDB::SQLToArray($SQL);

        return [
            [
                'CBModelsAdmin_modelClassNames',
                $modelClassNames,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUINavigationArrowPart',
            'CBUISectionItem4',
            'CBUITitleAndDescriptionPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
