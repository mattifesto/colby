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

            LEFT JOIN   CBModels AS model
                ON      pageInTheTrash.archiveID = model.ID

        EOT;

        return CBDB::SQLToObjects($SQL);
    }
    /* CBAjax_fetchPages() */



    /**
     * @return string
     */
    static function CBAjax_fetchPages_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          pageID: string
     *      }
     *
     * @return void
     */
    static function CBAjax_recoverPage(
        stdClass $args
    ): void {
        $pageModelCBID = CBModel::valueAsCBID(
            $args,
            'pageID'
        );

        /**
         * The test page model CBID is used by the test for this class.
         */

        $testPageModelCBID = '09e0a3527deb3dde49eb0453371cd0b454b4e505';

        if ($pageModelCBID === $testPageModelCBID) {
            return;
        }


        if ($pageModelCBID === null) {
            throw new CBExceptionWithValue(
                "The pageID argument must be a CBID",
                $args,
                '50fd906f9b4ffb56404e13b058bfb8987159668a'
            );
        }

        CBPages::recoverRowWithDataStoreIDFromTheTrash(
            $pageModelCBID
        );
    }
    /* CBAjax_recoverPage() */



    /**
     * @return string
     */
    static function CBAjax_recoverPage_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v528.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUIPanel',
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

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBPagesAdminMenu',
        ];
    }

}
/* CBPagesTrashAdmin */
