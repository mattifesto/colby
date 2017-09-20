<?php

final class CBAdminPageForModelList {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['models', 'directory'];
    }

    /**
     * return object
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [object]
     */
    static function fetchModelList() {
        $modelClassNameAsSQL = CBDB::stringToSQL(cb_query_string_value('modelClassName'));
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`)) AS `ID`, `title`
            FROM    `CBModels`
            WHERE   `className` = {$modelClassNameAsSQL}

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', CBSystemURL)];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBAdminPageForModelList_modelClassName', cb_query_string_value('modelClassName')],
            ['CBAdminPageForModelList_modelList', CBAdminPageForModelList::fetchModelList()],
        ];
    }
}
