<?php

final class CBLogAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'log'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::pageInformation()->title = 'Website Log';
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $SQL = <<<EOT

            SELECT DISTINCT sourceClassName
            FROM CBLog

EOT;

        $classNames = array_values(array_filter(
            CBDB::SQLToArray($SQL)
        ));

        return [
            ['CBLogAdminPage_classNames', $classNames],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v456.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
            'CBUI',
            'CBUIExpander',
            'CBUINavigationView',
            'CBUISelector',
        ];
    }
}
