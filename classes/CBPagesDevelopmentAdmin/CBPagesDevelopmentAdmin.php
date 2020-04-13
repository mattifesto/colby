<?php

final class CBPagesDevelopmentAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName() {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'pages',
            'develop',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Pages Development Admimistration';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v599.js', cbsysurl()),
        ];
    }



    /**
     * Get a list of all of the pages. This code is written with the
     * understanding that all pages should have a model. We will warn the
     * administrator of pages that don't have a model.
     *
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $SQL = <<< EOT

            SELECT      LOWER(HEX(ColbyPages.archiveID)) AS ID,
                        CBModels.className AS className,
                        ColbyPages.classNameForKind AS classNameForKind,
                        ColbyPages.published AS published,
                        CBModels.title AS title

            FROM        ColbyPages

            LEFT JOIN   CBModels ON
                        ColbyPages.archiveID = CBModels.ID

            ORDER BY    ISNULL(published),
                        className,
                        classNameForKind,
                        title

        EOT;

        return [
            [
                'CBPagesDevelopmentAdmin_pages',
                CBDB::SQLToObjects($SQL)
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
            'CBUI',
            'CBUIExpander',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBPagesAdminMenu::ID()
        );

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'develop',
            'text' => 'Develop',
            'URL' => '/admin/?c=CBPagesDevelopmentAdmin',
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
