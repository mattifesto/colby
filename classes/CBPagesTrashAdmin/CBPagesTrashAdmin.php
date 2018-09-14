<?php

final class CBPagesTrashAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'pages',
            'trash',
        ];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Pages in the Trash Administration';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v381.js', cbsysurl()),
        ];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBPagesAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'trash',
            'text' => 'Trash',
            'URL' => '/admin/?c=CBPagesTrashAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBPagesAdminMenu',
        ];
    }

    /**
     * @return null
     */
    static function CBAjax_fetchPages() {
        $SQL = <<<EOT

            SELECT      LOWER(HEX(trash.archiveID)) AS ID, model.title AS title
            FROM        CBPagesInTheTrash AS trash
            LEFT JOIN   CBModels AS model ON
                        trash.archiveID = model.ID

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchPages_group() {
        return 'Administrators';
    }

    /**
     * @param hex160 $_POST['ID']
     *
     * @return null
     */
    static function recoverPageForAjax() {
        $response = new CBAjaxResponse();

        $ID = $_POST['ID'];

        CBPages::recoverRowWithDataStoreIDFromTheTrash($ID);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function recoverPageForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return [
            'CBUI',
        ];
    }
}
