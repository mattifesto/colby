<?php

final class CBModelsAdmin {

    static $page = '';

    static function CBAdmin_initialize(): void {
        CBModelsAdmin::$page = cb_query_string_value('p', 'classNameList');
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        switch (CBModelsAdmin::$page) {
            case 'modelList':
                return ['models'];

            default:
                return ['models', 'directory'];
        }
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {

    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUINavigationArrowPart', 'CBUISectionItem4',
                'CBUITitleAndDescriptionPart'];
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
        $variables = [
            ['CBModelsAdmin_page', CBModelsAdmin::$page],
        ];

        switch (CBModelsAdmin::$page) {
            case 'modelList':
                $variables[] = ['CBModelsAdmin_modelClassName', cb_query_string_value('modelClassName')];
                $variables[] = ['CBModelsAdmin_modelList', CBModelsAdmin::fetchModelList()];
                break;

            default:
                $modelClassNames = CBDB::SQLToArray('SELECT DISTINCT `className` FROM `CBModels`');
                $variables[] = ['CBModelsAdmin_modelClassNames', $modelClassNames];
                break;
        }

        return $variables;
    }

    /**
     * @return [object]
     */
    private static function fetchModelList() {
        $modelClassNameAsSQL = CBDB::stringToSQL(cb_query_string_value('modelClassName'));
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)) AS `ID`, `title`
            FROM    `CBModels`
            WHERE   `className` = {$modelClassNameAsSQL}

EOT;

        return CBDB::SQLToObjects($SQL);
    }
}
