<?php

final class CBPagesTrashAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'pages',
            'trash',
        ];
    }
    /* CBAdmin_menuNamePath() */


    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title =
        'Pages in the Trash Administration';
    }
    /* CBAdmin_render() */


    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBAjax_fetchPages(): array {
        $SQL = <<<EOT

            SELECT      LOWER(HEX(pageInTheTrash.archiveID)) AS ID,
                        model.title AS title
            FROM        CBPagesInTheTrash AS pageInTheTrash
            LEFT JOIN   CBModels AS model ON
                        pageInTheTrash.archiveID = model.ID

EOT;

        return CBDB::SQLToObjects($SQL);
    }
    /* CBAjax_fetchPages() */


    /**
     * @return string
     */
    static function CBAjax_fetchPages_group(): string {
        return 'Administrators';
    }


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v381.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- CBInstall interfaces -- -- -- -- -- */

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
}
/* CBPagesTrashAdmin */
