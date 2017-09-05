<?php

final class CBDataStoresAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['develop', 'datastores'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return void
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Data Store Administration');
        CBHTMLOutput::setDescriptionHTML('Information about site data stores.');
    }

    /**
     * @return object
     */
    static function CBAjax_fetchData() {
        $SQL = <<<EOT

            SELECT      `m`.`className` as `className`, LOWER(HEX(`ds`.`ID`)) as `ID`
            FROM        `CBDataStores` AS `ds`
            LEFT JOIN   `CBModels` AS `m`
            ON          `ds`.`ID` = `m`.`ID`
            ORDER BY    `className`, `ID`

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchData_group() {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }
}
